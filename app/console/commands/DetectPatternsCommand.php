<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\AnalyticsService;

class DetectPatternsCommand extends Command
{
    protected $signature = 'analytics:detect-patterns';

    protected $description = 'كشف الأنماط وإنشاء التنبيهات الذكية';

    protected $analyticsService;

    public function __construct(AnalyticsService $analyticsService)
    {
        parent::__construct();
        $this->analyticsService = $analyticsService;
    }

    public function handle()
    {
        $this->info('🔍 بدء كشف الأنماط...');

        $alerts = $this->analyticsService->detectPatterns();

        $this->info('✅ تم كشف الأنماط بنجاح');
        $this->info("📢 تم إنشاء " . count($alerts) . " تنبيه جديد");

        return Command::SUCCESS;
    }
}