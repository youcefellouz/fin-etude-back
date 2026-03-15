<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Article;
use App\Models\Station;
use App\Models\Order;
use App\Models\Discount;
use App\Models\Stock;
use App\Models\OrderStationStock;
use App\Models\CustomerAnalytics;
use App\Models\ProductAnalytics;
use App\Models\UserActivityLog;
use App\Models\RecommendationLog;
use App\Models\Review;
use App\Models\RepairRequest;

class AnalyticsTestDataSeeder extends Seeder
{
    public function run()
    {
        $this->command->info('🚀 بدء إنشاء البيانات التجريبية...');

        // ─── 1. CATEGORIES ────────────────────────────────────────────────────
        $categories = [
    'Laptops & Ordinateurs',
    'Smartphones & Tablettes',
    'Écrans & Moniteurs',
    'Composants PC',
    'Périphériques',
    'Réseaux & Connectivité',
    'Stockage & Mémoire',
    'Imprimantes & Scanners',
    'Audio & Son',
    'Accessoires',
];
        foreach ($categories as $cat) {
            if (!Category::where('name', $cat)->exists()) {
                Category::create(['name' => $cat]);
            }
        }
        $this->command->info('✅ تم إنشاء الفئات');

        // ─── 2. BRANDS ───────────────────────────────────────────────────────
        $brands = ['Samsung', 'Apple','Honor', 'Adidas', 'Sony', 'LG', 'HP', 'Dell'];
        foreach ($brands as $brand) {
            if (!Brand::where('name', $brand)->exists()) {
                Brand::create(['name' => $brand]);
            }
        }
        $this->command->info('✅ تم إنشاء العلامات التجارية');

        // ─── 3. STATIONS ─────────────────────────────────────────────────────
        $stations = [
            ['name' => 'Station Tunis',  'location' => 'Avenue Habib Bourguiba', 'city' => 'tunis',  'type' => 'commerciale', 'status' => 'active'],
            ['name' => 'Station Sfax',   'location' => 'Centre Ville',           'city' => 'sfax',   'type' => 'commerciale', 'status' => 'active'],
            ['name' => 'Station Sousse', 'location' => 'Port El Kantaoui',       'city' => 'sousse', 'type' => 'technique',   'status' => 'active'],
        ];
        foreach ($stations as $station) {
            if (!Station::where('name', $station['name'])->exists()) {
                Station::create($station);
            }
        }
        $this->command->info('✅ تم إنشاء المحطات');

        // ─── 4. USERS ────────────────────────────────────────────────────────
        $this->command->info('📝 إنشاء مستخدمين...');
        for ($i = 1; $i <= 50; $i++) {
            if (!User::where('email', "testuser$i@example.com")->exists()) {
                User::create([
                    'name'     => "Test User $i",
                    'email'    => "testuser$i@example.com",
                    'password' => bcrypt('password123'),
                    'role'     => 'client',   // ✅ إضافة role
                ]);
            }
        }
        $this->command->info('✅ تم إنشاء 50 مستخدم');

        // ─── 5. ARTICLES + STOCKS ────────────────────────────────────────────
        $this->command->info('📦 إنشاء منتجات وتوزيع المخزون...');
        $categoryIds   = Category::pluck('id')->toArray();
        $brandIds      = Brand::pluck('id')->toArray();
        $allStationIds = Station::pluck('id')->toArray();

        for ($i = 1; $i <= 100; $i++) {
            $article = Article::create([
                'name'        => "Product Test $i",
                'description' => "This is a test product number $i with detailed description",
                'price'       => rand(20, 800),
                'category_id' => $categoryIds[array_rand($categoryIds)],
                'brand_id'    => $brandIds[array_rand($brandIds)],
            ]);

            // إضافة مخزون في كل المحطات
            foreach ($allStationIds as $stationId) {
                $article->stations()->attach($stationId, [
                    'quantity' => rand(50, 200),
                ]);
            }
        }
        $this->command->info('✅ تم إنشاء 100 منتج مع مخزون في كل المحطات');

        // ─── 6. DISCOUNTS ────────────────────────────────────────────────────
        $this->command->info('💰 إنشاء خصومات...');
        for ($i = 1; $i <= 15; $i++) {
            $discount = Discount::create([
                'type'       => 'percentage',
                'value'      => rand(5, 40),
                'start_date' => now()->subDays(rand(1, 20)),
                'end_date'   => now()->addDays(rand(10, 60)),
            ]);
            $articleIds = Article::inRandomOrder()->limit(rand(5, 15))->pluck('id');
            $discount->articles()->attach($articleIds);
        }
        $this->command->info('✅ تم إنشاء 15 خصم');

        // ─── 7. ORDERS ───────────────────────────────────────────────────────
        $this->command->info('🛒 إنشاء طلبات...');
        $userIds    = User::where('role', 'client')->pluck('id')->toArray();
        $articleIds = Article::pluck('id')->toArray();
        $statuses   = ['pending', 'confirmed', 'cancelled'];

        for ($i = 1; $i <= 200; $i++) {
            $userId    = $userIds[array_rand($userIds)];
            $stationId = $allStationIds[array_rand($allStationIds)];

            $cities = ['tunis','sousse','sfax','bizerte','gabes','monastir','nabeul','kairouan','ariana','beja'];
            $order = Order::create([
                'user_id'        => $userId,
                'status'         => $statuses[array_rand($statuses)],
                'global_price'   => 0,
                'city'           => $cities[array_rand($cities)],
                'address'        => rand(1, 99) . ' Rue Test, Quartier ' . rand(1, 10),
                'payment_method' => ['cash', 'card'][rand(0, 1)],
                'created_at'     => now()->subDays(rand(0, 90)),
                'updated_at'     => now()->subDays(rand(0, 90)),
            ]);

            $numArticles = rand(1, 6);
            $orderTotal  = 0;

            for ($j = 0; $j < $numArticles; $j++) {
                $article   = Article::find($articleIds[array_rand($articleIds)]);
                $quantity  = rand(1, 4);
                $unitPrice = $article->price;

                // ربط المنتج بالطلب
                $order->articles()->attach($article->id, [
                    'quantity'   => $quantity,
                    'unit_price' => $unitPrice,
                ]);

                // ✅ إنقاص المخزون بدون نزول تحت الصفر
                Stock::where('article_id', $article->id)
                    ->where('station_id', $stationId)
                    ->where('quantity', '>=', $quantity)
                    ->decrement('quantity', $quantity);

                // تسجيل توزيع الطلب على المحطة
                OrderStationStock::create([
                    'order_id'   => $order->id,
                    'article_id' => $article->id,
                    'station_id' => $stationId,
                    'quantity'   => $quantity,
                ]);

                $orderTotal += $quantity * $unitPrice;
            }

            $order->update(['global_price' => $orderTotal]);
        }
        $this->command->info('✅ تم إنشاء 200 طلب');

        // ─── 8. USER ACTIVITY LOGS (لتتبع المشاهدات للـ AI) ─────────────────
        $this->command->info('👁️ إنشاء سجلات نشاط المستخدمين...');
        $articleIdsList = Article::pluck('id')->toArray();
        $activityTypes  = ['product_view', 'product_view', 'product_view', 'search', 'cart_add'];

        foreach ($userIds as $userId) {
            $numActivities = rand(5, 20);
            for ($k = 0; $k < $numActivities; $k++) {
                $articleId    = $articleIdsList[array_rand($articleIdsList)];
                $activityType = $activityTypes[array_rand($activityTypes)];
                $article      = Article::find($articleId);

                UserActivityLog::create([
                    'user_id'       => $userId,
                    'session_id'    => 'seed_session_' . $userId,
                    'ip_address'    => '127.0.0.1',
                    'activity_type' => $activityType,
                    'entity_type'   => 'article',
                    'entity_id'     => $articleId,
                    'device_type'   => ['desktop', 'mobile', 'tablet'][rand(0, 2)],
                    'metadata'      => json_encode([
                        'article_name' => $article->name,
                        'price'        => $article->price,
                        'category_id'  => $article->category_id,
                    ]),
                    'created_at' => now()->subDays(rand(0, 90)),
                ]);

                // ✅ زيادة times_viewed في ProductAnalytics
                if ($activityType === 'product_view') {
                    ProductAnalytics::firstOrCreate(['article_id' => $articleId]);
                    ProductAnalytics::where('article_id', $articleId)
                        ->increment('times_viewed');
                }
            }
        }
        $this->command->info('✅ تم إنشاء سجلات النشاط وتحديث times_viewed');

        // ─── 9. PRODUCT ANALYTICS ────────────────────────────────────────────
        $this->command->info('📊 إنشاء تحليلات المنتجات...');
        $articles = Article::all();
        foreach ($articles as $article) {
            $analytics = ProductAnalytics::firstOrCreate(['article_id' => $article->id]);
            $analytics->updateAnalytics();
            $analytics->predictFutureSales();
        }
        $this->command->info('✅ تم إنشاء تحليلات المنتجات');

        // ─── 10. CUSTOMER ANALYTICS ──────────────────────────────────────────
        $this->command->info('👥 إنشاء تحليلات العملاء...');
        $usersWithOrders = User::has('orders')->get();
        foreach ($usersWithOrders as $user) {
            $analytics = CustomerAnalytics::firstOrCreate(['user_id' => $user->id]);
            $analytics->updateAnalytics();
        }
        $this->command->info('✅ تم إنشاء تحليلات العملاء');

        // ─── 11. RECOMMENDATION LOGS ─────────────────────────────────────────
        $this->command->info('🎯 إنشاء سجلات التوصيات...');
        $sampleUsers    = array_slice($userIds, 0, 20);
        $algoTypes      = ['personalized', 'frequently_bought_together', 'similar_products', 'trending'];

        foreach ($sampleUsers as $userId) {
            $sourceArticle = Article::find($articleIdsList[array_rand($articleIdsList)]);
            $recommended   = Article::inRandomOrder()->limit(5)->get();

            $log = RecommendationLog::create([
                'user_id'              => $userId,
                'session_id'           => 'seed_reco_' . $userId,
                'source_article_id'    => $sourceArticle->id,
                'recommended_articles' => $recommended->map(fn($a) => [
                    'id'    => $a->id,
                    'name'  => $a->name,
                    'price' => $a->price,
                ])->toArray(),
                'recommendation_type' => $algoTypes[array_rand($algoTypes)],
                'algorithm_used'      => 'hybrid_collaborative_filtering',
                'was_clicked'         => (bool) rand(0, 1),
                'was_purchased'       => (bool) rand(0, 3) === 0,
                'created_at'          => now()->subDays(rand(0, 30)),
            ]);

            // ربط الطلب إذا تم الشراء
            if ($log->was_purchased) {
                $order = Order::where('user_id', $userId)
                    ->where('status', 'confirmed')
                    ->inRandomOrder()
                    ->first();
                if ($order) {
                    $log->update(['resulting_order_id' => $order->id]);
                }
            }
        }
        $this->command->info('✅ تم إنشاء سجلات التوصيات');

        // ─── 12. REVIEWS ─────────────────────────────────────────────────────
        $this->command->info('⭐ إنشاء تقييمات المنتجات...');
        $comments = [
            'Excellent produit, je recommande !',
            'Très bonne qualité, livraison rapide.',
            'Produit conforme à la description.',
            'Bon rapport qualité/prix.',
            'Satisfait de mon achat.',
            'Produit correct mais emballage abîmé.',
            'Déçu par la qualité, pas à la hauteur.',
            'Parfait, exactement ce que je cherchais !',
            'Bonne qualité, mais un peu cher.',
            'Je suis très content de cet achat.',
        ];

        // chaque user note entre 1 et 5 produits qu'il a commandés
        $usersWithOrders = User::has('orders')->get();
        foreach ($usersWithOrders as $user) {
            $purchasedArticleIds = $user->orders()
                ->where('status', 'confirmed')
                ->with('articles')
                ->get()
                ->pluck('articles')
                ->flatten()
                ->pluck('id')
                ->unique()
                ->values()
                ->toArray();

            if (empty($purchasedArticleIds)) continue;

            $toReview = array_slice($purchasedArticleIds, 0, rand(1, min(5, count($purchasedArticleIds))));

            foreach ($toReview as $articleId) {
                // unique constraint: un user ne peut noter un article qu'une fois
                if (Review::where('user_id', $user->id)->where('article_id', $articleId)->exists()) continue;

                Review::create([
                    'user_id'    => $user->id,
                    'article_id' => $articleId,
                    'rating'     => rand(1, 5),
                    'comment'    => rand(0, 1) ? $comments[array_rand($comments)] : null,
                    'created_at' => now()->subDays(rand(0, 60)),
                    'updated_at' => now()->subDays(rand(0, 60)),
                ]);
            }
        }
        $this->command->info('✅ تم إنشاء التقييمات');

        // ─── 13. REPAIR REQUESTS ─────────────────────────────────────────────
        $this->command->info('🔧 إنشاء طلبات الإصلاح...');
        $techniqueStations = Station::where('type', 'technique')->pluck('id')->toArray();

        // fallback إذا لا توجد محطة تقنية
        if (empty($techniqueStations)) {
            $techniqueStations = Station::pluck('id')->toArray();
        }

        $repairStatuses = ['en_attente', 'en_cours', 'termine', 'refuse'];
        $repairDescriptions = [
            'Écran cassé, besoin de remplacement.',
            'Batterie ne charge plus.',
            'Problème de clavier, touches bloquées.',
            'Surchauffe anormale du processeur.',
            'Port USB défaillant.',
            'Problème de connexion WiFi.',
            'Son ne fonctionne plus.',
            'Ventilateur fait du bruit.',
            'Problème au démarrage.',
            'Écran qui scintille.',
        ];

        $sampleUsersForRepair = array_slice($userIds, 0, 30);
        foreach ($sampleUsersForRepair as $userId) {
            $numRepairs = rand(0, 3);
            for ($r = 0; $r < $numRepairs; $r++) {
                $status      = $repairStatuses[array_rand($repairStatuses)];
                $stationId   = $techniqueStations[array_rand($techniqueStations)];
                $articleId   = $articleIdsList[array_rand($articleIdsList)];
                $appointDate = now()->subDays(rand(0, 60));

                RepairRequest::create([
                    'user_id'          => $userId,
                    'station_id'       => $stationId,
                    'article_id'       => $articleId,
                    'description'      => $repairDescriptions[array_rand($repairDescriptions)],
                    'status'           => $status,
                    'warranty'         => (bool) rand(0, 1),
                    'estimated_cost'   => in_array($status, ['en_cours', 'termine']) ? rand(30, 300) : null,
                    'appointment_date' => $appointDate,
                    'technician_note'  => $status === 'termine'
                        ? 'Réparation effectuée avec succès.'
                        : ($status === 'refuse' ? 'Pièce introuvable.' : null),
                    'created_at' => $appointDate,
                    'updated_at' => $appointDate,
                ]);
            }
        }
        $this->command->info('✅ تم إنشاء طلبات الإصلاح');

        $this->command->info('');
        $this->command->info('🎉 اكتملت جميع البيانات التجريبية بنجاح!');
        $this->command->info('');
        $this->command->info('📋 ملخص البيانات المنشأة:');
        $this->command->info('   - ' . Category::count()         . ' فئة');
        $this->command->info('   - ' . Brand::count()            . ' علامة تجارية');
        $this->command->info('   - ' . Station::count()          . ' محطة');
        $this->command->info('   - ' . User::where('role', 'client')->count() . ' مستخدم');
        $this->command->info('   - ' . Article::count()          . ' منتج');
        $this->command->info('   - ' . Order::count()            . ' طلب');
        $this->command->info('   - ' . CustomerAnalytics::count(). ' تحليل عميل');
        $this->command->info('   - ' . ProductAnalytics::count() . ' تحليل منتج');
        $this->command->info('   - ' . UserActivityLog::count()  . ' سجل نشاط');
        $this->command->info('   - ' . RecommendationLog::count(). ' سجل توصية');
        $this->command->info('   - ' . Review::count()           . ' تقييم');
        $this->command->info('   - ' . RepairRequest::count()    . ' طلب إصلاح');
    }
}