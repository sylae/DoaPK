<?php
/**
 * Copyright (c) 2019 Keira Dueck <sylae@calref.net>
 * Use of this source code is governed by the MIT license, which
 * can be found in the LICENSE file.
 */

namespace PRT;

use Carbon\Carbon;

$nowDate = Carbon::now("America/Los_Angeles")->setTimezone("America/Los_Angeles")->setDate(2011, 7, 14);
Carbon::setTestNow($nowDate);

// store our PIR links because we can't connect them until this is done :v
$pirs = [];

// DATA LOADING STEP ONE (CAPES)
foreach (glob("data/Capes/*.json") as $file) {
    foreach (json_decode(file_get_contents($file), true) as $x) {
        $c = new Cape((int) hexdec($x['PIR'][0]), new Carbon($x['PIR'][1]), $x['PIR'][2]);
        foreach ($x as $arg => $value) {
            if (is_null($arg)) {
                continue;
            }
            switch ($arg) {
                case "PIR":
                    break;
                case "links":
                    $pirs[$c->getTag()] = $value;
                    break;
                case "civ":
                    if (!is_array($value)) {
                        break;
                    }
                    foreach ($value as $civArg => $civValue) {
                        $c->civID->$civArg = $civValue;
                    }
                    if (is_string($c->civID->dob)) {
                        $c->civID->dob = new Carbon($c->civID->dob);
                    }
                    break;
                default:
                    $c->$arg = $value;
            }
        }
    }
}

// DATA LOADING STEP TWO (ART)
foreach (glob("data/Art/*.json") as $file) {
    foreach (json_decode(file_get_contents($file), true) as $x) {
        $c = new Media((int) hexdec($x['PIR'][0]), new Carbon($x['PIR'][1]), $x['PIR'][2]);
        foreach ($x as $arg => $value) {
            if (is_null($arg)) {
                continue;
            }
            switch ($arg) {
                case "PIR":
                    break;
                case "links":
                    $pirs[$c->getTag()] = $value;
                    break;
                default:
                    $c->$arg = $value;
            }
        }
    }
}

// CONNECT PIRS
foreach ($pirs as $source => $array) {
    $source = PIR::pirDB()->get($source);
    foreach ($array as $dest) {
        $source->addRef(PIR::pirDB()->get($dest));
    }
}
