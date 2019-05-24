<?php

/**
 * Copyright (c) 2019 Keira Dueck <sylae@calref.net>
 * Use of this source code is governed by the MIT license, which
 * can be found in the LICENSE file.
 */

namespace PRT;

use Carbon\CarbonInterface;

/**
 * Pictures! of Art!
 *
 * @author Keira Dueck <sylae@calref.net>
 */
class Media extends PIR
{
    public $artist;
    public $artistURL;
    public $remarks;
    public $image;

    public function __construct(int $id, CarbonInterface $date, string $dept)
    {
        parent::__construct($id, $date, $dept);
        $this->addRef($this);
    }
}
