<?php

use App\Console\Commands\DispatchCollateSystemStatisticsJobCommand;
use Illuminate\Support\Facades\Schedule;

Schedule::command(DispatchCollateSystemStatisticsJobCommand::class)
    ->dailyAt('23:55')
    ->timezone('Australia/Melbourne');
