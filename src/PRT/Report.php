<?php

/**
 * Copyright (c) 2019 Keira Dueck <sylae@calref.net>
 * Use of this source code is governed by the MIT license, which
 * can be found in the LICENSE file.
 */

namespace PRT;

use Carbon\CarbonInterface;

/**
 * It's a report! We hold things here.
 *
 * @author Keira Dueck <sylae@calref.net>
 */
class Report extends PIR
{

    public function __construct(int $id, CarbonInterface $date, string $dept)
    {
        parent::__construct($id, $date, $dept);
        $this->civID = new CivID();
        $this->addRef($this);
    }
}
