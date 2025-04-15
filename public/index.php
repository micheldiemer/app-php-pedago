<?php

require_once __DIR__ . '/../vendor/autoload.php';

$app = new App\App\App("App PHP Pedago", __DIR__ . '/..');


/*
$app->rep()->html('<b>' . gras . '</b>')->send();
$app->rep()->raw('<b>' . gras . '</b>')->send();
$app->rep()->json(['todos' => ['id' => '´1', 'name' => 'app php pedago', 'done' => 'false']])->send();
*/
$app->rep()->raw(strval($app->req()->uri()))->send();
$app->rep()->raw(strval($app->rep()->uri()))->send();
