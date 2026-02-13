<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Article;
use App\Services\SearchService;
use App\Services\ImageAnalysisService;

class SearchController extends Controller
{
    protected $searchService;
    protected $imageAnalysisService;

    public function __construct(SearchService $searchService, ImageAnalysisService $imageAnalysisService)
    {
        $this->searchService = $searchService;
        $this->imageAnalysisService = $imageAnalysisService;
    }

    /**
     * Unified Search Endpoint
     * يدعم ثلاث أنواع من البحث:
     * 1. Text Search فقط
     * 2. Image Search فقط
     * 3. Hybrid Search (Text + Image)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function unified(Request $request)
    {
        try {
            $request->validate([
                'query' => 'nullable|string|max:255',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
                'limit' => 'nullable|integer|min:1|max:100',
                'offset' => 'nullable|integer|min:0',
                'text_weight' => 'nullable|numeric|min:0|max:1',
                'image_weight' => 'nullable|numeric|min:0|max:1',
            ]);

            $query = $request->input('query');
            $image = $request->file('image');
            $limit = $request->input('limit', 15);
            $offset = $request->input('offset', 0);
            $textWeight = (float) $request->input('text_weight', 0.5);
            $imageWeight = (float) $request->input('image_weight', 0.5);

            // التحقق من أن لدينا على الأقل واحد من النص أو الصورة
            if (!$query && !$image) {
                return response()->json([
                    'error' => 'يجب تقديم نص (query) أو صورة (image) على الأقل',
                    'status' => 'failed'
                ], 400);
            }

            // التحقق من أن مجموع الأوزان = 1
            $totalWeight = $textWeight + $imageWeight;
            if ($totalWeight == 0) {
                return response()->json([
                    'error' => 'مجموع الأوزان يجب أن يكون أكبر من 0',
                    'status' => 'failed'
                ], 400);
            }

            // تطبيع الأوزان
            $textWeight = $query ? ($textWeight / $totalWeight) : 0;
            $imageWeight = $image ? ($imageWeight / $totalWeight) : 0;

            $results = [];

            // حالة 1: Text Search فقط
            if ($query && !$image) {
                $results = $this->searchService->textSearch($query, $limit, $offset);
                $searchType = 'text';
            }
            // حالة 2: Image Search فقط
            elseif ($image && !$query) {
                $results = $this->imageSearch($image, $limit, $offset);
                $searchType = 'image';
            }
            // حالة 3: Hybrid Search (Text + Image)
            else {
                $results = $this->hybridSearch($query, $image, $textWeight, $imageWeight, $limit, $offset);
                $searchType = 'hybrid';
            }

            return response()->json([
                'status' => 'success',
                'search_type' => $searchType,
                'count' => count($results),
                'limit' => $limit,
                'offset' => $offset,
                'data' => $results,
                'weights' => [
                    'text' => $textWeight,
                    'image' => $imageWeight,
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'خطأ في البحث',
                'message' => $e->getMessage(),
                'status' => 'failed'
            ], 500);
        }
    }

    /**
     * Search using Image only
     */
    private function imageSearch($image, $limit, $offset)
    {
        try {
            // تحليل الصورة للحصول على الكلمات المفتاحية
            $keywords = $this->imageAnalysisService->analyzeImage($image);

            if (empty($keywords)) {
                return [];
            }

            // البحث في قاعدة البيانات باستخدام الكلمات المفتاحية المستخرجة
            return $this->searchService->imageKeywordSearch($keywords, $limit, $offset);

        } catch (\Exception $e) {
            \Log::error('Image Analysis Error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Hybrid Search - دمج نتائج البحث النصي مع البحث بالصورة
     */
    private function hybridSearch($query, $image, $textWeight, $imageWeight, $limit, $offset)
    {
        try {
            // البحث النصي
            $textResults = $this->searchService->textSearch($query, $limit * 2, 0);

            // البحث بالصورة
            $keywords = $this->imageAnalysisService->analyzeImage($image);
            $imageResults = $this->searchService->imageKeywordSearch($keywords, $limit * 2, 0);

            // دمج النتائج مع حساب النقاط
            $mergedResults = $this->mergeAndScoreResults(
                $textResults,
                $imageResults,
                $textWeight,
                $imageWeight
            );

            // ترتيب حسب النقاط
            usort($mergedResults, function ($a, $b) {
                return $b['hybrid_score'] <=> $a['hybrid_score'];
            });

            // تطبيق الـ Pagination
            $mergedResults = array_slice($mergedResults, $offset, $limit);

            return $mergedResults;

        } catch (\Exception $e) {
            \Log::error('Hybrid Search Error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * دمج نتائج البحث النصي والصور مع حساب النقاط
     */
    private function mergeAndScoreResults($textResults, $imageResults, $textWeight, $imageWeight)
    {
        $scoreMap = [];

        // إضافة نقاط البحث النصي
        foreach ($textResults as $index => $result) {
            $articleId = $result['id'];
            if (!isset($scoreMap[$articleId])) {
                $scoreMap[$articleId] = [
                    'article' => $result,
                    'text_score' => 0,
                    'image_score' => 0,
                    'text_rank' => PHP_INT_MAX,
                    'image_rank' => PHP_INT_MAX,
                ];
            }
            $scoreMap[$articleId]['text_rank'] = $index;
        }

        // إضافة نقاط البحث بالصورة
        foreach ($imageResults as $index => $result) {
            $articleId = $result['id'];
            if (!isset($scoreMap[$articleId])) {
                $scoreMap[$articleId] = [
                    'article' => $result,
                    'text_score' => 0,
                    'image_score' => 0,
                    'text_rank' => PHP_INT_MAX,
                    'image_rank' => PHP_INT_MAX,
                ];
            }
            $scoreMap[$articleId]['image_rank'] = $index;
        }

        // حساب النقاط النهائية
        $results = [];
        foreach ($scoreMap as $articleId => $data) {
            $textScore = $data['text_rank'] !== PHP_INT_MAX ? (1 / (1 + $data['text_rank'])) : 0;
            $imageScore = $data['image_rank'] !== PHP_INT_MAX ? (1 / (1 + $data['image_rank'])) : 0;

            $hybridScore = ($textScore * $textWeight) + ($imageScore * $imageWeight);

            $results[] = array_merge($data['article'], [
                'text_score' => round($textScore, 3),
                'image_score' => round($imageScore, 3),
                'hybrid_score' => round($hybridScore, 3),
            ]);
        }

        return $results;
    }
}
