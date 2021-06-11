<?php

namespace nichxlson\pricelists\records;

use craft\base\Element;
use yii\db\ActiveQueryInterface;
use yii\db\ActiveRecord;

class PricelistRecord extends ActiveRecord
{
    public static function tableName(): string {
        return '{{%pricelists_pricelist}}';
    }

    public function getElement(): ActiveQueryInterface {
        return $this->hasOne(Element::class, ['id' => 'id']);
    }
}