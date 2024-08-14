<?php

Schedule::command('app:dispatch-collate-system-statistics-job')
    ->dailyAt('23:55')
    ->timezone('Australia/Melbourne');
