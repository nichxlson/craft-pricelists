<?php

namespace nichxlson\pricelists\records;

use yii\db\ActiveRecord;

class PricelistRecord extends ActiveRecord
{
    public static function tableName(): string {
        return '{{%pricelists_pricelist}}';
    }
}