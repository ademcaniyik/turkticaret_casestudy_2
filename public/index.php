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

// Todo rotaları
Router::get('/api/todos', [TodoController::class, 'index']);
Router::get('/api/todos/{id}', [TodoController::class, 'show']);
Router::post('/api/todos', [TodoController::class, 'store']);
Router::put('/api/todos/{id}', [TodoController::class, 'update']);
Router::patch('/api/todos/{id}/status', [TodoController::class, 'updateStatus']);
Router::delete('/api/todos/{id}', [TodoController::class, 'destroy']);
Router::get('/api/todos/search', [TodoController::class, 'search']);
Router::get('/api/stats/todos', [TodoController::class, 'stats']);

// Kategori rotaları
Router::get('/api/categories', [CategoryController::class, 'index']);
Router::get('/api/categories/{id}', [CategoryController::class, 'show']);
Router::post('/api/categories', [CategoryController::class, 'store']);
Router::put('/api/categories/{id}', [CategoryController::class, 'update']);
Router::delete('/api/categories/{id}', [CategoryController::class, 'destroy']);

// Rotaları işle
Router::dispatch();