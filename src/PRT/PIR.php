<?php

/*
 * Copyright (c) 2018 Keira Dueck <sylae@calref.net>
 * Use of this source code is governed by the MIT license, which
 * can be found in the LICENSE file.
 */

namespace PRT;

use Carbon\Carbon;
use \Carbon\CarbonInterface;
use CharlotteDunois\Collect\Collection;

/**
 * All reports in the PRT system are assigned a PIR.
 *
 * @author Keira Dueck <sylae@calref.net>
 */
class PIR
{
    /**
     *
     * @var int
     */
    public $id;

    /**
     *
     * @var string
     */
    public $dept;

    /**
     *
     * @var \Carbon\CarbonInterface
     */
    public $date;

    /**
     *
     * @var \CharlotteDunois\Collect\Collection
     */
    private $capes;

    public function __construct(int $id, CarbonInterface $date, string $dept)
    {
        $this->capes = new Collection();
        if (!$this->isValidID($id)) {
            throw new \RangeException("ID failed checks!");
        }
        if (!$this->isValidDate($date)) {
            throw new \RangeException("Date failed checks!");
        }
        if (!$this->isValidDept($dept)) {
            throw new \RangeException("Dept failed checks!");
        }
        $this->id   = $id;
        $this->date = $date->startOfDay();
        $this->dept = $dept;
    }

    public function addCape(string $cape)
    {
        $this->capes->set($cape, true);
    }

    public function hasCape(string $cape)
    {
        return $this->capes->contains($cape);
    }

    public function getTag(): string
    {
        return sprintf("%s-%s-%s", mb_strtoupper($this->dept), $this->date->year, $this->formatID());
    }

    public function getLongTag(): string
    {
        return sprintf("%s %s", $this->getTag(), $this->date->format("Y-m-d"));
    }

    private function formatID(): string
    {
        return mb_strtoupper(str_pad(dechex($this->id), 4, "0", STR_PAD_LEFT));
    }

    private function isValidID(int $input): bool
    {
        if ($input > 0xFFFF || $input < 0x0) {
            return false;
        }
        return true;
    }

    private function isValidDept(string $input): bool
    {
        if (mb_strlen($input) != 3) {
            return false;
        }
        return true;
    }

    private function isValidDate(CarbonInterface $input): bool
    {
        $formation = new Carbon("18 Jan 1993");
        if ($input < $formation) {
            return false;
        }
        return true;
    }
}
