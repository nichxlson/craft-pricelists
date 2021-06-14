<?php

namespace nichxlson\pricelists\services;

use Craft;
use craft\base\Component;
use nichxlson\pricelists\elements\Pricelist;
use nichxlson\pricelists\models\PricelistCustomerModel;
use nichxlson\pricelists\models\PricelistProductModel;
use nichxlson\pricelists\records\PricelistCustomerRecord;
use nichxlson\pricelists\records\PricelistProductRecord;

class PricelistService extends Component
{
    private $_pricelists;
    private $_products;

    public function getPricelistById(int $id, $siteId = null) {
        return Craft::$app->getElements()->getElementById($id, Pricelist::class, $siteId, [
            'status' => null,
        ]);
    }

    public function getProductRowHtml(): array {
        $originalNamespace = Craft::$app->getView()->getNamespace();
        $namespace = Craft::$app->getView()->namespaceInputName('products[__ROWID__]', $originalNamespace);
        Craft::$app->getView()->setNamespace($namespace);

        Craft::$app->getView()->startJsBuffer();

        $variables = [
            'product' => [],
        ];

        $template = Craft::$app->getView()->renderTemplate('pricelists/_includes/product-row', $variables);

        $bodyHtml = Craft::$app->getView()->namespaceInputs($template);
        $footHtml = Craft::$app->getView()->clearJsBuffer();

        Craft::$app->getView()->setNamespace($originalNamespace);

        return [
            'bodyHtml' => $bodyHtml,
            'footHtml' => $footHtml,
        ];
    }

    public function getCustomerRowHtml(): array {
        $originalNamespace = Craft::$app->getView()->getNamespace();
        $namespace = Craft::$app->getView()->namespaceInputName('customers[__ROWID__]', $originalNamespace);
        Craft::$app->getView()->setNamespace($namespace);

        Craft::$app->getView()->startJsBuffer();

        $variables = [
            'customer' => [],
        ];

        $template = Craft::$app->getView()->renderTemplate('pricelists/_includes/customer-row', $variables);

        $bodyHtml = Craft::$app->getView()->namespaceInputs($template);
        $footHtml = Craft::$app->getView()->clearJsBuffer();

        Craft::$app->getView()->setNamespace($originalNamespace);

        return [
            'bodyHtml' => $bodyHtml,
            'footHtml' => $footHtml,
        ];
    }


    public function save(Pricelist $pricelist) {
        if(!Craft::$app->getElements()->saveElement($pricelist)) {
            return false;
        }

        $this->deleteAllCustomersForPricelist($pricelist);
        $this->deleteAllProductsForPricelist($pricelist);

        foreach($pricelist->getCustomers() as $customer) {
            $pricelistCustomer = new PricelistCustomerModel();
            $pricelistCustomer->setPricelist($pricelist);
            $pricelistCustomer->setCustomer($customer['customer']);

            if(!$pricelistCustomer->toRecord()->save()) {
                return false;
            }
        }

        foreach($pricelist->getProducts() as $product) {
            $pricelistProduct = new PricelistProductModel();
            $pricelistProduct->setPricelist($pricelist);
            $pricelistProduct->setProduct($product['variant']);
            $pricelistProduct->pricelistPrice = $product['pricelistPrice'];

            if(!$pricelistProduct->toRecord()->save()) {
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

    public function getProductsForPricelist(Pricelist $pricelist) {
        $records = PricelistProductRecord::find()
            ->where(['pricelistId' => $pricelist->getId()])
            ->all();

        return array_map(function(PricelistProductRecord $record) {
            return [
                'id' => $record->id,
                'productId' => $record->productId,
                'pricelistPrice' => $record->pricelistPrice,
                'product' => $record->getProduct(),
            ];
        }, $records);
    }

    protected function deleteAllCustomersForPricelist(Pricelist $pricelist) {
        PricelistCustomerRecord::deleteAll(['pricelistId' => $pricelist->getId()]);
    }

    protected function deleteAllProductsForPricelist(Pricelist $pricelist) {
        PricelistProductRecord::deleteAll(['pricelistId' => $pricelist->getId()]);
    }

    public function getPricelistsForUser() {
        $user = Craft::$app->getUser()->getIdentity();

        if(is_null($this->_products)) {
            $this->_pricelists = [];
        }

        if($user) {
            $this->_pricelists = Pricelist::find()->all();
        }

        return $this->_pricelists;
    }

    public function getProductsForUser() {
        $user = Craft::$app->getUser()->getIdentity();

        $pricelists = [];

        if($user) {
            $pricelists = $this->getPricelistsForUser();
        }

        if(is_null($this->_products)) {
            $this->_products = [];
        }

        if(sizeof($pricelists)) {
            foreach($pricelists as $pricelist) {
                foreach($pricelist->getProducts() as $product) {
                    if(isset($this->_products[$product['productId']])) {
                        if($this->_products[$product['productId']] > $product['pricelistPrice']) {
                            $this->_products[$product['productId']] = $product['pricelistPrice'];
                        }
                    } else {
                        $this->_products[$product['productId']] = $product['pricelistPrice'];
                    }
                }
            }
        }

        return $this->_products;
    }

    public function getPricelistPriceForProduct($variantId) {
        if(is_null($this->_products)) {
            $this->_products = $this->getProductsForUser();
        }

        return $this->_products[$variantId] ?? null;
    }
}