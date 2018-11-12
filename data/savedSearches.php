<?php

/*
 * Copyright (c) 2018 Keira Dueck <sylae@calref.net>
 * Use of this source code is governed by the MIT license, which
 * can be found in the LICENSE file.
 */

namespace PRT;

$searches = [];

$searches['heroesPDX'] = [
    'name'   => 'Portland Heroes',
    'args'   => [
        '<em>Filed under</em>: Wards OR Protectorate',
        'AND <em>Filed under</em>: Portland Area',
        'AND <em>Filed under</em>: Active',
    ],
    'filter' => function(Cape $v, $k): bool {
        return (( in_array("Wards", $v->groups) || in_array("Protectorate", $v->groups)) && in_array("Portland Area", $v->groups) && in_array("Active", $v->groups));
    }
];

$searches['elite'] = [
    'name'   => 'Elite with known Portland ties',
    'args'   => [
        '<em>Filed under</em>: Elite',
        'AND <em>Filed under</em>: Portland Area',
        'AND <em>Filed under</em>: Active',
    ],
    'filter' => function(Cape $v, $k): bool {
        return (in_array("Elite", $v->groups) && in_array("Portland Area", $v->groups) && in_array("Active", $v->groups));
    }
];

$searches['teamsters'] = [
    'name'   => 'Teamsters (Portland villain group)',
    'args'   => [
        '<em>Filed under</em>: Teamsters',
        'AND <em>Filed under</em>: Active',
    ],
    'filter' => function(Cape $v, $k): bool {
        return (in_array("Teamsters", $v->groups) && in_array("Active", $v->groups));
    }
];

$searches['burnside'] = [
    'name'   => 'Burnside Street Gang',
    'args'   => [
        '<em>Filed under</em>: Burnside St',
        'AND <em>Filed under</em>: Active',
    ],
    'filter' => function(Cape $v, $k): bool {
        return (in_array("Burnside St", $v->groups) && in_array("Active", $v->groups));
    }
];

$searches['cascadians'] = [
    'name'   => 'Cascadians',
    'args'   => [
        '<em>Filed under</em>: Cascadians',
        'AND <em>Filed under</em>: Active',
    ],
    'filter' => function(Cape $v, $k): bool {
        return (in_array("Elite", $v->groups) && in_array("Active", $v->groups));
    }
];

$searches['indies'] = [
    'name'   => 'Indie villains',
    'args'   => [
        '<em>Filed under</em>: Unaffiliated (Villain) OR Unaffiliated (Rogue) OR Unaffiliated (Unknown)',
        'AND <em>Filed under</em>: Portland Area',
        'AND <em>Filed under</em>: Active',
    ],
    'filter' => function(Cape $v, $k): bool {
        return (( in_array("Unaffiliated (Villain)", $v->groups) || in_array("Unaffiliated (Rogue)", $v->groups) || in_array("Unaffiliated (Unknown)", $v->groups)) && in_array("Portland Area", $v->groups) && in_array("Active", $v->groups));
    }
];

return $searches;
