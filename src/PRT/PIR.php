<?php

/**
 * Copyright (c) 2019 Keira Dueck <sylae@calref.net>
 * Use of this source code is governed by the MIT license, which
 * can be found in the LICENSE file.
 */

namespace PRT;

use Carbon\Carbon;
use Carbon\CarbonInterface;
use CharlotteDunois\Collect\Collection;
use RangeException;

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
     * @var CarbonInterface
     */
    public $date;
    public $name;
    public $intelAgency = [];
    public $groups = [];
    public $notePostPIR;
    /**
     *
     * @var Collection
     */
    private $refs;

    public function __construct(int $id, CarbonInterface $date, string $dept)
    {
        $this->refs = new Collection();
        if (!$this->isValidID($id)) {
            throw new RangeException("ID failed checks!");
        }
        if (!$this->isValidDate($date)) {
            throw new RangeException("Date failed checks!");
        }
        if (!$this->isValidDept($dept)) {
            throw new RangeException("Dept failed checks!");
        }
        $this->id = $id;
        $this->date = $date->startOfDay();
        $this->dept = $dept;

        $this->setSelf();
    }

    private function isValidID(int $input): bool
    {
        if ($input > 0xFFFF || $input < 0x0) {
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

    private function isValidDept(string $input): bool
    {
        if (mb_strlen($input) != 3) {
            return false;
        }
        return true;
    }

    private function setSelf()
    {
        self::pirDB()->set($this->getTag(), $this);
    }

    public static function pirDB(): Collection
    {
        static $pirs;

        if (!$pirs instanceof Collection) {
            $pirs = new Collection();
        }
        return $pirs;
    }

    public function getTag(): string
    {
        return sprintf("%s-%s-%s", mb_strtoupper($this->dept), $this->date->year, $this->formatID());
    }

    private function formatID(): string
    {
        return mb_strtoupper(str_pad(dechex($this->id), 4, "0", STR_PAD_LEFT));
    }

    public function addRef(PIR $ref, bool $reverse = true)
    {
        $this->refs->set($ref->getTag(), $ref);
        if ($reverse) {
            $ref->refs->set($this->getTag(), $this);
        }
    }

    public function getLongTag(): string
    {
        return sprintf("%s %s", $this->getTag(), $this->date->format("Y-m-d"));
    }

    public function getRefs(PIR $needle)
    {
        return self::pirDB()->filter(function (PIR $v) {
            // remove self first
            return ($v != $this);
        })->filter(function (PIR $v) use ($needle) {
            return $v->hasRef($needle);
        })->sortCustom(function (PIR $a, PIR $b) {
            return $a->getSortNumber() <=> $b->getSortNumber();
        });
    }

    public function hasRef(PIR $ref)
    {
        return $this->refs->has($ref->getTag());
    }

    public function getSortNumber(): int
    {
        return $this->date->year * 100000 + $this->id;
    }

    public function linkedFilesString(Collection $refs): string
    {
        $txt = "<a href=\"{{ base }}/pir/%s\" class=\"pirLink\">%s</a>";
        $x = [];
        foreach ($refs as $ref) {
            if (get_class($ref) === 'PRT\PIR') {
                $x[] = $ref->getTag();
            } else {
                $x[] = sprintf($txt, $ref->getTag(), $ref->getTag());
            }
        }
        return implode(", ", $x);
    }
}
