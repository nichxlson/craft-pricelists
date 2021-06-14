<?php

namespace nichxlson\pricelists\variables;

use nichxlson\pricelists\Pricelists;

class PricelistsVariable
{
    public function pricelists() {
        return Pricelists::getInstance()->pricelistService->getPricelistsForUser();
    }

    public function products() {
        return Pricelists::getInstance()->pricelistService->getProductsForUser();
    }
}