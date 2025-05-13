<?php
// tests/bootstrap.php

use Symfony\Component\Dotenv\Dotenv;

require dirname(__DIR__).'/vendor/autoload.php';

$dotenv = new Dotenv();

// Charge .env et .env.local
if (file_exists(dirname(__DIR__).'/.env')) {
    $dotenv->loadEnv(dirname(__DIR__).'/.env');
}

// Force le contenu de .env.test **après**
// pour **écraser** DATABASE_URL
if (file_exists(dirname(__DIR__).'/.env.test')) {
    $dotenv->overload(dirname(__DIR__).'/.env.test');
}
