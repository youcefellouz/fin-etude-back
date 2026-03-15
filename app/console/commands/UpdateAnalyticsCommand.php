<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\AnalyticsService;

class UpdateAnalyticsCommand extends Command
{
    protected $signature = 'analytics:update {--type=all : Type of analytics to update (all, customers, products)}';

    protected $description = 'Update customer and product analytics';

    protected $analyticsService;

    public function __construct(AnalyticsService $analyticsService)
    {
        parent::__construct();
        $this->analyticsService = $analyticsService;
    }

    public function handle()
    {
        $type = $this->option('type');

        $this->info('🚀 Démarrage de la mise à jour des analytiques...');

        if ($type === 'all' || $type === 'customers') {
            $this->info('📊 Mise à jour des analytiques clients...');
            $this->analyticsService->updateAllCustomerAnalytics();
            $this->info('✅ Analytiques clients mises à jour avec succès');
        }

        if ($type === 'all' || $type === 'products') {
            $this->info('📦 Mise à jour des analytiques produits...');
            $this->analyticsService->updateAllProductAnalytics();
            $this->info('✅ Analytiques produits mises à jour avec succès');
        }

        $this->info('🎉 Mise à jour des analytiques terminée avec succès !');

        return Command::SUCCESS;
    }
}