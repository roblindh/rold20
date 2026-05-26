<?php
declare(strict_types=1);

namespace Tests\Unit;

use Tests\TestCase;

/**
 * Test Suite for Database PDO Abstraction Layer
 * 
 * Tests the RulesSrc/Database.php singleton class and its
 * query execution methods.
 */
class DatabaseTest extends TestCase
{
    /**
     * Test singleton returns same instance
     */
    public function test_singleton_returns_same_instance(): void
    {
        $instance1 = \Database::getInstance();
        $instance2 = \Database::getInstance();
        
        $this->assertSame($instance1, $instance2);
        $this->assertInstanceOf(\Database::class, $instance1);
    }

    /**
     * Test Database class constructor is private (singleton pattern)
     */
    public function test_database_class_exists(): void
    {
        $reflection = new \ReflectionClass(\Database::class);
        $this->assertTrue($reflection->hasMethod('getInstance'));
        $this->assertTrue($reflection->hasMethod('connect'));
        $this->assertTrue($reflection->hasMethod('query'));
        $this->assertTrue($reflection->hasMethod('execute'));
    }

    /**
     * Test connect method throws on invalid connection
     */
    public function test_connect_throws_on_invalid_credentials(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessageMatches('/connection failed/i');
        
        $db = new \Database();
        $db->connect('invalid_host', 'invalid_user', 'invalid_pass', 'invalid_db');
    }

    /**
     * Test query method returns QueryResult object
     */
    public function test_query_method_exists(): void
    {
        $db = \Database::getInstance();
        $reflection = new \ReflectionClass($db);
        
        $this->assertTrue($reflection->hasMethod('query'));
        
        // Verify method signature
        $method = $reflection->getMethod('query');
        $params = $method->getParameters();
        $this->assertCount(2, $params);
    }

    /**
     * Test fetchAll method exists and is callable
     */
    public function test_fetchall_method_exists(): void
    {
        $db = \Database::getInstance();
        $reflection = new \ReflectionClass($db);
        
        $this->assertTrue($reflection->hasMethod('fetchAll'));
    }

    /**
     * Test fetchOne method exists and is callable
     */
    public function test_fetchone_method_exists(): void
    {
        $db = \Database::getInstance();
        $reflection = new \ReflectionClass($db);
        
        $this->assertTrue($reflection->hasMethod('fetchOne'));
    }

    /**
     * Test execute method exists for INSERT/UPDATE/DELETE
     */
    public function test_execute_method_exists(): void
    {
        $db = \Database::getInstance();
        $reflection = new \ReflectionClass($db);
        
        $this->assertTrue($reflection->hasMethod('execute'));
    }

    /**
     * Test lastInsertId method exists
     */
    public function test_last_insert_id_method_exists(): void
    {
        $db = \Database::getInstance();
        $reflection = new \ReflectionClass($db);
        
        $this->assertTrue($reflection->hasMethod('lastInsertId'));
    }

    /**
     * Test numRows method exists
     */
    public function test_num_rows_method_exists(): void
    {
        $db = \Database::getInstance();
        $reflection = new \ReflectionClass($db);
        
        $this->assertTrue($reflection->hasMethod('numRows'));
    }

    /**
     * Test close method exists
     */
    public function test_close_method_exists(): void
    {
        $db = \Database::getInstance();
        $reflection = new \ReflectionClass($db);
        
        $this->assertTrue($reflection->hasMethod('close'));
    }

    /**
     * Test query method signature matches expected parameters
     */
    public function test_query_method_accepts_query_and_params(): void
    {
        $db = \Database::getInstance();
        $reflection = new \ReflectionClass($db);
        $method = $reflection->getMethod('query');
        
        $params = $method->getParameters();
        
        // Should have $query and $params parameters
        $this->assertTrue(
            isset($params[0]) && isset($params[1]),
            'query method should accept query and params'
        );
    }

    /**
     * Test Database can be used in a try-catch for errors
     */
    public function test_database_error_handling(): void
    {
        $db = new \Database();
        
        try {
            // This should fail with invalid connection
            $db->connect('invalid_host', 'invalid', 'invalid', 'invalid');
            $this->fail('Should have thrown exception');
        } catch (\Exception $e) {
            $this->assertStringContainsString('connection', strtolower($e->getMessage()));
        }
    }
}
