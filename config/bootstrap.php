<?php

declare(strict_types=1);

// Load environment variables from .env file
if (class_exists('Symfony\Component\Dotenv\Dotenv')) {
    $dotenv = new Symfony\Component\Dotenv\Dotenv();
    $envFile = dirname(__DIR__) . '/.env';
    if (file_exists($envFile)) {
        $dotenv->load($envFile);
    }
}
