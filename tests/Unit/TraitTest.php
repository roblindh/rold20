<?php
declare(strict_types=1);

namespace Tests\Unit;

use Tests\TestCase;

/**
 * Test Suite for Trait Processing
 * 
 * Tests the RulesSrc/trait.php trait system for D&D character abilities.
 */
class TraitTest extends TestCase
{
    /**
     * Test cTraitDescription class exists
     */
    public function test_trait_description_class_exists(): void
    {
        $this->assertTrue(class_exists('cTraitDescription'));
    }

    /**
     * Test cTraitDescription instantiation with required parameters
     */
    public function test_trait_description_instantiation(): void
    {
        $trait = new \cTraitDescription('type', 'qual', 'brief', 'full', TYPE_INTEGER);
        $this->assertInstanceOf(\cTraitDescription::class, $trait);
    }

    /**
     * Test cTraitDescription has type property
     */
    public function test_trait_description_has_type_property(): void
    {
        $reflection = new \ReflectionClass(\cTraitDescription::class);
        $this->assertTrue($reflection->hasProperty('type'));
    }

    /**
     * Test cTraitDescription has qual property
     */
    public function test_trait_description_has_qual_property(): void
    {
        $reflection = new \ReflectionClass(\cTraitDescription::class);
        $this->assertTrue($reflection->hasProperty('qual'));
    }

    /**
     * Test cTraitDescription has briefdesc property
     */
    public function test_trait_description_has_briefdesc_property(): void
    {
        $reflection = new \ReflectionClass(\cTraitDescription::class);
        $this->assertTrue($reflection->hasProperty('briefdesc'));
    }

    /**
     * Test TraitLevel function with minor
     */
    public function test_trait_level_with_minor(): void
    {
        $level = TraitLevel('minor');
        $this->assertEquals(LVL_MINOR, $level);
    }

    /**
     * Test TraitLevel function with lesser
     */
    public function test_trait_level_with_lesser(): void
    {
        $level = TraitLevel('lesser');
        $this->assertEquals(LVL_LESSER, $level);
    }

    /**
     * Test TraitLevel function with greater
     */
    public function test_trait_level_with_greater(): void
    {
        $level = TraitLevel('greater');
        $this->assertEquals(LVL_GREATER, $level);
    }

    /**
     * Test TraitLevel function with major
     */
    public function test_trait_level_with_major(): void
    {
        $level = TraitLevel('major');
        $this->assertEquals(LVL_MAJOR, $level);
    }

    /**
     * Test TraitLevel function with superior
     */
    public function test_trait_level_with_superior(): void
    {
        $level = TraitLevel('superior');
        $this->assertEquals(LVL_SUPERIOR, $level);
    }

    /**
     * Test TraitLevel function returns LVL_NONE for unknown
     */
    public function test_trait_level_returns_none_for_unknown(): void
    {
        $level = TraitLevel('unknown');
        $this->assertEquals(LVL_NONE, $level);
    }

    /**
     * Test LevelStr function with minor
     */
    public function test_level_str_with_minor(): void
    {
        $str = LevelStr(LVL_MINOR);
        $this->assertEquals('Minor', $str);
    }

    /**
     * Test LevelStr function with lesser
     */
    public function test_level_str_with_lesser(): void
    {
        $str = LevelStr(LVL_LESSER);
        $this->assertEquals('Lesser', $str);
    }

    /**
     * Test LevelStr function with greater
     */
    public function test_level_str_with_greater(): void
    {
        $str = LevelStr(LVL_GREATER);
        $this->assertEquals('Greater', $str);
    }

    /**
     * Test LevelStr function with major
     */
    public function test_level_str_with_major(): void
    {
        $str = LevelStr(LVL_MAJOR);
        $this->assertEquals('Major', $str);
    }

    /**
     * Test LevelStr function with superior
     */
    public function test_level_str_with_superior(): void
    {
        $str = LevelStr(LVL_SUPERIOR);
        $this->assertEquals('Superior', $str);
    }

    /**
     * Test LevelStr returns empty string for unknown
     */
    public function test_level_str_returns_empty_for_unknown(): void
    {
        $str = LevelStr(LVL_NONE);
        $this->assertEquals('', $str);
    }

    /**
     * Test trait constants are defined
     */
    public function test_trait_level_constants_defined(): void
    {
        $this->assertTrue(defined('LVL_NONE'));
        $this->assertTrue(defined('LVL_MINOR'));
        $this->assertTrue(defined('LVL_LESSER'));
        $this->assertTrue(defined('LVL_GREATER'));
        $this->assertTrue(defined('LVL_MAJOR'));
        $this->assertTrue(defined('LVL_SUPERIOR'));
    }

    /**
     * Test info level constants are defined
     */
    public function test_info_level_constants_defined(): void
    {
        $this->assertTrue(defined('INFO_BRIEF'));
        $this->assertTrue(defined('INFO_STANDARD'));
        $this->assertTrue(defined('INFO_FULL'));
    }

    /**
     * Test type constants are defined
     */
    public function test_type_constants_defined(): void
    {
        $this->assertTrue(defined('TYPE_INTEGER'));
        $this->assertTrue(defined('TYPE_LEVEL'));
        $this->assertTrue(defined('TYPE_FLOAT'));
        $this->assertTrue(defined('TYPE_OTHER'));
    }

    /**
     * Test energy constants are defined
     */
    public function test_energy_constants_defined(): void
    {
        $this->assertTrue(defined('ENERGY_ACID'));
        $this->assertTrue(defined('ENERGY_COLD'));
        $this->assertTrue(defined('ENERGY_ELEC'));
        $this->assertTrue(defined('ENERGY_FIRE'));
        $this->assertTrue(defined('ENERGY_NECRO'));
        $this->assertTrue(defined('ENERGY_RADIANT'));
        $this->assertTrue(defined('ENERGY_SONIC'));
    }

    /**
     * Test TraitLevel with empty string
     */
    public function test_trait_level_with_empty_string(): void
    {
        $level = TraitLevel('');
        $this->assertEquals(LVL_NONE, $level);
    }

    /**
     * Test LevelStr roundtrip conversion
     */
    public function test_level_str_and_trait_level_roundtrip(): void
    {
        $originalLevel = LVL_GREATER;
        $levelStr = LevelStr($originalLevel);
        $convertedLevel = TraitLevel(strtolower($levelStr));
        
        $this->assertEquals($originalLevel, $convertedLevel);
    }

    /**
     * Test cTraitDescription type property can be set
     */
    public function test_trait_description_type_property_settable(): void
    {
        $trait = new \cTraitDescription('original', 'qual', 'brief', 'full', TYPE_INTEGER);
        $this->assertEquals('original', $trait->type);
    }

    /**
     * Test cTraitDescription qual property can be set
     */
    public function test_trait_description_qual_property_settable(): void
    {
        $trait = new \cTraitDescription('type', 'original', 'brief', 'full', TYPE_INTEGER);
        $this->assertEquals('original', $trait->qual);
    }

    /**
     * Test cTraitDescription briefdesc property can be set
     */
    public function test_trait_description_briefdesc_property_settable(): void
    {
        $trait = new \cTraitDescription('type', 'qual', 'original', 'full', TYPE_INTEGER);
        $this->assertEquals('original', $trait->briefdesc);
    }

    /**
     * Test trait level values are distinct
     */
    public function test_trait_level_values_are_distinct(): void
    {
        $levels = [LVL_NONE, LVL_MINOR, LVL_LESSER, LVL_GREATER, LVL_MAJOR, LVL_SUPERIOR];
        $unique = array_unique($levels);
        $this->assertCount(count($levels), $unique, 'All trait levels should have distinct values');
    }
}
