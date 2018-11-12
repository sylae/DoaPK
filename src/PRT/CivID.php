<?php

/*
 * Copyright (c) 2018 Keira Dueck <sylae@calref.net>
 * Use of this source code is governed by the MIT license, which
 * can be found in the LICENSE file.
 */

namespace PRT;

use Carbon\Carbon;

/**
 * Helper class to hold civ ID stuff. Easier this way.
 *
 * @author Keira Dueck <sylae@calref.net>
 */
class CivID
{
    public $name;
    public $confidence;
    public $dob;
    public $dobConfident = true;
    public $birthplace;
    public $note;
    public $agency       = [];
    public $c53          = false;

    public function pirString(): string
    {
        if ($this->c53) {
            return "<p><span class=\"prtHeader\">Civ. Identity</span>: See PRT-SPEC-0053</p>";
        }
        $agency  = implode("/", $this->agency);
        $lines   = [];
        $lines[] = "<p class=\"prtMinor\">-----BEGIN TOP SECRET//{$agency}//ORCON-----</p>";
        $lines[] = "<p><span class=\"prtHeader\">Civ. Identity</span>: " . $this->verifiedString();
        if (is_string($this->name)) {
            $lines[] = '<br /><span class="prtHeader">Name</span>: ' . $this->name;
        }
        if ($this->dob instanceof Carbon) {
            $lines[] = '<br /><span class="prtHeader">Age</span>: ' . $this->ageString();
        }
        $lines[] = "<p class=\"prtMinor\">-----END TOP SECRET//{$agency}//ORCON-----</p>";
        return implode(PHP_EOL, $lines);
    }

    private function ageString(): string
    {
        if ($this->dob instanceof Carbon) {
            $age   = (string) $this->dob->diffInYears();
            $paren = [];
            if ($this->dobConfident) {
                $paren[] = "born " . $this->dob->format("Y-m-d");
            }
            if (is_string($this->birthplace)) {
                $paren[] = $this->birthplace;
            }
            if (count($paren) > 0) {
                return $age . " (" . implode(", ", $paren) . ")";
            } else {
                return $age;
            }
        }
        return "";
    }

    private function verifiedString(): string
    {
        switch ($this->confidence) {
            case "V":
                return "Verified";
            case "A":
                return "Likely identity matched (confidence level A)";
            case "B":
                return "Probable identity matched (confidence level B)";
            case "C":
                return "Potential match found (confidence level C)";
            case "D":
                return "Potential match found (confidence level D)";
            case "F":
            default:
                return "Limited information known (unverified, confidence level F)";
        }
    }
}
