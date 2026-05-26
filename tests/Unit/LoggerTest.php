<?php
declare(strict_types=1);

namespace Tests\Unit;

use Tests\TestCase;

/**
 * Test Suite for Centralized Logger
 * 
 * Tests the RulesSrc/Logger.php logging system with
 * various log levels and error handling.
 */
class LoggerTest extends TestCase
{
    /**
     * Test logger singleton pattern
     */
    public function test_logger_singleton_pattern(): void
    {
        $logger1 = \Logger::getInstance();
        $logger2 = \Logger::getInstance();
        
        $this->assertSame($logger1, $logger2);
        $this->assertInstanceOf(\Logger::class, $logger1);
    }

    /**
     * Test info level logging method exists
     */
    public function test_info_level_method_exists(): void
    {
        $reflection = new \ReflectionClass(\Logger::class);
        $this->assertTrue($reflection->hasMethod('info'));
    }

    /**
     * Test warning level logging method exists
     */
    public function test_warning_level_method_exists(): void
    {
        $reflection = new \ReflectionClass(\Logger::class);
        $this->assertTrue($reflection->hasMethod('warning'));
    }

    /**
     * Test error level logging method exists
     */
    public function test_error_level_method_exists(): void
    {
        $reflection = new \ReflectionClass(\Logger::class);
        $this->assertTrue($reflection->hasMethod('error'));
    }

    /**
     * Test critical level logging method exists
     */
    public function test_critical_level_method_exists(): void
    {
        $reflection = new \ReflectionClass(\Logger::class);
        $this->assertTrue($reflection->hasMethod('critical'));
    }

    /**
     * Test displayError method exists
     */
    public function test_display_error_method_exists(): void
    {
        $reflection = new \ReflectionClass(\Logger::class);
        $this->assertTrue($reflection->hasMethod('displayError'));
    }

    /**
     * Test setProductionMode method exists
     */
    public function test_set_production_mode_method_exists(): void
    {
        $reflection = new \ReflectionClass(\Logger::class);
        $this->assertTrue($reflection->hasMethod('setProductionMode'));
    }

    /**
     * Test info method accepts message and context
     */
    public function test_info_accepts_message_and_context(): void
    {
        $logger = \Logger::getInstance();
        
        // Should not throw
        $logger->info('Test message', ['key' => 'value']);
        $this->assertTrue(true);
    }

    /**
     * Test warning method accepts message and context
     */
    public function test_warning_accepts_message_and_context(): void
    {
        $logger = \Logger::getInstance();
        
        // Should not throw
        $logger->warning('Test warning', ['context' => 'data']);
        $this->assertTrue(true);
    }

    /**
     * Test error method accepts message and context
     */
    public function test_error_accepts_message_and_context(): void
    {
        $logger = \Logger::getInstance();
        
        // Should not throw
        $logger->error('Test error', ['error_code' => 500]);
        $this->assertTrue(true);
    }

    /**
     * Test critical method accepts message and context
     */
    public function test_critical_accepts_message_and_context(): void
    {
        $logger = \Logger::getInstance();
        
        // Should not throw
        $logger->critical('Critical failure', ['severity' => 'high']);
        $this->assertTrue(true);
    }

    /**
     * Test production mode can be set
     */
    public function test_production_mode_can_be_set(): void
    {
        $logger = \Logger::getInstance();
        
        // Should not throw
        \Logger::setProductionMode(true);
        $this->assertTrue(true);
        
        \Logger::setProductionMode(false);
        $this->assertTrue(true);
    }

    /**
     * Test displayError method with production mode
     */
    public function test_display_error_in_production_mode(): void
    {
        \Logger::setProductionMode(true);
        
        // Should output generic message
        ob_start();
        \Logger::displayError('Database connection failed: Access denied for user', 'An error occurred');
        $output = ob_get_clean();
        
        $this->assertStringContainsString('An error occurred', $output);
        
        \Logger::setProductionMode(false);
    }

    /**
     * Test displayError method in development mode
     */
    public function test_display_error_in_development_mode(): void
    {
        \Logger::setProductionMode(false);
        
        // Should output detailed message
        ob_start();
        \Logger::displayError('Detailed error message for developers');
        $output = ob_get_clean();
        
        $this->assertStringContainsString('Detailed error message', $output);
    }

    /**
     * Test Logger class has proper structure
     */
    public function test_logger_class_structure(): void
    {
        $reflection = new \ReflectionClass(\Logger::class);
        
        // Verify it has needed methods
        $methods = ['getInstance', 'info', 'warning', 'error', 'critical', 'displayError'];
        foreach ($methods as $method) {
            $this->assertTrue(
                $reflection->hasMethod($method),
                "Logger should have {$method} method"
            );
        }
    }

    /**
     * Test logger methods are static
     */
    public function test_logger_static_methods(): void
    {
        $reflection = new \ReflectionClass(\Logger::class);
        
        $staticMethods = ['getInstance', 'info', 'warning', 'error', 'critical', 'setProductionMode'];
        foreach ($staticMethods as $method) {
            $refMethod = $reflection->getMethod($method);
            $this->assertTrue(
                $refMethod->isStatic(),
                "{$method} should be static"
            );
        }
    }

    /**
     * Test logger info with empty context
     */
    public function test_logger_info_with_empty_context(): void
    {
        $logger = \Logger::getInstance();
        
        // Should handle empty context
        $logger->info('Test message');
        $this->assertTrue(true);
    }

    /**
     * Test logger error with empty context
     */
    public function test_logger_error_with_empty_context(): void
    {
        $logger = \Logger::getInstance();
        
        // Should handle empty context
        $logger->error('Error occurred');
        $this->assertTrue(true);
    }
}
