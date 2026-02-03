<?php

declare(strict_types=1);

use Symfony\Component\Dotenv\Dotenv;

// Load .env file
$dotenv = new Dotenv();
$envFile = dirname(__DIR__, 2) . '/.env';
if (file_exists($envFile)) {
    $dotenv->load($envFile);
}
