<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\AnalyticsService;

class GeneratePredictionsCommand extends Command
{
    protected $signature = 'analytics:predict 
                            {--days=7 : عدد الأيام للتوقع}
                            {--period=day : نوع الفترة (day, week, month)}';

    protected $description = 'إنشاء توقعات المبيعات للفترة القادمة';

    protected $analyticsService;

    public function __construct(AnalyticsService $analyticsService)
    {
        parent::__construct();
        $this->analyticsService = $analyticsService;
    }

    public function handle()
    {
        $days = $this->option('days');
        $periodType = $this->option('period');

        $this->info('🔮 بدء إنشاء التوقعات...');

        $created = 0;
        for ($i = 1; $i <= $days; $i++) {
            $date = now()->addDays($i);
            
            $prediction = $this->analyticsService->generateSalesPredictions($date, $periodType);
            
            if ($prediction) {
                $created++;
                $this->info("✅ تم إنشاء توقع لتاريخ: {$date->format('Y-m-d')}");
            }
        }

        $this->info("🎉 تم إنشاء {$created} توقع بنجاح!");

        return Command::SUCCESS;
    }
}