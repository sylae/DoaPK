<?php
/**
 * Copyright (c) 2019 Keira Dueck <sylae@calref.net>
 * Use of this source code is governed by the MIT license, which
 * can be found in the LICENSE file.
 */

namespace PRT;

require_once "vendor/autoload.php";
require_once "bootstrap.php";

$sorted = PIR::pirDB()->sortCustom(function (PIR $a, PIR $b) {
    return $a->getSortNumber() <=> $b->getSortNumber();
});

$last = null;
/** @var PIR $v */
foreach ($sorted as $v) {
    // get "average" PIR id...
    $day = (int) ($v->date->dayOfYear * 0xffff / 365);
    $offset = $day - $v->id;

    if (!is_null($last) && $last->date != $v->date) {
        if ($last->date->year != $v->date->year) {
            echo "-- YEAR BREAK --\n";
        } else {
            $slope = ($v->id - $last->id) / ($v->date->diffInDays($last->date));
            foreach (range(1, ($v->date->diffInDays($last->date) - 1)) as $interpDay) {
                $newDay = clone $last->date;
                $newDay->addDays($interpDay);
                $newID = ($slope * $interpDay) + $last->id;
                $interpTag = (new PIR($newID, $newDay, "XXX"))->getLongTag();
                // echo sprintf("%s (interpolated %s per day)\n", $interpTag, number_format($slope));
            }
        }
    }

    echo sprintf("%s %s (%s // %s)\n", $v->getLongTag(), $v->name, dechex($day), $offset);
    $last = $v;
}

