<?php

namespace nichxlson\pricelists\services;

use Craft;
use nichxlson\pricelists\elements\Pricelist;
use nichxlson\pricelists\models\PricelistCustomerModel;
use nichxlson\pricelists\records\PricelistCustomerRecord;
use yii\base\Component;

class PricelistService extends Component
{
    public function getPricelistById(int $id, $siteId = null) {
        return Craft::$app->getElements()->getElementById($id, Pricelist::class, $siteId, [
            'status' => null,
        ]);
    }

    public function save(Pricelist $pricelist) {
        if(!Craft::$app->getElements()->saveElement($pricelist)) {
            return false;
        }

        $this->deleteAllCustomersForPricelist($pricelist);

        foreach($pricelist->getCustomers() as $customer) {
            $pricelistCustomer = new PricelistCustomerModel();
            $pricelistCustomer->setPricelist($pricelist);
            $pricelistCustomer->setCustomer($customer);

            if(!$pricelistCustomer->toRecord()->save()) {
                return false;
            }
        }

        return true;
    }

    public function getCustomersForPricelist(Pricelist $pricelist) {
        $records = PricelistCustomerRecord::find()
            ->where(['pricelistId' => $pricelist->getId()])
            ->all();

        return array_map(function(PricelistCustomerRecord $record) {
            return $record->getCustomer();
        }, $records);
    }

    protected function deleteAllCustomersForPricelist(Pricelist $pricelist) {
        PricelistCustomerRecord::deleteAll(['pricelistId' => $pricelist->getId()]);
    }
}