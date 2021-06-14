<?php

namespace nichxlson\pricelists\behaviors;

use craft\commerce\helpers\Currency;
use nichxlson\pricelists\Pricelists;
use yii\base\Behavior;

class VariantBehavior extends Behavior
{
    public function getPricelistPrice() {
        $variantId = $this->owner->id;
        return Pricelists::getInstance()->pricelistService->getPricelistPriceForProduct($variantId);
    }

    public function getPricelistPriceAsCurrency() {
        $pricelistPrice = $this->getPricelistPrice();

        if(!$pricelistPrice) {
            return null;
        }

        return Currency::formatAsCurrency($pricelistPrice, null, false, true, false);
    }
}