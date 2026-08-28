<?php
declare(strict_types=1);

namespace Tests\Unit;

use Tests\TestCase;

/**
 * Test Suite for cCreature and Creature Image/Text Helpers
 */
class CreatureTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        global $_APP;
        if (!isset($_APP['creatures'])) {
            $_APP['creatures'] = [
                1 => [
                    'ID' => 1,
                    'Name' => 'Human',
                    'StrAdj' => 0,
                    'ConAdj' => 0,
                    'DexAdj' => 0,
                    'IntAdj' => 0,
                    'WisAdj' => 0,
                    'ChaAdj' => 0,
                ],
                2 => [
                    'ID' => 2,
                    'Name' => 'Dromite',
                    'StrAdj' => -2,
                    'ConAdj' => 0,
                    'DexAdj' => 2,
                    'IntAdj' => 0,
                    'WisAdj' => -2,
                    'ChaAdj' => 2,
                ],
            ];
        }
    }

    public function test_get_abil_adj_with_valid_creature(): void
    {
        $this->assertEquals(0, \cCreature::GetAbilAdj(1, A_STR));
        $this->assertEquals(-2, \cCreature::GetAbilAdj(2, A_STR));
        $this->assertEquals(2, \cCreature::GetAbilAdj(2, A_DEX));
    }

    public function test_get_abil_adj_with_null_or_invalid_creature(): void
    {
        $this->assertNull(\cCreature::GetAbilAdj(null, A_STR));
        $this->assertNull(\cCreature::GetAbilAdj(99999, A_STR));
    }

    public function test_get_abil_adj_str_with_null_or_invalid(): void
    {
        $this->assertEquals('', \cCreature::GetAbilAdjStr(null));
        $this->assertEquals('', \cCreature::GetAbilAdjStr(99999));
    }

    public function test_format_text_handles_null_empty_and_newlines(): void
    {
        $this->assertEquals('', format_text(null));
        $this->assertEquals('', format_text(''));
        $this->assertEquals('Hello<br/>World', format_text('Hello\nWorld'));
        $this->assertEquals('Line 1<br/>Line 2<br/>Line 3', format_text('Line 1\nLine 2\nLine 3'));
        $this->assertEquals('Regular text', format_text('Regular text'));
    }

    public function test_get_local_image_path_returns_null_when_not_found(): void
    {
        $this->assertNull(\cCreature::GetLocalImagePath(999999, 'NonExistentCreature'));
    }

    public function test_get_local_image_path_detects_existing_file(): void
    {
        $testImgPath = dirname(__DIR__, 2) . '/images/creatures/999999.png';
        file_put_contents($testImgPath, 'dummy image data');
        
        try {
            $found = \cCreature::GetLocalImagePath(999999);
            $this->assertEquals('images/creatures/999999.png', $found);
        } finally {
            if (file_exists($testImgPath)) {
                unlink($testImgPath);
            }
        }
    }

    public function test_get_resolved_image_url_with_wizards_url(): void
    {
        $raw = 'http://www.wizards.com/dnd/images/MM35_gallery/MM35_PG92.jpg';
        $resolved = \cCreature::GetResolvedImageUrl($raw, 999998);
        $expected = 'https://web.archive.org/web/20160401000000im_/http://www.wizards.com/dnd/images/MM35_gallery/MM35_PG92.jpg';
        $this->assertEquals($expected, $resolved);
    }

    public function test_get_resolved_image_url_with_wikia_url(): void
    {
        $raw = 'https://vignette.wikia.nocookie.net/forgottenrealms/images/3/3d/Monster_Manual_5e_-_Githyanki_-_p160.jpg/revision/latest?cb=20200229181610';
        $resolved = \cCreature::GetResolvedImageUrl($raw, 999998);
        $expected = 'https://static.wikia.nocookie.net/forgottenrealms/images/3/3d/Monster_Manual_5e_-_Githyanki_-_p160.jpg';
        $this->assertEquals($expected, $resolved);
    }

    public function test_get_resolved_image_url_prefers_local_image(): void
    {
        $testImgPath = dirname(__DIR__, 2) . '/images/creatures/999997.jpg';
        file_put_contents($testImgPath, 'dummy image data');

        try {
            $resolved = \cCreature::GetResolvedImageUrl('http://www.wizards.com/dnd/images/test.jpg', 999997);
            $this->assertEquals('images/creatures/999997.jpg', $resolved);
        } finally {
            if (file_exists($testImgPath)) {
                unlink($testImgPath);
            }
        }
    }

    public function test_get_resolved_image_url_handles_null_and_empty(): void
    {
        $this->assertNull(\cCreature::GetResolvedImageUrl(null, 999998));
        $this->assertNull(\cCreature::GetResolvedImageUrl('', 999998));
    }
}

