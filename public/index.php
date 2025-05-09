<?php

// Set error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set timezone
date_default_timezone_set('Europe/Istanbul');

// Define base path
define('BASE_PATH', dirname(__DIR__));

// Autoload classes
require_once BASE_PATH . '/vendor/autoload.php';

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(BASE_PATH);
$dotenv->load();

// Set headers
header('Content-Type: application/json');

// Initialize database connection
App\Database\Database::setConfig([
    'host' => $_ENV['DB_HOST'],
    'dbname' => $_ENV['DB_NAME'],
    'username' => $_ENV['DB_USER'],
    'password' => $_ENV['DB_PASS']
]);

// Initialize router
App\Helpers\Router::group('/', function() {
    // Define routes
    App\Helpers\Router::get('/todos', [App\Controllers\TodoController::class, 'index']);
    App\Helpers\Router::get('/todos/{id}', [App\Controllers\TodoController::class, 'show']);
    App\Helpers\Router::post('/todos', [App\Controllers\TodoController::class, 'store']);
    App\Helpers\Router::put('/todos/{id}', [App\Controllers\TodoController::class, 'update']);
    App\Helpers\Router::patch('/todos/{id}/status', [App\Controllers\TodoController::class, 'updateStatus']);
    App\Helpers\Router::delete('/todos/{id}', [App\Controllers\TodoController::class, 'destroy']);
    App\Helpers\Router::get('/todos/search', [App\Controllers\TodoController::class, 'search']);

    App\Helpers\Router::get('/categories', [App\Controllers\CategoryController::class, 'index']);
    App\Helpers\Router::get('/categories/{id}', [App\Controllers\CategoryController::class, 'show']);
    App\Helpers\Router::post('/categories', [App\Controllers\CategoryController::class, 'store']);
    App\Helpers\Router::put('/categories/{id}', [App\Controllers\CategoryController::class, 'update']);
    App\Helpers\Router::delete('/categories/{id}', [App\Controllers\CategoryController::class, 'destroy']);
    App\Helpers\Router::get('/categories/{id}/todos', [App\Controllers\CategoryController::class, 'todos']);
});

// Handle request
App\Helpers\Router::dispatch();