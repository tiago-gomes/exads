<?php

/**
 * PHP Version 7.4
 *
 * A/B Test Data File Test
 *
 * @category Test_Class
 * @package  Exads\Test
 * @license  GPLv3 https://www.gnu.org/licenses/gpl-3.0.en.html
 */

namespace Exads\Test;

use Exads\{CustomCrypt, ABTestData, ABTestException};
use PHPUnit\Framework\TestCase;

/**
 * Class AB Test Data Test Case
 */
class ABTestDataTest extends TestCase
{
    private const ENCRYPTED_DATA_FILE = __DIR__ . '/../data/encrypted-designs.b64gz';
    private const ENCRYPTED_BACKUP = __DIR__ . '/../data/encrypted-backup.b64gz';
    private const PROMOTION_ID = 7;
    private const DESIGN_ZERO_ID = 3;
    private const DESIGN_ONE_ID = 2;

    private static $dataForTest = [
        'promotions' => [
            [
                'id' => self::PROMOTION_ID,
                'name' => 'PHPUnit',
                'designs' => [
                    [
                        'designId' => self::DESIGN_ZERO_ID,
                        'designName' => 'Test A Test',
                        'splitPercent' => 33,
                    ],
                    [
                        'designId' => self::DESIGN_ONE_ID,
                        'designName' => 'Test B Test',
                        'splitPercent' => 33,
                    ],
                ]
            ],
        ]
    ];

    /**
     * Pre Set Up Before Tests
     */
    public static function setUpBeforeClass(): void
    {
        rename(self::ENCRYPTED_DATA_FILE, self::ENCRYPTED_BACKUP);

        file_put_contents(
            self::ENCRYPTED_DATA_FILE,
            CustomCrypt::encrypt(json_encode(self::$dataForTest))
        );
    }

    /**
     * Tear Down After Tests
     */
    public static function tearDownAfterClass(): void
    {
        @unlink(self::ENCRYPTED_DATA_FILE);
        rename(self::ENCRYPTED_BACKUP, self::ENCRYPTED_DATA_FILE);
    }

    /**
     * Test Temporary File
     */
    public function testTemporaryFile(): void
    {
        $this->assertSame(
            file_get_contents(self::ENCRYPTED_DATA_FILE),
            CustomCrypt::encrypt(json_encode(self::$dataForTest))
        );
    }

    /**
     * Validate Data File Structure
     *
     * @dataProvider mainDataProvider
     */
    public function testValidDataStructure(array $abTestData): void
    {
        $this->assertArrayHasKey('promotions', $abTestData);
        $this->assertNotEmpty($abTestData['promotions']);

        foreach ($abTestData['promotions'] as $promotion) {
            $this->assertArrayHasKey('id', $promotion);
            $this->assertArrayHasKey('name', $promotion);
            $this->assertArrayHasKey('designs', $promotion);

            $this->assertIsInt($promotion['id']);
            $this->assertIsString($promotion['name']);
            $this->assertNotEmpty($promotion['designs']);

            foreach ($promotion['designs'] as $design) {
                $this->assertArrayHasKey('designId', $design);
                $this->assertArrayHasKey('designName', $design);
                $this->assertArrayHasKey('splitPercent', $design);

                $this->assertIsInt($design['designId']);
                $this->assertIsInt($design['splitPercent']);
                $this->assertIsString($design['designName']);
            }
        }
    }

    /**
     * Main Data Provider
     */
    public function mainDataProvider()
    {
        $productionData = json_decode(CustomCrypt::decrypt(
            file_get_contents(self::ENCRYPTED_DATA_FILE)
        ), true, 8, JSON_THROW_ON_ERROR);

        return [
            'production' => [
                'ab_test_data' => $productionData,
            ],
            'test' => [
                'ab_test_data' => self::$dataForTest,
            ],
        ];
    }

    /**
     * Test Fail Get Promotion.
     */
    public function testFailGetPromotion(): void
    {
        try {
            new ABTestData(0);

            $this->assertTrue(false, 'Promotion ID 0 should be invalid!');
        } catch (ABTestException $e) {
            $this->assertSame('Invalid promotion ID: 0', $e->getMessage());
        }

        $promotionId = self::PROMOTION_ID + 2;
        try {
            new ABTestData($promotionId);

            $this->assertTrue(false, "Promotion ID {$promotionId} should be invalid!");
        } catch (ABTestException $e) {
            $this->assertSame("Invalid promotion ID: {$promotionId}", $e->getMessage());
        }
    }

    /**
     * Test Success Get Promotion
     */
    public function testSuccessGetPromotion(): void
    {
        $promotion = new ABTestData(self::PROMOTION_ID);
        $this->assertSame(self::$dataForTest['promotions'][0]['name'], $promotion->getPromotionName());

        $testDesigns = &self::$dataForTest['promotions'][0]['designs'];
        foreach ($promotion->getAllDesigns() as $index => $design) {
            $this->assertSame($testDesigns[$index]['designId'], $design['designId']);
            $this->assertSame($testDesigns[$index]['designName'], $design['designName']);
            $this->assertSame($testDesigns[$index]['splitPercent'], $design['splitPercent']);
        }
    }

    /**
     * Test Fail Get Design Data
     */
    public function testFailGetDesignData(): void
    {
        $promotion = new ABTestData(self::PROMOTION_ID);

        try {
            $promotion->getDesign(-1);

            $this->assertTrue(false, 'Design ID -1 should be invalid!');
        } catch (ABTestException $e) {
            $this->assertSame('Invalid design ID: -1', $e->getMessage());
        }

        $designId = self::DESIGN_ZERO_ID + self::DESIGN_ONE_ID;
        try {
            $promotion->getDesign($designId);

            $this->assertTrue(false, "Design ID {$designId} should be invalid!");
        } catch (ABTestException $e) {
            $this->assertSame("Invalid design ID: {$designId}", $e->getMessage());
        }
    }

    /**
     * Test Success Get Design Data
     */
    public function testSuccessGetDesignData(): void
    {
        $promotion = new ABTestData(self::PROMOTION_ID);

        $this->assertSame(
            self::$dataForTest['promotions'][0]['designs'][0],
            $promotion->getDesign(self::DESIGN_ZERO_ID)
        );
    }

    /**
     * Test Fail Read Full Data
     */
    public function testFailReadFullData(): void
    {
        rename(self::ENCRYPTED_DATA_FILE, self::ENCRYPTED_DATA_FILE . '.invalid');

        try {
            new ABTestData(self::PROMOTION_ID);

            $this->assertTrue(false, 'Promotion data should not load!');
        } catch (ABTestException $e) {
            $this->assertSame('Could not load designs data!', $e->getMessage());
        } finally {
            rename(self::ENCRYPTED_DATA_FILE . '.invalid', self::ENCRYPTED_DATA_FILE);
        }
    }
}
