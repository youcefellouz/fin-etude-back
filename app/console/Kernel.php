protected function schedule(Schedule $schedule)
{
    // تحديث التحليلات كل ساعة
    $schedule->command('analytics:update')->hourly();
    
    // كشف الأنماط كل 6 ساعات
    $schedule->command('analytics:detect-patterns')->everySixHours();
    
    // إنشاء توقعات يومياً في منتصف الليل
    $schedule->command('analytics:predict --days=7 --period=day')->daily();

}