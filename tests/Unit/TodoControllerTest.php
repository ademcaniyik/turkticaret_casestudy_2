<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Database\Database;
use App\Controllers\TodoController;
use App\Models\Todo;
use PDO;

class TodoControllerTest extends TestCase
{
    private TodoController $controller;
    private PDO $db;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Database bağlantısını başlat
        Database::setConfig([
            'host' => 'localhost',
            'dbname' => 'acd1f4ftwarecom_turkticaret',
            'username' => 'acd1f4ftwarecom_root',
            'password' => 'acdi\'root321.'
        ]);
        $this->db = Database::getConnection();
        
        // Mevcut verileri temizle
        $this->db->exec("DELETE FROM todo_category");
        $this->db->exec("DELETE FROM categories");
        $this->db->exec("DELETE FROM todos");

        // Controller'ı başlat
        $this->controller = new TodoController();
    }

    public function testCreateTodo(): void
    {
        $data = [
            'title' => 'Test Todo',
            'description' => 'This is a test todo',
            'status' => 'pending',
            'priority' => 'medium',
            'due_date' => date('Y-m-d H:i:s', strtotime('+1 day'))
        ];

        $todo = $this->controller->store($data);
        $this->assertNotNull($todo, 'Todo should not be null');
        $this->assertInstanceOf(Todo::class, $todo, 'Result should be a Todo instance');
        $this->assertEquals('Test Todo', $todo->title, 'Todo title should match');
        $this->assertEquals('pending', $todo->status, 'Todo status should match');
        $this->assertEquals('medium', $todo->priority, 'Todo priority should match');
        $this->assertEquals(date('Y-m-d H:i:s', strtotime('+1 day')), $todo->due_date, 'Todo due date should match');
    }

    public function testUpdateTodo(): void
    {
        $todo = $this->controller->store([
            'title' => 'Test Todo',
            'description' => 'This is a test todo',
            'status' => 'pending',
            'priority' => 'medium',
            'due_date' => date('Y-m-d H:i:s', strtotime('+1 day'))
        ]);

        $this->assertNotNull($todo, 'Todo should not be null');
        $this->assertInstanceOf(Todo::class, $todo, 'Result should be a Todo instance');

        $updatedTodo = $this->controller->update($todo->id, [
            'title' => 'Updated Todo',
            'description' => 'This is an updated test todo',
            'status' => 'in_progress',
            'priority' => 'high',
            'due_date' => date('Y-m-d H:i:s', strtotime('+2 days'))
        ]);

        $this->assertNotNull($updatedTodo, 'Updated todo should not be null');
        $this->assertInstanceOf(Todo::class, $updatedTodo, 'Result should be a Todo instance');
        $this->assertEquals('Updated Todo', $updatedTodo->title, 'Todo title should be updated');
        $this->assertEquals('in_progress', $updatedTodo->status, 'Todo status should be updated');
        $this->assertEquals('high', $updatedTodo->priority, 'Todo priority should be updated');
    }

    public function testDeleteTodo(): void
    {
        $todo = $this->controller->store([
            'title' => 'Test Todo',
            'description' => 'This is a test todo',
            'status' => 'pending',
            'priority' => 'medium',
            'due_date' => date('Y-m-d H:i:s', strtotime('+1 day'))
        ]);

        $this->assertNotNull($todo, 'Todo should not be null');
        $this->assertInstanceOf(Todo::class, $todo, 'Result should be a Todo instance');

        $deleted = $this->controller->delete($todo->id);
        $this->assertTrue($deleted, 'Todo should be deleted');

        $deletedTodo = Todo::find($todo->id);
        $this->assertNull($deletedTodo, 'Todo should be deleted');
    }

    public function testGetTodoWithCategories(): void
    {
        // Bir todo oluştur
        $todo = $this->controller->store([
            'title' => 'Test Todo',
            'description' => 'This is a test todo',
            'status' => 'pending',
            'priority' => 'medium',
            'due_date' => date('Y-m-d H:i:s', strtotime('+1 day'))
        ]);

        // Kategorileri oluştur
        $category1 = $this->db->prepare("INSERT INTO categories (name) VALUES (?)");
        $category1->execute(['Category 1']);
        $categoryId1 = $this->db->lastInsertId();
        
        $category2 = $this->db->prepare("INSERT INTO categories (name) VALUES (?)");
        $category2->execute(['Category 2']);
        $categoryId2 = $this->db->lastInsertId();

        // Todo-kategori ilişkilerini oluştur
        $this->db->prepare("INSERT INTO todo_category (todo_id, category_id) VALUES (?, ?)")->execute([$todo->id, $categoryId1]);
        $this->db->prepare("INSERT INTO todo_category (todo_id, category_id) VALUES (?, ?)")->execute([$todo->id, $categoryId2]);

        // Todo'yu getir ve kategorileri kontrol et
        $result = $this->controller->show(['id' => $todo->id]);
        $this->assertNotNull($result, 'Result should not be null');
        $this->assertIsArray($result->categories, 'Todo should have categories');
        $this->assertCount(2, $result->categories, 'Todo should have 2 categories');
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->db->exec("DELETE FROM todo_category");
        $this->db->exec("DELETE FROM categories");
        $this->db->exec("DELETE FROM todos");
    }
}
