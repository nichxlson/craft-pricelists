<?php

namespace nichxlson\pricelists\models;

use craft\base\Model;
use craft\elements\User;
use nichxlson\pricelists\elements\Pricelist;
use nichxlson\pricelists\records\PricelistCustomerRecord;

class PricelistCustomerModel extends Model
{
    public $id;
    public $pricelist;
    public $customer;

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

    public function getCustomer(): User {
        return $this->customer;
    }

    public function setCustomer(User $customer) {
        $this->customer = $customer;
    }

    public function toRecord() {
        $record = new PricelistCustomerRecord();
        $record->setAttribute('id', $this->getId());
        $record->setAttribute('pricelistId', $this->getPricelist()->id);
        $record->setAttribute('customerId', $this->getCustomer()->id);

        return $record;
    }
}