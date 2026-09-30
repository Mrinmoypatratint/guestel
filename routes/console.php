<?php
use Illuminate\Support\Facades\Schedule;
Schedule::command('hospitality:expire-guest-sessions')->everyFifteenMinutes()->withoutOverlapping();
Schedule::command('hospitality:escalate-sla')->everyMinute()->withoutOverlapping();
Schedule::command('model:prune')->daily();
