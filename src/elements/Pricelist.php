<?php

namespace nichxlson\pricelists\elements;

use Craft;
use craft\base\Element;
use craft\commerce\Plugin;
use craft\elements\actions\Delete;
use craft\elements\db\ElementQueryInterface;
use craft\helpers\UrlHelper;
use nichxlson\pricelists\elements\db\PricelistQuery;
use nichxlson\pricelists\Pricelists;
use nichxlson\pricelists\records\PricelistRecord;

class Pricelist extends Element
{
    protected $_customers;
    protected $_products;

    public static function displayName(): string {
        return Craft::t('app', 'Pricelist');
    }

    public static function pluralDisplayName(): string {
        return Craft::t('app', 'Pricelists');
    }

    public static function hasContent(): bool {
        return true;
    }

    public static function hasTitles(): bool {
        return true;
    }

    public static function find(): ElementQueryInterface {
        return new PricelistQuery(static::class);
    }

    protected static function defineActions(string $source = null): array {
        return [
            Craft::$app->getElements()->createAction([
                'type' => Delete::class,
                'confirmationMessage' => Craft::t('pricelists', 'Are you sure you want to delete the selected pricelist(s)?'),
                'successMessage' => Craft::t('pricelists', 'Pricelists deleted.'),
            ])
        ];
    }

    protected static function defineSources(string $context = null): array {
        $sources = [
            '*' => [
                'key' => '*',
                'label' => 'All pricelists',
                'criteria' => []
            ]
        ];

        return $sources;
    }

    protected static function defineTableAttributes(): array {
        return [
            'title' => ['label' => Craft::t('pricelists', 'Title')],
//            'customers' => ['label' => Craft::t('pricelists', 'Customers')],
        ];
    }

    protected static function defineSearchableAttributes(): array {
        return ['title'];
    }

    protected function tableAttributeHtml(string $attribute): string {
        switch ($attribute) {
            case 'customers':
                $customerList = [];

                foreach($this->getCustomers() as $customer) {
                    $customerList[] = $customer->username;
                }

                return join(',', $customerList);

            default: {
                return parent::tableAttributeHtml($attribute);
            }
        }
    }

    protected static function defineSortOptions(): array {
        return [
//            'title' => Craft::t('pricelists', 'Title'),
        ];
    }

    public function getIsEditable(): bool {
        return true;
    }

    public function afterSave(bool $isNew) {
        if(!$isNew) {
            $record = PricelistRecord::findOne($this->id);

            if(!$record) {
                throw new \Exception('Invalid pricelist id: ' . $this->id);
            }
        } else {
            $record = new PricelistRecord();
            $record->id = $this->id;
        }

        $record->save(false);

        return parent::afterSave($isNew);
    }

    public function getCpEditUrl() {
        return UrlHelper::cpUrl('pricelists/' . $this->id);
    }

    public function getCustomers() {
        if(is_null($this->_customers) && $this->getId()) {
            $this->_customers = Pricelists::getInstance()->pricelistService->getCustomersForPricelist($this);
        }

        return $this->_customers;
    }

    public function setCustomers($customers) {
        $this->_customers = [];

        if(is_array($customers)) {
            foreach($customers as $customer) {
                $innerCustomers = $customer['customers'];

                if(is_array($innerCustomers)) {
                    foreach($innerCustomers as $innerCustomer) {
                        $this->_customers[] = [
                            'customer' => Craft::$app->getUsers()->getUserById($innerCustomer)
                        ];
                    }
                }
            }
        }
    }

    public function getProducts() {
        if(is_null($this->_products) && $this->getId()) {
            $this->_products = Pricelists::getInstance()->pricelistService->getProductsForPricelist($this);
        }

        return $this->_products;
    }

    public function setProducts($products) {
        $this->_products = [];

        if(is_array($products)) {
            foreach($products as $product) {
                $innerProducts = $product['products'];

                if(is_array($innerProducts)) {
                    foreach($innerProducts as $innerProduct) {
                        $this->_products[] = [
                            'pricelistPrice' => $product['pricelistPrice'],
                            'variant' => Plugin::getInstance()->variants->getVariantById($innerProduct),
                        ];
                    }
                }
            }
        }
    }
}