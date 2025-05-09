<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Controllers\TodoController;
use App\Controllers\CategoryController;
use App\Helpers\Router;
use App\Database\Database;
use Dotenv\Dotenv;

// .env dosyasını yükle
$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// Veritabanı bağlantısını yapılandır
Database::setConfig([
    'host' => $_ENV['DB_HOST'],
    'database' => $_ENV['DB_NAME'],
    'username' => $_ENV['DB_USER'],
    'password' => $_ENV['DB_PASS']
]);

// Base path tanımı
$basePath = '/turkticaret';

// Todo rotaları
Router::get($basePath . '/todos', [TodoController::class, 'index']);
Router::post($basePath . '/todos', [TodoController::class, 'store']);
Router::get($basePath . '/todos/search', [TodoController::class, 'search']);
Router::get($basePath . '/stats/todos', [TodoController::class, 'stats']);
Router::get($basePath . '/todos/{id}', [TodoController::class, 'show']);
Router::put($basePath . '/todos/{id}', [TodoController::class, 'update']);
Router::patch($basePath . '/todos/{id}/status', [TodoController::class, 'updateStatus']);
Router::delete($basePath . '/todos/{id}', [TodoController::class, 'destroy']);

// Kategori rotaları
Router::get($basePath . '/categories', [CategoryController::class, 'index']);
Router::post($basePath . '/categories', [CategoryController::class, 'store']);
Router::get($basePath . '/categories/{id}', [CategoryController::class, 'show']);
Router::put($basePath . '/categories/{id}', [CategoryController::class, 'update']);
Router::delete($basePath . '/categories/{id}', [CategoryController::class, 'destroy']);
Router::get($basePath . '/categories/{id}/todos', [CategoryController::class, 'todos']);

// Rotaları işle
Router::dispatch();