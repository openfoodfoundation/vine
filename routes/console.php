<?php

use App\Console\Commands\DispatchCollateSystemStatisticsJobCommand;
use App\Console\Commands\VoucherSets\GenerateVoucherSetVouchersCommand;
use Illuminate\Support\Facades\Schedule;


Schedule::command(GenerateVoucherSetVouchersCommand::class)
        ->everyFiveMinutes()
        ->withoutOverlapping();

Schedule::command(DispatchCollateSystemStatisticsJobCommand::class)
    ->dailyAt('23:55')
    ->timezone('Australia/Melbourne');
