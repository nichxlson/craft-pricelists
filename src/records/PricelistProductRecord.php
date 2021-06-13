<?php

namespace nichxlson\pricelists\records;

use craft\commerce\elements\Variant;
use nichxlson\pricelists\elements\Pricelist;
use yii\db\ActiveQueryInterface;
use yii\db\ActiveRecord;

class PricelistProductRecord extends ActiveRecord
{
    public static function tableName(): string {
        return '{{%pricelists_pricelist_product}}';
    }

    public function getPricelist(): ActiveQueryInterface {
        return $this->hasOne(Pricelist::class, ['id' => 'pricelistId']);
    }

    public function getProduct(): Variant {
        return Variant::findOne([
            'id' => $this->productId,
            'status' => null
        ]);
    }
}