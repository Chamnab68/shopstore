<?php

use Illuminate\Support\Facades\Schedule;

// Send monthly sales summary on 1st of each month at 8:00 AM
Schedule::command('report:monthly-summary')->monthlyOn(1, '08:00');

// Optional: test manually anytime with:
// php artisan report:monthly-summary
// php artisan report:monthly-summary --month=2026-02
