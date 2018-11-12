<?php

/*
 * Copyright (c) 2018 Keira Dueck <sylae@calref.net>
 * Use of this source code is governed by the MIT license, which
 * can be found in the LICENSE file.
 */

namespace PRT;

use Carbon\Carbon;

/**
 * Extension of Collection that autoloads our capes.
 *
 * @author Keira Dueck <sylae@calref.net>
 */
class CapeCollection extends \CharlotteDunois\Collect\Collection
{

    function __construct(mixed $data = null)
    {
        $capes = [];
        foreach (glob("data/Capes/*.json") as $file) {
            foreach (json_decode(file_get_contents($file), true) as $x) {
                $c = new Cape();
                foreach ($x as $arg => $value) {
                    if (is_null($arg)) {
                        continue;
                    }
                    switch ($arg) {
                        case "PIR":
                            $c->mainPIR = new PIR(hexdec($value[0]), new Carbon($value[1]), $value[2]);
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
                $capes[$c->name] = $c;
            }
        }
        parent::__construct($capes);
    }
}
