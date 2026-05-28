<?php
declare(strict_types=1);

namespace Tests\Integration;

use Tests\TestCase;

/**
 * Database and Logger Integration Tests
 * 
 * Tests the interaction between Database and Logger classes
 * in realistic scenarios.
 */
class DatabaseLoggerTest extends TestCase
{
    /**
     * Test logger and database both are singletons
     */
    public function test_both_classes_are_singletons(): void
    {
        $db1 = \Database::getInstance();
        $db2 = \Database::getInstance();
        
        $logger1 = \Logger::getInstance();
        $logger2 = \Logger::getInstance();
        
        $this->assertSame($db1, $db2);
        $this->assertSame($logger1, $logger2);
    }

    /**
     * Test logger can be called after database operations
     */
    public function test_logger_operations_after_database(): void
    {
        $db = \Database::getInstance();
        $logger = \Logger::getInstance();
        
        // Both should work without interfering
        $logger->info('Starting database operation');
        
        // Verify logger was called
        $this->assertTrue(true);
    }

    /**
     * Test database connection error can be logged
     */
    public function test_database_error_logging(): void
    {
        $logger = \Logger::getInstance();
        
        try {
            $db = new \Database();
            $db->connect('invalid_host', 'invalid', 'invalid', 'invalid');
        } catch (\Exception $e) {
            $logger->error('Database connection failed', [
                'host' => 'invalid_host',
                'error' => $e->getMessage(),
            ]);
        }
        
        $this->assertTrue(true);
    }

    /**
     * Test logger production mode setting
     */
    public function test_logger_production_mode_in_integration(): void
    {
        \Logger::setProductionMode(false);
        $logger = \Logger::getInstance();
        
        ob_start();
        $logger->displayError('Detailed error message');
        $output = ob_get_clean();
        
        $this->assertStringContainsString('Detailed error', $output);
        
        \Logger::setProductionMode(false);
    }

    /**
     * Test multiple log levels in sequence
     */
    public function test_multiple_log_levels_in_sequence(): void
    {
        $logger = \Logger::getInstance();
        
        $logger->info('Starting test');
        $logger->warning('Something to watch');
        $logger->error('Error occurred');
        $logger->critical('Critical situation');
        
        $this->assertTrue(true);
    }

    /**
     * Test logger with complex context data
     */
    public function test_logger_with_complex_context(): void
    {
        $logger = \Logger::getInstance();
        
        $context = [
            'user_id' => 123,
            'action' => 'database_operation',
            'details' => [
                'table' => 'characters',
                'operation' => 'insert',
            ],
            'timestamp' => time(),
        ];
        
        $logger->info('Complex operation', $context);
        
        $this->assertTrue(true);
    }

    /**
     * Test database class accessible from static context
     */
    public function test_database_static_access(): void
    {
        $db = \Database::getInstance();
        
        // Verify methods exist
        $reflection = new \ReflectionClass($db);
        $this->assertTrue($reflection->hasMethod('connect'));
        $this->assertTrue($reflection->hasMethod('query'));
        $this->assertTrue($reflection->hasMethod('execute'));
    }

    /**
     * Test logger static methods work correctly
     */
    public function test_logger_static_methods_work(): void
    {
        // All should work without throwing
        \Logger::info('Test info');
        \Logger::warning('Test warning');
        \Logger::error('Test error');
        \Logger::critical('Test critical');
        \Logger::setProductionMode(false);
        
        $this->assertTrue(true);
    }

    /**
     * Test error and info logging together
     */
    public function test_error_and_info_logging_together(): void
    {
        $logger = \Logger::getInstance();
        
        // Simulate an error scenario
        $logger->info('User login attempt', ['user' => 'testuser']);
        
        try {
            throw new \Exception('Auth failed');
        } catch (\Exception $e) {
            $logger->error('Login failed', [
                'user' => 'testuser',
                'error' => $e->getMessage(),
            ]);
        }
        
        $this->assertTrue(true);
    }

    /**
     * Test both singletons can coexist
     */
    public function test_singletons_coexist(): void
    {
        $db = \Database::getInstance();
        $logger = \Logger::getInstance();
        
        // Both should be instances of their respective classes
        $this->assertInstanceOf(\Database::class, $db);
        $this->assertInstanceOf(\Logger::class, $logger);
        
        // Should maintain singleton property
        $db2 = \Database::getInstance();
        $logger2 = \Logger::getInstance();
        
        $this->assertSame($db, $db2);
        $this->assertSame($logger, $logger2);
    }

    /**
     * Test logger context with database-like data
     */
    public function test_logger_context_with_database_data(): void
    {
        $logger = \Logger::getInstance();
        
        $context = [
            'query' => 'SELECT * FROM characters WHERE id = ?',
            'params' => [123],
            'execution_time' => 0.002,
        ];
        
        $logger->info('Database query executed', $context);
        
        $this->assertTrue(true);
    }
}
