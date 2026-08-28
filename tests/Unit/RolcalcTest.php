<?php
declare(strict_types=1);

namespace Tests\Unit;

use Tests\TestCase;

/**
 * Test Suite for cExpressionParser Class (rolcalc.php)
 * 
 * Tests mathematical expression parsing, functions,
 * dice rolling mechanics, and base conversions.
 */
class RolcalcTest extends TestCase
{
    protected \cExpressionParser $parser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->parser = new \cExpressionParser();
    }

    /**
     * Test basic arithmetic operations
     */
    public function test_basic_arithmetic(): void
    {
        $this->assertEquals(5, $this->parser->Evaluate("2 + 3"));
        $this->assertEquals(7, $this->parser->Evaluate("10 - 3"));
        $this->assertEquals(24, $this->parser->Evaluate("6 * 4"));
        $this->assertEquals(4, $this->parser->Evaluate("12 / 3"));
        $this->assertEquals(2, $this->parser->Evaluate("14 % 4"));
    }

    /**
     * Test operator precedence and parenthesis
     */
    public function test_operator_precedence_and_parenthesis(): void
    {
        // 2 + 3 * 4 should be 14, not 20
        $this->assertEquals(14, $this->parser->Evaluate("2 + 3 * 4"));
        
        // (2 + 3) * 4 should be 20
        $this->assertEquals(20, $this->parser->Evaluate("(2 + 3) * 4"));

        // Power operator ^
        $this->assertEquals(8, $this->parser->Evaluate("2 ^ 3"));
        $this->assertEquals(17, $this->parser->Evaluate("1 + 2 ^ 4"));
    }

    /**
     * Test bracket floor [...] and absolute value |...|
     */
    public function test_brackets_and_absolute_value(): void
    {
        $this->assertEquals(3, $this->parser->Evaluate("[3.75]"));
        $this->assertEquals(-4, $this->parser->Evaluate("[-3.25]"));
        $this->assertEquals(5, $this->parser->Evaluate("|-5|"));
        $this->assertEquals(5, $this->parser->Evaluate("|5|"));
        $this->assertEquals(7, $this->parser->Evaluate("|3 - 10|"));
    }

    /**
     * Test variables and assignment
     */
    public function test_variables_and_assignments(): void
    {
        $this->assertEquals(10, $this->parser->Evaluate("x = 10"));
        $this->assertEquals(20, $this->parser->Evaluate("x * 2"));
        $this->assertEquals(15, $this->parser->Evaluate("x += 5"));
        $this->assertEquals(15, $this->parser->Evaluate("x"));
        $this->assertEquals(12, $this->parser->Evaluate("x -= 3"));
        $this->assertEquals(24, $this->parser->Evaluate("x *= 2"));
        $this->assertEquals(6, $this->parser->Evaluate("x /= 4"));
    }

    /**
     * Test predefined constants
     */
    public function test_predefined_constants(): void
    {
        $this->assertEqualsWithDelta(M_PI, $this->parser->Evaluate("PI"), 0.0001);
        $this->assertEqualsWithDelta(M_E, $this->parser->Evaluate("E"), 0.0001);
        $this->assertEquals(1.0, $this->parser->Evaluate("TRUE"));
        $this->assertEquals(0.0, $this->parser->Evaluate("FALSE"));
    }

    /**
     * Test relational and equality comparisons
     */
    public function test_relational_and_equality_operators(): void
    {
        $this->assertEquals(1.0, $this->parser->Evaluate("5 == 5"));
        $this->assertEquals(0.0, $this->parser->Evaluate("5 == 6"));
        $this->assertEquals(1.0, $this->parser->Evaluate("5 != 6"));
        $this->assertEquals(1.0, $this->parser->Evaluate("5 <> 6"));
        $this->assertEquals(1.0, $this->parser->Evaluate("3 < 5"));
        $this->assertEquals(0.0, $this->parser->Evaluate("5 < 3"));
        $this->assertEquals(1.0, $this->parser->Evaluate("5 <= 5"));
        $this->assertEquals(1.0, $this->parser->Evaluate("7 > 4"));
        $this->assertEquals(1.0, $this->parser->Evaluate("4 >= 4"));
    }

    /**
     * Test logical operators
     */
    public function test_logical_operators(): void
    {
        $this->assertEquals(1.0, $this->parser->Evaluate("1 AND 1"));
        $this->assertEquals(0.0, $this->parser->Evaluate("1 AND 0"));
        $this->assertEquals(1.0, $this->parser->Evaluate("1 OR 0"));
        $this->assertEquals(0.0, $this->parser->Evaluate("0 OR 0"));
        $this->assertEquals(1.0, $this->parser->Evaluate("1 XOR 0"));
        $this->assertEquals(0.0, $this->parser->Evaluate("1 XOR 1"));
        $this->assertEquals(0.0, $this->parser->Evaluate("NOT 1"));
        $this->assertEquals(1.0, $this->parser->Evaluate("NOT 0"));
    }

    /**
     * Test bitwise operators
     */
    public function test_bitwise_operators(): void
    {
        // & = AND, @ = OR, \ = XOR, { = shift left, } = shift right
        $this->assertEquals(1, $this->parser->Evaluate("5 & 3")); // 101 & 011 = 001
        $this->assertEquals(7, $this->parser->Evaluate("5 @ 3")); // 101 | 011 = 111
        $this->assertEquals(6, $this->parser->Evaluate("5 \\ 3")); // 101 ^ 011 = 110
        $this->assertEquals(16, $this->parser->Evaluate("4 { 2")); // 4 << 2 = 16
        $this->assertEquals(4, $this->parser->Evaluate("16 } 2")); // 16 >> 2 = 4
    }

    /**
     * Test mathematical helper functions
     */
    public function test_math_functions(): void
    {
        $this->assertEquals(2, $this->parser->Evaluate("SQRT(4)"));
        $this->assertEquals(4, $this->parser->Evaluate("CEIL(3.2)"));
        $this->assertEquals(3, $this->parser->Evaluate("FLOOR(3.8)"));
        $this->assertEquals(4, $this->parser->Evaluate("RND(3.6)"));
        $this->assertEquals(3, $this->parser->Evaluate("RND(3.2)"));
        $this->assertEquals(1, $this->parser->Evaluate("SGN(42)"));
        $this->assertEquals(-1, $this->parser->Evaluate("SGN(-15)"));
        $this->assertEquals(0, $this->parser->Evaluate("SGN(0)"));
        $this->assertEquals(10, $this->parser->Evaluate("MAX(10, 4)"));
        $this->assertEquals(4, $this->parser->Evaluate("MIN(10, 4)"));
        $this->assertEqualsWithDelta(0.75, $this->parser->Evaluate("FRAC(3.75)"), 0.0001);
    }

    /**
     * Test trigonometric functions and angle units
     */
    public function test_trigonometric_functions(): void
    {
        // Degrees (default)
        $this->parser->SetAngleUnit(ANGLE_DEGREES);
        $this->assertEqualsWithDelta(0.5, $this->parser->Evaluate("SIN(30)"), 0.0001);
        $this->assertEqualsWithDelta(0.5, $this->parser->Evaluate("COS(60)"), 0.0001);
        $this->assertEqualsWithDelta(1.0, $this->parser->Evaluate("TAN(45)"), 0.0001);
        $this->assertEqualsWithDelta(45, $this->parser->Evaluate("ARCTAN(1)"), 0.0001);

        // Radians
        $this->parser->SetAngleUnit(ANGLE_RADIANS);
        $this->assertEqualsWithDelta(0.0, $this->parser->Evaluate("SIN(PI)"), 0.0001);
        $this->assertEqualsWithDelta(-1.0, $this->parser->Evaluate("COS(PI)"), 0.0001);
    }

    /**
     * Test base conversion static methods and inline literals
     */
    public function test_base_conversions(): void
    {
        $this->assertEquals("1010_2", \cExpressionParser::ConvertToBase(10, 2));
        $this->assertEquals("FF_16", \cExpressionParser::ConvertToBase(255, 16));
        $this->assertEquals(10, \cExpressionParser::ConvertFromBase("1010", 2));
        $this->assertEquals(255, \cExpressionParser::ConvertFromBase("FF", 16));

        // Inline base constant tokens
        $this->assertEquals(10, $this->parser->Evaluate("1010_2"));
        $this->assertEquals(255, $this->parser->Evaluate("FF_16"));
    }

    /**
     * Test RAN(x) function produces values within 1..x
     */
    public function test_ran_function(): void
    {
        for ($i = 0; $i < 20; $i++) {
            $val = $this->parser->Evaluate("RAN(6)");
            $this->assertGreaterThanOrEqual(1, $val);
            $this->assertLessThanOrEqual(6, $val);
        }
    }

    /**
     * Test DICE(count, sides) rolls sum of multiple dice
     */
    public function test_dice_rolls_multiple_dice(): void
    {
        // 3d6 must produce values between 3 and 18
        $values = [];
        for ($i = 0; $i < 30; $i++) {
            $val = (int) $this->parser->Evaluate("DICE(3, 6)");
            $values[] = $val;
            $this->assertGreaterThanOrEqual(3, $val, "DICE(3, 6) returned less than minimum 3: {$val}");
            $this->assertLessThanOrEqual(18, $val, "DICE(3, 6) returned greater than maximum 18: {$val}");
        }

        // Over 30 rolls of 3d6, average should be close to 10.5, and at least some values should be > 6
        $hasGreaterThanSix = false;
        foreach ($values as $v) {
            if ($v > 6) {
                $hasGreaterThanSix = true;
                break;
            }
        }
        $this->assertTrue($hasGreaterThanSix, "DICE(3, 6) should roll 3 dice (scores > 6 expected)");
    }

    /**
     * Test XDICE complex roll function
     */
    public function test_xdice_function(): void
    {
        // XDICE(rollCount, diceCount, diceSize, highCount, minRoll)
        // 4 rolls of 1d6, take highest 3, min roll 1
        for ($i = 0; $i < 20; $i++) {
            $val = (int) $this->parser->Evaluate("XDICE(4, 1, 6, 3, 1)");
            $this->assertGreaterThanOrEqual(3, $val);
            $this->assertLessThanOrEqual(18, $val);
        }
    }
}
