<?php

use Dotenv\Dotenv;

require_once '../vendor/autoload.php';

$dotnev = Dotenv::createImmutable(__DIR__ . "/../");
$dotnev->load();

require_once '../config/app.php';


