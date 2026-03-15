<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\AnalyticsService;

class DetectPatternsCommand extends Command
{
    protected $signature = 'analytics:detect-patterns';

    protected $description = 'detect patterns and create smart alerts';

    protected $analyticsService;

    public function __construct(AnalyticsService $analyticsService)
    {
        parent::__construct();
        $this->analyticsService = $analyticsService;
    }

    public function handle()
    {
        $this->info('🔍 Démarrage de la détection des patterns...');

        $alerts = $this->analyticsService->detectPatterns();

        $this->info('✅ Patterns détectés avec succès');
        $this->info("📢 Créé avec succès " . count($alerts) . " nouvelles alertes");

        return Command::SUCCESS;
    }
}