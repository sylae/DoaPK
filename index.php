<?php

/**
 * Copyright (c) 2019 Keira Dueck <sylae@calref.net>
 * Use of this source code is governed by the MIT license, which
 * can be found in the LICENSE file.
 */

namespace PRT;

use Carbon\Carbon;
use FastRoute;
use FastRoute\RouteCollector;
use ReflectionClass;
use Twig\Environment;
use Twig\Extension\StringLoaderExtension;
use Twig\Loader\FilesystemLoader;
use Twig\TwigTest;
use function FastRoute\simpleDispatcher;

require_once "vendor/autoload.php";
require_once "bootstrap.php";


// THE ACTUAL ROUTER
$dispatcher = simpleDispatcher(function (RouteCollector $r) {
    $loader = new FilesystemLoader('tpl');
    $twig = new Environment($loader, [
        'cache' => false,
    ]);
    $twig->addExtension(new StringLoaderExtension());
    $twig->addTest(new TwigTest('instanceof', function ($var, $instance) {
        $reflexionClass = new ReflectionClass($instance);
        return $reflexionClass->isInstance($var);
    }));
    $searches = include "data/savedSearches.php";

    $r->addRoute('GET', '/diary/lookup', function () use ($twig, $searches) {
        echo $twig->render("lookup.twig", [
            'base' => (php_uname('s') == "Windows NT") ? "" : "/diary",
            'now' => Carbon::now(),
            'count' => PIR::pirDB()->count(),
            'searches' => $searches,
        ]);
    });

    $r->addRoute('GET', '/diary/pir/{pir}', function (array $args) use ($twig, $searches) {
        if (PIR::pirDB()->has($args['pir'] ?? null)) {
            echo $twig->render("view.twig", [
                'base' => (php_uname('s') == "Windows NT") ? "" : "/diary",
                'records' => PIR::pirDB()->filter(function (PIR $v) use ($args) {
                    return $v->getTag() == $args['pir'];
                }),
                'params' => ['<em>PIR reference number</em>: Equal to ' . htmlspecialchars($args['pir'])],
                'now' => Carbon::now(),
                'count' => PIR::pirDB()->count(),
                'searches' => $searches,
            ]);
        } else {
            echo $twig->render("base.twig", [
                'base' => (php_uname('s') == "Windows NT") ? "" : "/diary",
                'now' => Carbon::now(),
                'count' => PIR::pirDB()->count(),
                'searches' => $searches,
            ]);
        }
    });

    $r->addRoute('GET', '/diary/[{search}]', function (array $args) use ($twig, $searches) {

        if (array_key_exists($args['search'] ?? null, $searches)) {
            echo $twig->render("view.twig", [
                'base' => (php_uname('s') == "Windows NT") ? "" : "/diary",
                'records' => PIR::pirDB()->filter($searches[$args['search']]['filter']),
                'params' => $searches[$args['search']]['args'],
                'now' => Carbon::now(),
                'count' => PIR::pirDB()->count(),
                'searches' => $searches,
            ]);
        } else {
            echo $twig->render("base.twig", [
                'base' => (php_uname('s') == "Windows NT") ? "" : "/diary",
                'now' => Carbon::now(),
                'count' => PIR::pirDB()->count(),
                'searches' => $searches,
            ]);
        }
    });
});

$uri = $_SERVER['REQUEST_URI'];
if (php_uname('s') == "Windows NT") {
    $uri = "/diary" . $uri;
}
if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

$routeInfo = $dispatcher->dispatch($_SERVER['REQUEST_METHOD'], $uri);
switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        http_response_code(404);
        $loader = new FilesystemLoader('tpl');
        $twig = new Environment($loader, [
            'cache' => false,
        ]);
        echo $twig->render("404.twig", [
            'base' => (php_uname('s') == "Windows NT") ? "" : "/diary",
            'now' => Carbon::now(),
            'count' => PIR::pirDB()->count(),
            'uri' => $uri,
        ]);
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        http_response_code(405);
        $loader = new FilesystemLoader('tpl');
        $twig = new Environment($loader, [
            'cache' => false,
        ]);
        echo $twig->render("405.twig", [
            'base' => (php_uname('s') == "Windows NT") ? "" : "/diary",
            'now' => Carbon::now(),
            'count' => PIR::pirDB()->count(),
            'allowedMethods' => $routeInfo[1],
        ]);
        break;
    case FastRoute\Dispatcher::FOUND:
        $routeInfo[1]($routeInfo[2]);
        break;
}
