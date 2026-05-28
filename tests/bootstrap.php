<?php
declare(strict_types=1);

/**
 * PHPUnit Bootstrap File
 * 
 * Configures the test environment and autoloading for all tests.
 */

// Get project root directory
$projectRoot = dirname(__DIR__);

// Set error reporting for tests
error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');

// Add RulesSrc to include path
set_include_path(
    get_include_path() .
    PATH_SEPARATOR . $projectRoot . '/RulesSrc' .
    PATH_SEPARATOR . $projectRoot . '/tests'
);

// Require core RulesSrc files (in dependency order)
// Global constants first
require_once $projectRoot . '/RulesSrc/global.php';

// Database and Logger classes
require_once $projectRoot . '/RulesSrc/Database.php';
require_once $projectRoot . '/RulesSrc/Logger.php';
require_once $projectRoot . '/RulesSrc/Validator.php';

// Core classes
require_once $projectRoot . '/RulesSrc/entity.php';
require_once $projectRoot . '/RulesSrc/trait.php';
require_once $projectRoot . '/RulesSrc/creature.php';

// Load test base class
require_once $projectRoot . '/tests/TestCase.php';

// Load test fixtures
require_once $projectRoot . '/tests/Fixtures/test_data.php';

// Setup test environment
if (!defined('PHPUNIT_TESTSUITE')) {
    define('PHPUNIT_TESTSUITE', true);
}

// Disable production mode for testing
Logger::setProductionMode(false);
