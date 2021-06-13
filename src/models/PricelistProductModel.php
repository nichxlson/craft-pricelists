<?php

namespace nichxlson\pricelists\models;

use craft\base\Model;
use craft\commerce\elements\Variant;
use nichxlson\pricelists\elements\Pricelist;
use nichxlson\pricelists\records\PricelistProductRecord;

class PricelistProductModel extends Model
{
    public $id;
    public $pricelist;
    public $product;
    public $pricelistPrice;

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getPricelist(): Pricelist {
        return $this->pricelist;
    }

    public function setPricelist(Pricelist $pricelist) {
        $this->pricelist = $pricelist;
    }

    public function getProduct(): Variant {
        return $this->product;
    }

    public function setProduct(Variant $product) {
        $this->product = $product;
    }

    public function toRecord() {
        $record = new PricelistProductRecord();
        $record->setAttribute('id', $this->getId());
        $record->setAttribute('pricelistId', $this->getPricelist()->id);
        $record->setAttribute('productId', $this->getProduct()->id);
        $record->setAttribute('pricelistPrice', $this->pricelistPrice);

        return $record;
    }
}