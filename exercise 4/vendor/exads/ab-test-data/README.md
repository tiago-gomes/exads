# A/B Test Data
In order to use this library:

1. Install it using composer:
```bash
composer require exads/ab-test-data
```

2. Use it in your class:
```php
<?php

namespace MyNamespace;

use Exads\ABTestData;

class MyClass
{
  public function getData(int $promoId): array
  {
    $abTest = new ABTestData($promoId);
    $promotion = $abTest->getPromotionName();
    $designs = $abTest->getAllDesigns();
    // ...
    return array_map(function ($item) {
      // ...
    }, $designs);
  }
}
```

There are 3 A/B Test promotions that can be accessed via this class.
 Feel free to use any of them (1, 2, 3).

Designs come in the following format:
```php
$designs = [
  [ 'designId' => 1, 'designName' => 'Design 1', 'splitPercent' => 35 ],
  // [ ... ]
];
```
