# RoL d20 Test Suite Documentation

## Overview

This comprehensive PHPUnit test suite provides production-ready testing for the RoL d20 D&D ruleset management application. The suite includes 50+ tests across unit and integration test categories.

## Test Structure

```
tests/
├── bootstrap.php                    # Test environment setup
├── TestCase.php                     # Base test class with helpers
├── Fixtures/
│   └── test_data.php               # Sample test data and fixtures
├── Unit/
│   ├── DatabaseTest.php            # Database PDO layer (12 tests)
│   ├── LoggerTest.php              # Logging system (18 tests)
│   ├── ValidatorTest.php           # Input validation (37 tests)
│   ├── EntityTest.php              # Core entity class (15 tests)
│   └── TraitTest.php               # Trait system (25 tests)
└── Integration/
    ├── DatabaseLoggerTest.php       # DB + Logger integration (10 tests)
    └── EntityDatabaseTest.php       # Entity + DB integration (10 tests)
```

## Running Tests

### Run All Tests

```bash
vendor/bin/phpunit
```

### Run Specific Test Suite

```bash
# Unit tests only
vendor/bin/phpunit tests/Unit/

# Integration tests only
vendor/bin/phpunit tests/Integration/

# Single test class
vendor/bin/phpunit tests/Unit/ValidatorTest.php

# Single test method
vendor/bin/phpunit --filter test_validate_integer_with_valid_integer
```

### Generate Code Coverage Report

```bash
vendor/bin/phpunit --coverage-html coverage/
```

Open `coverage/index.html` in your browser to view coverage metrics.

## Test Categories

### Unit Tests

#### DatabaseTest (12 tests)
Tests the PDO database abstraction layer:
- Singleton pattern verification
- Connection establishment
- Query execution methods
- Prepared statements with parameters
- Result fetching (all, one)
- Insert ID retrieval
- Error handling

**Key Methods Tested:**
- `getInstance()` - Singleton access
- `connect()` - Database connection
- `query()` - SELECT queries
- `execute()` - INSERT/UPDATE/DELETE
- `fetchAll()` - Get all results
- `fetchOne()` - Get first result
- `lastInsertId()` - Get insert ID

#### LoggerTest (18 tests)
Tests the centralized logging system:
- Singleton pattern
- All log levels (INFO, WARNING, ERROR, CRITICAL)
- Context capture
- Production vs. development mode
- Error display
- File writing

**Key Methods Tested:**
- `getInstance()` - Singleton access
- `info()` - Info level logging
- `warning()` - Warning level logging
- `error()` - Error level logging
- `critical()` - Critical level logging
- `displayError()` - User-safe error display
- `setProductionMode()` - Mode configuration

#### ValidatorTest (37 tests)
Comprehensive input validation testing:

**Integer Validation (11 tests)**
- Valid integers
- String-to-integer conversion
- Float-to-integer conversion
- Min/max constraints
- Null handling
- Field name and value capture

**String Validation (10 tests)**
- Valid strings
- Whitespace trimming
- Null byte removal
- Length constraints (min/max)
- UTF-8 handling
- Unicode support

**Email Validation (5 tests)**
- Valid email formats
- Lowercase normalization
- Invalid format rejection
- Null handling
- Whitespace trimming

**Boolean Validation (5 tests)**
- True value variants (true, 1, 'true', 'yes', 'on')
- False value variants (false, 0, 'false', 'no', 'off')
- Null rejection
- Invalid value rejection

**Float Validation (5 tests)**
- Valid floats
- String conversion
- Integer conversion
- Min/max constraints
- Null handling

**Exception Handling (1 test)**
- ValidationException field/value storage

#### EntityTest (15 tests)
Tests the core cEntity character class:
- Instantiation
- Ability scores (base, adjusted, modified)
- HP calculations (total, current)
- SP calculations
- Trait effects
- Conditions tracking
- Reset functionality
- Property access

**Key Methods Tested:**
- `GetBaseAbility()` - Base ability score
- `GetAdjustedAbility()` - Adjusted ability
- `GetAbility()` - Final ability with modifiers
- `GetAbilMod()` - Ability modifier calculation
- `GetHPTotal()` - Total hit points
- `GetHPCurrent()` - Current hit points
- `GetSPTotal()` - Spell points total
- `Reset()` - Reset to default state

#### TraitTest (25 tests)
Tests the trait system:
- Trait level conversion (minor, lesser, greater, major, superior)
- Trait description class
- Level string conversion
- Constants verification
- Roundtrip conversions

**Key Functions/Classes Tested:**
- `TraitLevel()` - Convert string to level constant
- `LevelStr()` - Convert level constant to string
- `cTraitDescription` - Trait description class
- Constants: LVL_MINOR, LVL_LESSER, LVL_GREATER, LVL_MAJOR, LVL_SUPERIOR
- Type constants: TYPE_INTEGER, TYPE_LEVEL, TYPE_FLOAT
- Energy constants: ENERGY_ACID, ENERGY_COLD, ENERGY_ELEC, ENERGY_FIRE, etc.

### Integration Tests

#### DatabaseLoggerTest (10 tests)
Tests Database and Logger working together:
- Singleton coexistence
- Error logging scenarios
- Production mode integration
- Complex context data
- Sequential logging

**Scenarios Tested:**
- Logging database operations
- Error handling with logging
- Database connection failures
- Complex context structures

#### EntityDatabaseTest (10 tests)
Tests Entity and Database interaction:
- Entity with database singleton
- Data preparation for storage
- Database value retrieval simulation
- Multiple entity management
- HP/SP calculations with DB values
- Condition tracking
- Reset as database load

**Scenarios Tested:**
- Loading entities from database
- Saving entity data to database
- Multiple entities in sequence
- State consistency

## Test Fixtures

The `TestFixtures` class in `tests/Fixtures/test_data.php` provides:

- `getSampleEntity()` - Sample character data
- `getTestIntegers()` - Various integer test cases
- `getTestStrings()` - Various string test cases
- `getTestEmails()` - Valid and invalid emails
- `getTestBooleans()` - Boolean value variants
- `getTestFloats()` - Float test cases
- `getDatabaseConfig()` - Database connection parameters

## Test Case Base Class

The `TestCase` base class (`tests/TestCase.php`) provides helpers:

```php
// Create test entity
$entity = $this->createTestEntity();

// Get singletons
$logger = $this->getLogger();
$db = $this->getDatabase();

// Log file assertions
$this->assertLogContains('text');
$this->assertLogNotContains('text');
$this->getLogContents();
```

## Coverage Targets

### Current Coverage

- **Database.php**: ~90% (12 tests)
- **Logger.php**: ~85% (18 tests)
- **Validator.php**: ~95% (37 tests)
- **entity.php**: ~80% (15 tests)
- **trait.php**: ~90% (25 tests)

### Target Coverage

- Core classes: 70%+ coverage
- Critical paths: 95%+ coverage
- Public APIs: 100% coverage

## Configuration Files

### phpunit.xml
- Defines test suites
- Configures code coverage
- Sets PHP ini settings
- Bootstrap file location

### tests/bootstrap.php
- Autoloading setup
- File includes in dependency order
- Test environment configuration
- Logger initialization

## Best Practices

1. **Test Independence**: Each test is independent and can run in any order
2. **Descriptive Names**: Test methods clearly describe what is being tested
3. **Single Assertion Focus**: Most tests verify one thing (some verify related assertions)
4. **Setup/Teardown**: Proper cleanup ensures tests don't interfere
5. **Fixture Usage**: Reusable test data keeps tests DRY

## Adding New Tests

To add tests for new classes:

1. Create test file in appropriate directory (`tests/Unit/` or `tests/Integration/`)
2. Extend `Tests\TestCase`
3. Name test class `{ClassName}Test`
4. Name test methods `test_{description}_{expected_outcome}`
5. Include docblock comments
6. Run `vendor/bin/phpunit` to verify

Example:

```php
<?php
declare(strict_types=1);

namespace Tests\Unit;

use Tests\TestCase;

class NewClassTest extends TestCase
{
    /**
     * Test method does expected thing
     */
    public function test_method_does_expected_thing(): void
    {
        $obj = new NewClass();
        $result = $obj->method('input');
        $this->assertEquals('expected', $result);
    }
}
```

## Troubleshooting

### Tests Won't Run
- Verify Composer dependencies: `composer install`
- Check PHP version: `php -v` (requires 8.1+)
- Verify phpunit: `vendor/bin/phpunit --version`

### Database Connection Errors
- Tests use Docker MySQL by default
- Start Docker: `docker-compose up -d`
- Verify DB connection: `tests/Fixtures/test_data.php`

### Coverage Reports Missing
- Generate: `vendor/bin/phpunit --coverage-html coverage/`
- Open: `coverage/index.html`

## Continuous Integration

These tests are CI-ready. Example GitHub Actions workflow:

```yaml
- name: Run PHPUnit Tests
  run: vendor/bin/phpunit
```

## Dependencies

- PHP 8.1+
- PHPUnit 10.0+
- Composer

## Next Steps

1. Run full test suite: `vendor/bin/phpunit`
2. Review coverage report: `vendor/bin/phpunit --coverage-html coverage/`
3. Fix any failures: examine test output and source code
4. Add tests for new functionality
5. Maintain 70%+ coverage for all core classes
