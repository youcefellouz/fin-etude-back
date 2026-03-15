<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\AnalyticsService;

class GeneratePredictionsCommand extends Command
{
    protected $signature = 'analytics:predict 
                            {--days=7 : Number of days to forecast}
                            {--period=day : Period type (day, week, month)}';

    protected $description = 'Generate sales predictions for the upcoming period';

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

        $this->info('🔮 Démarrage de la génération des prédictions...');

        $created = 0;
        for ($i = 1; $i <= $days; $i++) {
            $date = now()->addDays($i);
            
            $prediction = $this->analyticsService->generateSalesPredictions($date, $periodType);
            
            if ($prediction) {
                $created++;
                $this->info("✅ Prédiction générée pour la date : {$date->format('Y-m-d')}");
            }
        }

        $this->info("🎉 {$created} prédiction(s) générée(s) avec succès !");

        return Command::SUCCESS;
    }
}