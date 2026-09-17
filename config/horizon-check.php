<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Schedule
    |--------------------------------------------------------------------------
    |
    | Cron expression for how often horizon:check runs through the Laravel
    | scheduler. Set to null to register the command without scheduling it.
    |
    */

    'schedule' => env('HORIZON_CHECK_SCHEDULE', '*/30 * * * *'),

];
