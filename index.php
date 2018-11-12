<?php

/*
 * Copyright (c) 2018 Keira Dueck <sylae@calref.net>
 * Use of this source code is governed by the MIT license, which
 * can be found in the LICENSE file.
 */

namespace PRT;

use Carbon\Carbon;
use \FastRoute;

require_once "vendor/autoload.php";

$nowDate = Carbon::now("America/Los_Angeles")->setTimezone("America/Los_Angeles")->setDate(2011, 7, 14);
Carbon::setTestNow($nowDate);

$dispatcher = FastRoute\simpleDispatcher(function(\FastRoute\RouteCollector $r) {
    $r->addRoute('GET', '/[{search}]', function(array $args) {
        $loader = new \Twig\Loader\FilesystemLoader('tpl');
        $twig   = new \Twig\Environment($loader, [
            'cache' => false,
        ]);

        $capes = new CapeCollection();

        $searches = include "data/savedSearches.php";

        if (array_key_exists($args['search'] ?? null, $searches)) {
            echo $twig->render("capes.twig", [
                'base'     => (php_uname('s') == "Windows NT") ? "" : "/diary",
                'capes'    => $capes->filter($searches[$args['search']]['filter']),
                'params'   => $searches[$args['search']]['args'],
                'now'      => Carbon::now(),
                'searches' => $searches,
            ]);
        } else {
            echo $twig->render("base.twig", [
                'base'     => (php_uname('s') == "Windows NT") ? "" : "/diary",
                'now'      => Carbon::now(),
                'searches' => $searches,
            ]);
        }
    });
});

$uri = $_SERVER['REQUEST_URI'];
if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

$routeInfo = $dispatcher->dispatch($_SERVER['REQUEST_METHOD'], $uri);
switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        http_response_code(404);
        $loader = new \Twig\Loader\FilesystemLoader('tpl');
        $twig   = new \Twig\Environment($loader, [
            'cache' => false,
        ]);
        echo $twig->render("404.twig", [
            'base' => (php_uname('s') == "Windows NT") ? "" : "/diary",
            'uri'  => $uri
        ]);
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        http_response_code(405);
        $loader = new \Twig\Loader\FilesystemLoader('tpl');
        $twig   = new \Twig\Environment($loader, [
            'cache' => false,
        ]);
        echo $twig->render("405.twig", [
            'base'           => (php_uname('s') == "Windows NT") ? "" : "/diary",
            'allowedMethods' => $routeInfo[1]
        ]);
        break;
    case FastRoute\Dispatcher::FOUND:
        $routeInfo[1]($routeInfo[2]);
        break;
}

