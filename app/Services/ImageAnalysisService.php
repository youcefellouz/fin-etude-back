<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class ImageAnalysisService
{
    protected $client;
    protected $provider; // 'google', 'aws', 'azure', etc.

    public function __construct()
    {
        $this->client = new Client();
        $this->provider = config('services.image_analysis.provider', 'google');
    }

    /**
     * تحليل الصورة واستخراج الكلمات المفتاحية
     * يدعم عدة خدمات: Google Vision, AWS Rekognition, Azure, إلخ
     */
    public function analyzeImage(UploadedFile $image)
    {
        try {
            $keywords = [];

            switch ($this->provider) {
                case 'google':
                    $keywords = $this->analyzeWithGoogle($image);
                    break;
                case 'aws':
                    $keywords = $this->analyzeWithAWS($image);
                    break;
                case 'azure':
                    $keywords = $this->analyzeWithAzure($image);
                    break;
                default:
                    $keywords = $this->analyzeWithGoogle($image);
            }

            return $keywords;

        } catch (\Exception $e) {
            \Log::error('Image Analysis Service Error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * تحليل الصورة باستخدام Google Vision API
     */
    private function analyzeWithGoogle(UploadedFile $image)
    {
        try {
            $apiKey = config('services.google.vision_api_key');
            
            if (!$apiKey) {
                \Log::warning('Google Vision API key not configured');
                return [];
            }

            // قراءة محتوى الصورة وتحويلها إلى Base64
            $imageData = base64_encode(file_get_contents($image->getRealPath()));

            $response = $this->client->post(
                'https://vision.googleapis.com/v1/images:annotate?key=' . $apiKey,
                [
                    'json' => [
                        'requests' => [
                            [
                                'image' => ['content' => $imageData],
                                'features' => [
                                    ['type' => 'LABEL_DETECTION', 'maxResults' => 10],
                                    ['type' => 'OBJECT_LOCALIZATION', 'maxResults' => 10],
                                    ['type' => 'TEXT_DETECTION'],
                                ],
                            ],
                        ],
                    ],
                ]
            );

            $body = json_decode($response->getBody(), true);
            
            return $this->extractKeywordsFromGoogleResponse($body);

        } catch (RequestException $e) {
            \Log::error('Google Vision API Error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * تحليل الصورة باستخدام AWS Rekognition
     */
    private function analyzeWithAWS(UploadedFile $image)
    {
        try {
            // يتطلب AWS SDK
            // composer require aws/aws-sdk-php
            
            $accessKey = config('services.aws.access_key_id');
            $secretKey = config('services.aws.secret_access_key');
            $region = config('services.aws.region', 'us-east-1');

            if (!$accessKey || !$secretKey) {
                \Log::warning('AWS Credentials not configured');
                return [];
            }

            $imageData = base64_encode(file_get_contents($image->getRealPath()));

            // إرسال الصورة إلى AWS Rekognition
            $response = $this->client->post(
                'https://rekognition.' . $region . '.amazonaws.com/',
                [
                    'headers' => [
                        'X-Amz-Target' => 'RekognitionService.DetectLabels',
                        'Content-Type' => 'application/x-amz-json-1.1',
                    ],
                    'json' => [
                        'Image' => ['Bytes' => $imageData],
                        'MaxLabels' => 10,
                        'MinConfidence' => 70,
                    ],
                ]
            );

            $body = json_decode($response->getBody(), true);
            
            return $this->extractKeywordsFromAWSResponse($body);

        } catch (RequestException $e) {
            \Log::error('AWS Rekognition Error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * تحليل الصورة باستخدام Azure Computer Vision
     */
    private function analyzeWithAzure(UploadedFile $image)
    {
        try {
            $apiKey = config('services.azure.vision_api_key');
            $endpoint = config('services.azure.vision_endpoint');

            if (!$apiKey || !$endpoint) {
                \Log::warning('Azure Vision credentials not configured');
                return [];
            }

            $response = $this->client->post(
                $endpoint . '/analyze?api-version=2024-02-01',
                [
                    'headers' => [
                        'Ocp-Apim-Subscription-Key' => $apiKey,
                    ],
                    'multipart' => [
                        [
                            'name' => 'image',
                            'contents' => fopen($image->getRealPath(), 'r'),
                        ],
                    ],
                    'query' => [
                        'features' => 'tags,objects,read',
                        'model-version' => 'latest',
                    ],
                ]
            );

            $body = json_decode($response->getBody(), true);
            
            return $this->extractKeywordsFromAzureResponse($body);

        } catch (RequestException $e) {
            \Log::error('Azure Vision Error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * استخراج الكلمات المفتاحية من رد Google Vision
     */
    private function extractKeywordsFromGoogleResponse($response)
    {
        $keywords = [];

        if (!isset($response['responses'][0])) {
            return $keywords;
        }

        $annotations = $response['responses'][0];

        // استخراج من Label Detection
        if (isset($annotations['labelAnnotations'])) {
            foreach ($annotations['labelAnnotations'] as $label) {
                if (isset($label['description']) && $label['score'] >= 0.5) {
                    $keywords[] = strtolower($label['description']);
                }
            }
        }

        // استخراج من Object Localization
        if (isset($annotations['localizedObjectAnnotations'])) {
            foreach ($annotations['localizedObjectAnnotations'] as $object) {
                if (isset($object['name']) && $object['score'] >= 0.5) {
                    $keywords[] = strtolower($object['name']);
                }
            }
        }

        // استخراج من Text Detection
        if (isset($annotations['textAnnotations']) && count($annotations['textAnnotations']) > 1) {
            // الأول هو النص الكامل، الباقي تفاصيل
            $fullText = $annotations['textAnnotations'][0]['description'];
            $words = preg_split('/\s+/', strtolower($fullText), -1, PREG_SPLIT_NO_EMPTY);
            // إضافة كلمات بطول معقول فقط
            foreach ($words as $word) {
                if (strlen($word) > 3 && strlen($word) < 30) {
                    $keywords[] = $word;
                }
            }
        }

        return array_unique(array_slice($keywords, 0, 15));
    }

    /**
     * استخراج الكلمات المفتاحية من رد AWS Rekognition
     */
    private function extractKeywordsFromAWSResponse($response)
    {
        $keywords = [];

        if (isset($response['Labels'])) {
            foreach ($response['Labels'] as $label) {
                if (isset($label['Name']) && $label['Confidence'] >= 70) {
                    $keywords[] = strtolower($label['Name']);
                }
            }
        }

        return array_unique($keywords);
    }

    /**
     * استخراج الكلمات المفتاحية من رد Azure Computer Vision
     */
    private function extractKeywordsFromAzureResponse($response)
    {
        $keywords = [];

        // استخراج من Tags
        if (isset($response['tags'])) {
            foreach ($response['tags'] as $tag) {
                if (isset($tag['name']) && $tag['confidence'] >= 0.5) {
                    $keywords[] = strtolower($tag['name']);
                }
            }
        }

        // استخراج من Objects
        if (isset($response['objects'])) {
            foreach ($response['objects'] as $object) {
                if (isset($object['objectProperty']) && $object['confidence'] >= 0.5) {
                    $keywords[] = strtolower($object['objectProperty']);
                }
            }
        }

        return array_unique(array_slice($keywords, 0, 15));
    }

    /**
     * توليد كلمات مفتاحية مخصصة (للاختبار بدون API)
     */
    public function generateMockKeywords(UploadedFile $image)
    {
        // كلمات مفتاحية افتراضية للاختبار
        return [
            'product',
            'item',
            'merchandise',
            'merchandise',
            'goods',
        ];
    }
}
