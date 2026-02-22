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

class AnalyticsTestDataSeeder extends Seeder
{
    public function run()
    {
        $this->command->info('🚀 بدء إنشاء البيانات التجريبية...');

        // إنشاء فئات إضافية
        $categories = ['Electronics', 'Clothing', 'Books', 'Home & Garden', 'Sports', 'Toys', 'Beauty'];
        
        foreach ($categories as $cat) {
            if (!Category::where('name', $cat)->exists()) {
                Category::create(['name' => $cat]);
            }
        }
        $this->command->info('✅ تم إنشاء الفئات');

        // إنشاء علامات تجارية
        $brands = ['Samsung', 'Apple', 'Nike', 'Adidas', 'Sony', 'LG', 'HP', 'Dell'];
        
        foreach ($brands as $brand) {
            if (!Brand::where('name', $brand)->exists()) {
                Brand::create(['name' => $brand]);
            }
        }
        $this->command->info('✅ تم إنشاء العلامات التجارية');

        // إنشاء محطات
        // تصحيح القيم لتتوافق مع الـ enum في قاعدة البيانات
        $stations = [
            ['name' => 'Station Tunis', 'location' => 'Avenue Habib Bourguiba', 'city' => 'tunis', 'type' => 'commerciale', 'status' => 'active'],
            ['name' => 'Station Sfax', 'location' => 'Centre Ville', 'city' => 'sfax', 'type' => 'commerciale', 'status' => 'active'],
            ['name' => 'Station Sousse', 'location' => 'Port El Kantaoui', 'city' => 'sousse', 'type' => 'technique', 'status' => 'active'],
        ];
        
        foreach ($stations as $station) {
            if (!Station::where('name', $station['name'])->exists()) {
                Station::create($station);
            }
        }
        $this->command->info('✅ تم إنشاء المحطات');

        // إنشاء مستخدمين
        $this->command->info('📝 إنشاء مستخدمين...');
        for ($i = 1; $i <= 50; $i++) {
            if (!User::where('email', "testuser$i@example.com")->exists()) {
                User::create([
                    'name' => "Test User $i",
                    'email' => "testuser$i@example.com",
                    'password' => bcrypt('password123'),
                ]);
            }
        }
        $this->command->info('✅ تم إنشاء 50 مستخدم');

        // إنشاء منتجات وتوزيع المخزون
        $this->command->info('📦 إنشاء منتجات وتوزيع المخزون...');
        $categoryIds = Category::pluck('id')->toArray();
        $brandIds = Brand::pluck('id')->toArray();
        $allStationIds = Station::pluck('id')->toArray();

        for ($i = 1; $i <= 100; $i++) {
            $article = Article::create([
                'name' => "Product Test $i",
                'description' => "This is a test product number $i with detailed description",
                'price' => rand(20, 800),
                'category_id' => $categoryIds[array_rand($categoryIds)],
                'brand_id' => $brandIds[array_rand($brandIds)],
            ]);

            // إضافة مخزون للمنتج في جميع المحطات لضمان توفره للطلبات
            foreach ($allStationIds as $stationId) {
                $article->stations()->attach(
                    $stationId, 
                    ['quantity' => rand(50, 200)] // كمية كافية لتجنب أخطاء المخزون أثناء الـ seeding
                );
            }
        }
        $this->command->info('✅ تم إنشاء 100 منتج مع مخزون في كل المحطات');

        // إنشاء خصومات
        $this->command->info('💰 إنشاء خصومات...');
        for ($i = 1; $i <= 15; $i++) {
            $discount = Discount::create([
                'type' => 'percentage',
                'value' => rand(5, 40),
                'start_date' => now()->subDays(rand(1, 20)),
                'end_date' => now()->addDays(rand(10, 60)),
            ]);

            $articleIds = Article::inRandomOrder()->limit(rand(5, 15))->pluck('id');
            $discount->articles()->attach($articleIds);
        }
        $this->command->info('✅ تم إنشاء 15 خصم');

        // إنشاء طلبات متنوعة
        $this->command->info('🛒 إنشاء طلبات...');
        $userIds = User::pluck('id')->toArray();
        $articleIds = Article::pluck('id')->toArray();
        $statuses = ['pending', 'confirmed', 'cancelled'];

        for ($i = 1; $i <= 200; $i++) {
            $user = User::find($userIds[array_rand($userIds)]);
            // اختيار محطة عشوائية للطلب
            $stationId = $allStationIds[array_rand($allStationIds)];
            
            $order = Order::create([
                'user_id' => $user->id,
                'station_id' => $stationId, // ربط الطلب بمحطة
                'status' => $statuses[array_rand($statuses)],
                'global_price' => 0,
                'created_at' => now()->subDays(rand(0, 90)),
            ]);

            // إضافة منتجات للطلب وإنقاص المخزون يدوياً (لأن الـ Seeder قد لا يمر عبر الـ Controller)
            $numArticles = rand(1, 6);
            $orderTotal = 0;

            for ($j = 0; $j < $numArticles; $j++) {
                $article = Article::find($articleIds[array_rand($articleIds)]);
                $quantity = rand(1, 4);
                
                // حساب السعر (افتراضاً بدون خصم للتبسيط أو يمكن استخدام price_after_discount)
                // هنا نستخدم السعر الأساسي للسرعة، أو يمكن جلب السعر بعد الخصم إذا كان الموديل يدعمه
                $unitPrice = $article->price; 

                $order->articles()->attach($article->id, [
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                ]);

                // إنقاص المخزون يدوياً لمحاكاة العملية الحقيقية
                // ملاحظة: في الـ Seeder لا نستخدم الـ Controller، لذا نعدل المخزون مباشرة
                $article->stations()
                        ->wherePivot('station_id', $stationId)
                        ->decrement('quantity', $quantity);

                $orderTotal += $quantity * $unitPrice;
            }

            $order->update(['global_price' => $orderTotal]);
        }
        $this->command->info('✅ تم إنشاء 200 طلب مع ربطها بالمحطات وتحديث المخزون');

        $this->command->info('🎉 اكتملت جميع البيانات التجريبية بنجاح!');
    }
}