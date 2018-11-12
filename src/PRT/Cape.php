<?php

/*
 * Copyright (c) 2018 Keira Dueck <sylae@calref.net>
 * Use of this source code is governed by the MIT license, which
 * can be found in the LICENSE file.
 */

namespace PRT;

use CharlotteDunois\Collect\Collection;

/**
 * Stores information on a Cape.
 *
 * @author Keira Dueck <sylae@calref.net>
 */
class Cape
{
    public $name;

    /**
     *
     * @var PIR
     */
    public $mainPIR;
    public $intelAgency    = [];
    public $classification = [];
    public $groups         = [];

    /**
     *
     * @var CivID
     */
    public $civID;
    public $notePower;
    public $notePostPIR;
    public $noteBehavior;
    public $noteAppearance;
    public $noteContainment;
    public $image;

    public function __construct()
    {
        $this->civID = new CivID();
    }

    public function __toString(): string
    {
        return $this->toHTMLString();
    }

    /**
     * @todo replace this with a Twig template
     */
    public function toHTMLString(): string
    {
        $agency = implode("/", $this->intelAgency);
        $pir    = $this->mainPIR->getLongTag();

        $lines   = [];
        $lines[] = "<p class=\"prtMinor\">CONFIDENTIAL//{$agency}//ORCON</p>"
        . "<p class=\"prtMinor\">PARAHUMAN RESPONSE TEAM<br />"
        . "PACIFIC NORTHWEST DIVISION - PORTLAND OFFICE<br />"
        . "INTELLIGENCE BUREAU</p>";

        $lines[] = "<p class=\"prtMinor\">Parahuman Intelligence Report<br />{$pir}</p>";


        if (is_string($this->notePostPIR)) {
            $lines[] = "<p class=\"prtMinor\">{$this->notePostPIR}</p>";
        }

        if (count($this->groups) > 0) {
            $groups  = implode(", ", $this->groups);
            $lines[] = "<p><span class=\"prtHeader\">Filed under</span>: {$groups}</p>";
        }

        $lines[] = "<p><span class = \"prtHeader\">Subject ID</span>: {$this->name}</p>";
        if (count($this->classification) > 0) {
            $class   = implode(", ", $this->classification);
            $lines[] = "<p><span class=\"prtHeader\">Classification</span>: {$class}</p>";
        }
        $lines[] = $this->civID->pirString();

        if (is_string($this->notePower)) {
            $lines[] = "<p><span class=\"prtHeader\">Power</span>: {$this->notePower}</p>";
        }
        if (is_string($this->noteBehavior)) {
            $lines[] = "<p><span class=\"prtHeader\">Behavior</span>: {$this->noteBehavior}</p>";
        }
        if (is_string($this->noteAppearance)) {
            $lines[] = "<p><span class=\"prtHeader\">Appearance</span>: {$this->noteAppearance}</p>";
        }
        if (is_string($this->noteContainment)) {
            $lines[] = "<p><span class=\"prtHeader\">Containment</span>: {$this->noteContainment}</p>";
        } elseif (count($this->classification) > 0) {
            $lines[] = "<p><span class=\"prtHeader\">Containment</span>: Per Classification SOP.</p>";
        }

        if (count($this->pirs) > 0) {
            $lines[] = "<p><span class=\"prtMinor\"><span class=\"prtHeader\">Linked files</span>: " . $this->linkedFilesString() . "</p>";
        }
        $lines[] = "<p class=\"prtMinor\">CONFIDENTIAL//{$agency}//ORCON</span></p>";

        return implode(PHP_EOL, $lines);
    }

    public function linkedFilesString(): string
    {
        return "";
    }
}
