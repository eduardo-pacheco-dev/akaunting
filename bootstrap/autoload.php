<?php

// Define minimum supported PHP version
define('AKAUNTING_PHP', '8.2.0');

// Check PHP version
if (version_compare(PHP_VERSION, AKAUNTING_PHP, '<')) {
    $message = 'Error: Ask your hosting provider to use PHP ' . AKAUNTING_PHP . ' or higher for HTTP, CLI, and php command.' . PHP_EOL . PHP_EOL . 'Current PHP version: ' . PHP_VERSION . PHP_EOL;

    if (defined('STDOUT')) {
        fwrite(STDOUT, $message);
    } else {
        echo($message);
    }

    die(1);
}

// Composer loads legacy helper files before Laravel registers its own error
// handling, which floods the console with deprecation notices on newer PHP
// versions. Runtime deprecations are still handled/logged by Laravel after
// bootstrap; this only keeps the pre-bootstrap output readable.
error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED);

// Load composer for core
require __DIR__ . '/../vendor/autoload.php';
