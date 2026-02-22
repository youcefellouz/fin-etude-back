<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\AnalyticsService;

class UpdateAnalyticsCommand extends Command
{
    protected $signature = 'analytics:update {--type=all : Type of analytics to update (all, customers, products)}';

    protected $description = 'تحديث تحليلات العملاء والمنتجات';

    protected $analyticsService;

    public function __construct(AnalyticsService $analyticsService)
    {
        parent::__construct();
        $this->analyticsService = $analyticsService;
    }

    public function handle()
    {
        $type = $this->option('type');

        $this->info('🚀 بدء تحديث التحليلات...');

        if ($type === 'all' || $type === 'customers') {
            $this->info('📊 تحديث تحليلات العملاء...');
            $this->analyticsService->updateAllCustomerAnalytics();
            $this->info('✅ تم تحديث تحليلات العملاء');
        }

        if ($type === 'all' || $type === 'products') {
            $this->info('📦 تحديث تحليلات المنتجات...');
            $this->analyticsService->updateAllProductAnalytics();
            $this->info('✅ تم تحديث تحليلات المنتجات');
        }

        $this->info('🎉 اكتمل تحديث التحليلات بنجاح!');

        return Command::SUCCESS;
    }
}