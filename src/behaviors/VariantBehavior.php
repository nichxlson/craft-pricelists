<?php

namespace nichxlson\pricelists\behaviors;

use craft\commerce\helpers\Currency;
use nichxlson\pricelists\Pricelists;
use yii\base\Behavior;

class VariantBehavior extends Behavior
{
    public function getPricelistPrice() {
        return Pricelists::getInstance()->pricelistService->getPricelistPriceForProduct($this->owner->id);
    }

    public function getPricelistPriceAsCurrency() {
        $pricelistPrice = $this->getPricelistPrice();

        if(!$pricelistPrice) {
            return null;
        }

        return Currency::formatAsCurrency($pricelistPrice, null, false, true, false);
    }
}