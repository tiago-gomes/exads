<?php
require_once 'vendor/autoload.php';

use Exads\ABTestData;

class TestAB
{
    public $testAb;

    public function __construct(int $promoId) {
        $this->testAb = new ABTestData($promoId);
    }

    public function getRedirect()
    {
        $allDesigns = $this->testAb->getAllDesigns();
        return array_reduce($allDesigns, function ($a,$b) {
            if (empty($a)) {
                $a['splitPercent'] = $b;
            }
            return $b['splitPercent'] <= $a['splitPercent'] ? $b : $a;
        });
    }
}

?>