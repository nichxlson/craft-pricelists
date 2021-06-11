<?php

namespace nichxlson\pricelists\records;

use craft\base\Element;
use craft\elements\User;
use nichxlson\pricelists\elements\Pricelist;
use yii\db\ActiveQueryInterface;
use yii\db\ActiveRecord;

class PricelistCustomerRecord extends ActiveRecord
{
    public static function tableName(): string {
        return '{{%pricelists_pricelist_customer}}';
    }

    public function getPricelist(): ActiveQueryInterface {
        return $this->hasOne(Pricelist::class, ['id' => 'pricelistId']);
    }

    public function getCustomer(): User {
        return User::findOne([
            'id' => $this->customerId,
            'status' => null
        ]);
    }
}