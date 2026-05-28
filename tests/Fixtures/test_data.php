<?php
declare(strict_types=1);

/**
 * Test Data and Fixtures
 * 
 * Contains sample data used by tests.
 */

class TestFixtures
{
    /**
     * Get sample entity data
     */
    public static function getSampleEntity(): array
    {
        return [
            'name' => 'Test Fighter',
            'class' => 'Fighter',
            'level' => 1,
            'abilities' => [
                'strength' => 15,
                'dexterity' => 14,
                'constitution' => 13,
                'intelligence' => 10,
                'wisdom' => 12,
                'charisma' => 8,
            ],
        ];
    }

    /**
     * Get various test integers
     */
    public static function getTestIntegers(): array
    {
        return [
            'valid' => [5, 10, 100, 1000, -5, 0],
            'strings_valid' => ['5', '10', '100', '-5', '0'],
            'floats_convertible' => [5.0, 10.9, 100.1],
            'invalid' => ['abc', 'hello', 'not_a_number', [], new stdClass()],
        ];
    }

    /**
     * Get various test strings
     */
    public static function getTestStrings(): array
    {
        return [
            'valid' => ['Hello', 'Test String', 'a', 'Character Name'],
            'with_spaces' => ['  padded  ', '  extra  spaces  '],
            'with_nulls' => ["test\0null", "has\0null\0bytes"],
            'unicode' => ['café', '日本語', 'Ñoño'],
            'invalid' => [123, 45.67, true, false, [], new stdClass()],
        ];
    }

    /**
     * Get various test emails
     */
    public static function getTestEmails(): array
    {
        return [
            'valid' => [
                'test@example.com',
                'user.name+tag@domain.co.uk',
                'firstname.lastname@domain.com',
            ],
            'invalid' => [
                'plainaddress',
                'missing@domain',
                'user@',
                '@domain.com',
                'user name@domain.com',
                'user@domain@domain.com',
            ],
        ];
    }

    /**
     * Get various test booleans
     */
    public static function getTestBooleans(): array
    {
        return [
            'true_values' => [true, 1, '1', 'true', 'yes', 'on'],
            'false_values' => [false, 0, '0', 'false', 'no', 'off'],
            'invalid' => ['maybe', 'unknown', 2, -1],
        ];
    }

    /**
     * Get various test floats
     */
    public static function getTestFloats(): array
    {
        return [
            'valid' => [1.5, 10.99, 100.0, 0.1, -5.5],
            'strings_valid' => ['1.5', '10.99', '100.0', '-5.5'],
            'integers_convertible' => [1, 10, 100],
            'invalid' => ['abc', 'not_a_number', true, false, [], new stdClass()],
        ];
    }

    /**
     * Get database connection parameters for testing
     */
    public static function getDatabaseConfig(): array
    {
        return [
            'host' => $_ENV['DB_HOST'] ?? 'db',
            'user' => $_ENV['DB_USER'] ?? 'rold20_user',
            'password' => $_ENV['DB_PASSWORD'] ?? 'rold20_pass',
            'database' => $_ENV['DB_NAME'] ?? 'rold20_test',
        ];
    }
}
