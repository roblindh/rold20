<?php
declare(strict_types=1);

namespace Tests\Unit;

use Tests\TestCase;
use RulesSrc\Validator;
use RulesSrc\ValidationException;

/**
 * Test Suite for Input Validator
 * 
 * Tests the RulesSrc/Validator.php validation system with
 * comprehensive coverage of all validation methods.
 */
class ValidatorTest extends TestCase
{
    // ===== INTEGER VALIDATION =====

    /**
     * Test validate integer with valid integer
     */
    public function test_validate_integer_with_valid_integer(): void
    {
        $result = Validator::integer(42);
        $this->assertIsInt($result);
        $this->assertEquals(42, $result);
    }

    /**
     * Test validate integer converts numeric string
     */
    public function test_validate_integer_converts_numeric_string(): void
    {
        $result = Validator::integer('42');
        $this->assertIsInt($result);
        $this->assertEquals(42, $result);
    }

    /**
     * Test validate integer converts float
     */
    public function test_validate_integer_converts_float(): void
    {
        $result = Validator::integer(42.9);
        $this->assertIsInt($result);
        $this->assertEquals(42, $result);
    }

    /**
     * Test validate integer with minimum constraint
     */
    public function test_validate_integer_with_min_constraint(): void
    {
        $result = Validator::integer(10, 5);
        $this->assertEquals(10, $result);
    }

    /**
     * Test validate integer fails with below minimum
     */
    public function test_validate_integer_throws_below_minimum(): void
    {
        $this->expectException(ValidationException::class);
        Validator::integer(4, 5);
    }

    /**
     * Test validate integer with maximum constraint
     */
    public function test_validate_integer_with_max_constraint(): void
    {
        $result = Validator::integer(10, null, 15);
        $this->assertEquals(10, $result);
    }

    /**
     * Test validate integer fails with above maximum
     */
    public function test_validate_integer_throws_above_maximum(): void
    {
        $this->expectException(ValidationException::class);
        Validator::integer(16, null, 15);
    }

    /**
     * Test validate integer with both min and max
     */
    public function test_validate_integer_with_min_and_max(): void
    {
        $result = Validator::integer(10, 5, 15);
        $this->assertEquals(10, $result);
    }

    /**
     * Test validate integer throws on non-numeric string
     */
    public function test_validate_integer_throws_on_non_numeric_string(): void
    {
        $this->expectException(ValidationException::class);
        Validator::integer('not_a_number');
    }

    /**
     * Test validate integer throws on null
     */
    public function test_validate_integer_throws_on_null(): void
    {
        $this->expectException(ValidationException::class);
        Validator::integer(null);
    }

    /**
     * Test validate integer captures field name in exception
     */
    public function test_validate_integer_captures_field_name(): void
    {
        try {
            Validator::integer('invalid', 0, 100, 'age');
            $this->fail('Should throw ValidationException');
        } catch (ValidationException $e) {
            $this->assertEquals('age', $e->getFieldName());
        }
    }

    /**
     * Test validate integer captures value in exception
     */
    public function test_validate_integer_captures_value(): void
    {
        try {
            Validator::integer('invalid', 0, 100, 'count');
            $this->fail('Should throw ValidationException');
        } catch (ValidationException $e) {
            $this->assertEquals('invalid', $e->getValue());
        }
    }

    // ===== STRING VALIDATION =====

    /**
     * Test validate string with valid string
     */
    public function test_validate_string_with_valid_string(): void
    {
        $result = Validator::string('Hello World');
        $this->assertIsString($result);
        $this->assertEquals('Hello World', $result);
    }

    /**
     * Test validate string trims whitespace
     */
    public function test_validate_string_trims_whitespace(): void
    {
        $result = Validator::string('  padded  ');
        $this->assertEquals('padded', $result);
    }

    /**
     * Test validate string removes null bytes
     */
    public function test_validate_string_removes_null_bytes(): void
    {
        $result = Validator::string("test\0null");
        $this->assertStringNotContainsString("\0", $result);
    }

    /**
     * Test validate string with minimum length
     */
    public function test_validate_string_with_min_length(): void
    {
        $result = Validator::string('Hello', 3);
        $this->assertEquals('Hello', $result);
    }

    /**
     * Test validate string throws below minimum length
     */
    public function test_validate_string_throws_below_min_length(): void
    {
        $this->expectException(ValidationException::class);
        Validator::string('Hi', 3);
    }

    /**
     * Test validate string with maximum length
     */
    public function test_validate_string_with_max_length(): void
    {
        $result = Validator::string('Hello', null, 10);
        $this->assertEquals('Hello', $result);
    }

    /**
     * Test validate string throws above maximum length
     */
    public function test_validate_string_throws_above_max_length(): void
    {
        $this->expectException(ValidationException::class);
        Validator::string('Hello World', null, 5);
    }

    /**
     * Test validate string with both min and max length
     */
    public function test_validate_string_with_min_and_max_length(): void
    {
        $result = Validator::string('Hello', 3, 10);
        $this->assertEquals('Hello', $result);
    }

    /**
     * Test validate string throws on null
     */
    public function test_validate_string_throws_on_null(): void
    {
        $this->expectException(ValidationException::class);
        Validator::string(null);
    }

    /**
     * Test validate string handles UTF-8
     */
    public function test_validate_string_handles_utf8(): void
    {
        $result = Validator::string('café');
        $this->assertStringContainsString('café', $result);
    }

    // ===== EMAIL VALIDATION =====

    /**
     * Test validate valid email
     */
    public function test_validate_email_with_valid_address(): void
    {
        $result = Validator::email('test@example.com');
        $this->assertIsString($result);
        $this->assertEquals('test@example.com', $result);
    }

    /**
     * Test validate email normalizes to lowercase
     */
    public function test_validate_email_normalizes_to_lowercase(): void
    {
        $result = Validator::email('Test@Example.COM');
        $this->assertEquals('test@example.com', $result);
    }

    /**
     * Test validate email throws on invalid format
     */
    public function test_validate_email_throws_invalid_format(): void
    {
        $this->expectException(ValidationException::class);
        Validator::email('not_an_email');
    }

    /**
     * Test validate email throws on null
     */
    public function test_validate_email_throws_on_null(): void
    {
        $this->expectException(ValidationException::class);
        Validator::email(null);
    }

    /**
     * Test validate email trims whitespace
     */
    public function test_validate_email_trims_whitespace(): void
    {
        $result = Validator::email('  test@example.com  ');
        $this->assertEquals('test@example.com', $result);
    }

    // ===== BOOLEAN VALIDATION =====

    /**
     * Test validate boolean true value
     */
    public function test_validate_boolean_true_value(): void
    {
        $this->assertTrue(Validator::boolean(true));
        $this->assertTrue(Validator::boolean(1));
        $this->assertTrue(Validator::boolean('true'));
        $this->assertTrue(Validator::boolean('yes'));
        $this->assertTrue(Validator::boolean('on'));
    }

    /**
     * Test validate boolean false value
     */
    public function test_validate_boolean_false_value(): void
    {
        $this->assertFalse(Validator::boolean(false));
        $this->assertFalse(Validator::boolean(0));
        $this->assertFalse(Validator::boolean('false'));
        $this->assertFalse(Validator::boolean('no'));
        $this->assertFalse(Validator::boolean('off'));
    }

    /**
     * Test validate boolean throws on null
     */
    public function test_validate_boolean_throws_on_null(): void
    {
        $this->expectException(ValidationException::class);
        Validator::boolean(null);
    }

    /**
     * Test validate boolean throws on invalid value
     */
    public function test_validate_boolean_throws_on_invalid(): void
    {
        $this->expectException(ValidationException::class);
        Validator::boolean('maybe');
    }

    // ===== FLOAT VALIDATION =====

    /**
     * Test validate float with valid float
     */
    public function test_validate_float_with_valid_float(): void
    {
        $result = Validator::float(42.5);
        $this->assertIsFloat($result);
        $this->assertEquals(42.5, $result);
    }

    /**
     * Test validate float converts numeric string
     */
    public function test_validate_float_converts_string(): void
    {
        $result = Validator::float('42.5');
        $this->assertIsFloat($result);
        $this->assertEquals(42.5, $result);
    }

    /**
     * Test validate float converts integer
     */
    public function test_validate_float_converts_integer(): void
    {
        $result = Validator::float(42);
        $this->assertIsFloat($result);
        $this->assertEquals(42.0, $result);
    }

    /**
     * Test validate float with minimum constraint
     */
    public function test_validate_float_with_min(): void
    {
        $result = Validator::float(10.5, 5.0);
        $this->assertEquals(10.5, $result);
    }

    /**
     * Test validate float with maximum constraint
     */
    public function test_validate_float_with_max(): void
    {
        $result = Validator::float(10.5, null, 15.0);
        $this->assertEquals(10.5, $result);
    }

    /**
     * Test validate float throws on null
     */
    public function test_validate_float_throws_on_null(): void
    {
        $this->expectException(ValidationException::class);
        Validator::float(null);
    }

    // ===== VALIDATION EXCEPTION =====

    /**
     * Test ValidationException stores field name
     */
    public function test_validation_exception_stores_field_name(): void
    {
        $exception = new ValidationException('Test error', 'username', 'invalid_value');
        $this->assertEquals('username', $exception->getFieldName());
        $this->assertEquals('invalid_value', $exception->getValue());
    }

    /**
     * Test ValidationException with empty field name
     */
    public function test_validation_exception_empty_field_name(): void
    {
        $exception = new ValidationException('Test error');
        $this->assertEquals('', $exception->getFieldName());
    }

    /**
     * Test ValidationException extends Exception
     */
    public function test_validation_exception_extends_exception(): void
    {
        $exception = new ValidationException('Test');
        $this->assertInstanceOf(\Exception::class, $exception);
    }
}
