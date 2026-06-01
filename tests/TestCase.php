<?php
declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase as PHPUnitTestCase;

/**
 * Base Test Case Class
 * 
 * Provides common setup/teardown methods and helper functions
 * for all test classes.
 */
class TestCase extends PHPUnitTestCase
{
    protected $testLogFile;
    protected $originalLogDirectory;

    /**
     * Setup before each test
     */
    protected function setUp(): void
    {
        parent::setUp();
        
        // Setup test log directory
        $this->testLogFile = sys_get_temp_dir() . '/rold20_test_' . time() . '.log';
    }

    /**
     * Teardown after each test
     */
    protected function tearDown(): void
    {
        parent::tearDown();
        
        // Clean up test log files
        if (file_exists($this->testLogFile)) {
            unlink($this->testLogFile);
        }
    }

    /**
     * Helper: Create a test entity
     */
    protected function createTestEntity(): \cEntity
    {
        $entity = new \cEntity();
        $entity->Name = 'Test Entity';
        return $entity;
    }

    /**
     * Helper: Get Logger singleton (for testing)
     */
    protected function getLogger(): \Logger
    {
        return \Logger::getInstance();
    }

    /**
     * Helper: Get Database singleton (for testing)
     */
    protected function getDatabase(): \Database
    {
        return \Database::getInstance();
    }

    /**
     * Helper: Assert log file contains text
     */
    protected function assertLogContains(string $text, ?string $logFile = null): void
    {
        $file = $logFile ?? $this->testLogFile;
        
        $this->assertTrue(
            file_exists($file),
            "Log file does not exist: {$file}"
        );

        $content = file_get_contents($file);
        $this->assertStringContainsString(
            $text,
            $content,
            "Log file does not contain expected text: {$text}"
        );
    }

    /**
     * Helper: Assert log file does NOT contain text
     */
    protected function assertLogNotContains(string $text, ?string $logFile = null): void
    {
        $file = $logFile ?? $this->testLogFile;
        
        if (!file_exists($file)) {
            return;
        }

        $content = file_get_contents($file);
        $this->assertStringNotContainsString(
            $text,
            $content,
            "Log file should not contain: {$text}"
        );
    }

    /**
     * Helper: Get log file contents
     */
    protected function getLogContents(?string $logFile = null): string
    {
        $file = $logFile ?? $this->testLogFile;
        
        if (!file_exists($file)) {
            return '';
        }

        return file_get_contents($file);
    }

    /**
     * Helper: Create a ValidationException for testing
     */
    protected function createValidationException(string $message, string $fieldName = '', $value = null): \RulesSrc\ValidationException
    {
        return new \RulesSrc\ValidationException($message, $fieldName, $value);
    }
}
