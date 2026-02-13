<?php

namespace App\Services;

use App\Models\Article;
use Illuminate\Database\Eloquent\Collection;

class SearchService
{
    /**
     * البحث النصي البسيط والمتقدم
     * يبحث عن الكلمات المفتاحية في:
     * - الاسم (name)
     * - الوصف (description)
     * - الفئة (category)
     * - العلامة التجارية (brand)
     */
    public function textSearch($query, $limit = 15, $offset = 0)
    {
        $searchTerm = "%{$query}%";

        $articles = Article::where('name', 'like', $searchTerm)
            ->orWhere('description', 'like', $searchTerm)
            ->with(['category', 'brand'])
            ->skip($offset)
            ->take($limit)
            ->get();

        return $articles->map(function ($article) {
            return $this->formatArticleResponse($article);
        })->toArray();
    }

    /**
     * البحث باستخدام كلمات مفتاحية مستخرجة من الصورة
     */
    public function imageKeywordSearch($keywords, $limit = 15, $offset = 0)
    {
        if (empty($keywords)) {
            return [];
        }

        $query = Article::query();

        // البحث عن أي من الكلمات المفتاحية
        $query->where(function ($q) use ($keywords) {
            foreach ($keywords as $keyword) {
                $searchTerm = "%{$keyword}%";
                $q->orWhere('name', 'like', $searchTerm)
                  ->orWhere('description', 'like', $searchTerm);
            }
        });

        $articles = $query->with(['category', 'brand'])
            ->skip($offset)
            ->take($limit)
            ->get();

        return $articles->map(function ($article) {
            return $this->formatArticleResponse($article);
        })->toArray();
    }

    /**
     * البحث المتقدم مع الفلاتر
     */
    public function advancedSearch($filters, $limit = 15, $offset = 0)
    {
        $query = Article::query();

        // البحث النصي
        if (isset($filters['query']) && !empty($filters['query'])) {
            $searchTerm = "%{$filters['query']}%";
            $query->where('name', 'like', $searchTerm)
                  ->orWhere('description', 'like', $searchTerm);
        }

        // فلتر حسب الفئة
        if (isset($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        // فلتر حسب العلامة التجارية
        if (isset($filters['brand_id'])) {
            $query->where('brand_id', $filters['brand_id']);
        }

        // فلتر حسب نطاق السعر
        if (isset($filters['min_price'])) {
            $query->where('price', '>=', $filters['min_price']);
        }

        if (isset($filters['max_price'])) {
            $query->where('price', '<=', $filters['max_price']);
        }

        // الترتيب
        $orderBy = $filters['sort_by'] ?? 'created_at';
        $orderDirection = $filters['sort_direction'] ?? 'desc';
        $query->orderBy($orderBy, $orderDirection);

        $articles = $query->with(['category', 'brand'])
            ->skip($offset)
            ->take($limit)
            ->get();

        return $articles->map(function ($article) {
            return $this->formatArticleResponse($article);
        })->toArray();
    }

    /**
     * صيغة موحدة لردود المقالات
     */
    private function formatArticleResponse($article)
    {
        return [
            'id' => $article->id,
            'name' => $article->name,
            'description' => $article->description,
            'price' => $article->price,
            'price_after_discount' => $article->price_after_discount ?? $article->price,
            'image_url' => $article->image_url,
            'category' => $article->category ? [
                'id' => $article->category->id,
                'name' => $article->category->name ?? 'N/A',
            ] : null,
            'brand' => $article->brand ? [
                'id' => $article->brand->id,
                'name' => $article->brand->name ?? 'N/A',
            ] : null,
            'created_at' => $article->created_at,
            'updated_at' => $article->updated_at,
        ];
    }
}
