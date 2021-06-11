<?php

namespace nichxlson\pricelists\elements\db;

use craft\elements\db\ElementQuery;

class PricelistQuery extends ElementQuery
{
    protected function beforePrepare(): bool {
        $this->joinElementTable('pricelists_pricelist');

        return parent::beforePrepare();
    }
}