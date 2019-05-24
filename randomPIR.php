<?php

/*
 * Copyright (c) 2018 Keira Dueck <sylae@calref.net>
 * Use of this source code is governed by the MIT license, which
 * can be found in the LICENSE file.
 */

namespace PRT;

use Carbon\Carbon;

require_once "vendor/autoload.php";

$nowDate = Carbon::now("America/Los_Angeles")->setTimezone("America/Los_Angeles")->setDate(2011, 7, 14);
Carbon::setTestNow($nowDate);

$x = [];
foreach (range(1, $argv[1] ?? 1) as $n) {
    $day = random_int(1, 365);
    $pirNo = max(0, min(0xffff, ($day * 0xffff / 365) + random_int(-180, 180)));
    $cbn = (new \Carbon\CarbonImmutable("2010-01-01"))->addDays($day - 1);
    $args = [
        (new PIR($pirNo, $cbn->addYears(0), "PDX"))->getLongTag(),
        (new PIR($pirNo, $cbn->addYears(-5), "PDX"))->getLongTag(),
        (new PIR($pirNo, $cbn->addYears(-10), "PDX"))->getLongTag(),
        (new PIR($pirNo, $cbn->addYears(-15), "PDX"))->getLongTag(),
    ];
    $x[] = vsprintf("%s  %s  %s  %s", $args);
}
echo implode(PHP_EOL, $x);
