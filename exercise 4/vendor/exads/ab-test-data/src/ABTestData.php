<?php

/**
 * PHP Version 7.4
 *
 * A/B Test Data
 *
 * @category Domain_Class
 * @package  Exads\Assessment
 * @license  GPLv3 https://www.gnu.org/licenses/gpl-3.0.en.html
 * @link     https://packagist.org/packages/exads/ab-test-data
 */

namespace Exads;

/**
 * A/B Test Data Class
 */
class ABTestData
{
    private const ENCRYPTED_DATA_FILE = __DIR__ . '/../data/encrypted-designs.b64gz';

    private $promotion;
    private $designs;

    /**
     * Instantiate A/B Test Promotion
     *
     * @throws ABTestException
     */
    public function __construct(int $promotionId)
    {
        $this->promotion = $this->findPromotionById($promotionId);
    }

    /**
     * Get Promotion Name
     */
    public function getPromotionName(): ?string
    {
        return $this->promotion['name'] ?? null;
    }

    /**
     * Get All A/B Test Designs
     *
     * @throws ABTestException
     */
    public function getAllDesigns(): array
    {
        if (!is_array($this->designs)) {
            if (empty($this->promotion['designs'])) {
                throw new ABTestException('Could not load design data!');
            }

            $this->designs = $this->promotion['designs'];
        }

        return $this->designs;
    }

    /**
     * Get Design by ID
     *
     * @throws ABTestException
     */
    public function getDesign(int $designId): array
    {
        if ($designId <= 0) {
            throw new ABTestException("Invalid design ID: {$designId}");
        }

        foreach ($this->getAllDesigns() as $design) {
            if ($designId === $design['designId']) {
                return $design;
            }
        }

        throw new ABTestException("Invalid design ID: {$designId}");
    }

    /**
     * Find Promotion By ID
     *
     * @throws ABTestException
     */
    private function findPromotionById(int $promotionId): array
    {
        if ($promotionId <= 0) {
            throw new ABTestException("Invalid promotion ID: {$promotionId}");
        }

        foreach ($this->getFullData() as $promotion) {
            if ($promotionId === $promotion['id']) {
                return $promotion;
            }
        }

        throw new ABTestException("Invalid promotion ID: {$promotionId}");
    }

    /**
     * Get Full Data from encrypted file
     *
     * @throws ABTestException
     */
    private function getFullData(): array
    {
        try {
            $encryptedData = file_get_contents(self::ENCRYPTED_DATA_FILE);
            $decryptedData = CustomCrypt::decrypt($encryptedData);
            $fullData = json_decode($decryptedData, true, 8, JSON_THROW_ON_ERROR);
        } catch (\Throwable $e) {
            throw new ABTestException('Could not load designs data!');
        }

        if (empty($fullData['promotions'])) {
            throw new ABTestException('Could not load promotions data!');
        }

        return $fullData['promotions'];
    }
}
