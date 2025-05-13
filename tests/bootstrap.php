<?php
// tests/bootstrap.php

use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Component\Dotenv\Dotenv;

// 1) Autoloader
require dirname(__DIR__).'/vendor/autoload.php';

// 2) On charge d’abord les .env et .env.test/.env.local.test
$dotenv = new Dotenv();
$dotenv->loadEnv(dirname(__DIR__).'/.env');

// 3) Puis on override pour SQLite en mémoire
//    (vous pouvez aussi utiliser $_ENV['DATABASE_URL']=… si besoin)
putenv('DATABASE_URL=sqlite:///:memory:');

// 4) On boot le kernel en test
$kernel = new \App\Kernel('test', true);
$kernel->boot();

// 5) On (re)crée le schéma Doctrine **sur la connexion SQLite**
$container = $kernel->getContainer();
$em        = $container->get('doctrine')->getManager();
$meta      = $em->getMetadataFactory()->getAllMetadata();

if (!empty($meta)) {
    $tool = new SchemaTool($em);
    $tool->dropSchema($meta);
    $tool->createSchema($meta);
}
