<?php

/**
 * Copyright (c) 2019 Keira Dueck <sylae@calref.net>
 * Use of this source code is governed by the MIT license, which
 * can be found in the LICENSE file.
 */

namespace PRT;

use Carbon\CarbonInterface;

/**
 * Stores information on a Cape.
 *
 * @author Keira Dueck <sylae@calref.net>
 */
class Cape extends PIR
{
    public $classification = [];

    /**
     *
     * @var CivID
     */
    public $civID;
    public $notePower;
    public $noteBehavior;
    public $noteAppearance;
    public $noteContainment;

    public function __construct(int $id, CarbonInterface $date, string $dept)
    {
        parent::__construct($id, $date, $dept);
        $this->civID = new CivID();
        $this->addRef($this);
    }
}
