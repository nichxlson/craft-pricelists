<?php

namespace nichxlson\pricelists\elements;

use Craft;
use craft\base\Element;
use craft\elements\actions\Delete;
use craft\elements\db\ElementQueryInterface;
use craft\helpers\UrlHelper;
use nichxlson\pricelists\elements\db\PricelistQuery;
use nichxlson\pricelists\Pricelists;
use nichxlson\pricelists\records\PricelistRecord;

class Pricelist extends Element
{
    protected $_customers;

    public static function displayName(): string {
        return 'Pricelist';
    }

    public static function pluralDisplayName(): string {
        return 'Pricelists';
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
        ];
    }

    protected static function defineSearchableAttributes(): array {
        return ['title'];
    }

    protected static function defineSortOptions(): array {
        return [
            'title' => \Craft::t('pricelists', 'Title'),
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
                $this->_customers[] = Craft::$app->getUsers()->getUserById($customer);
            }
        }
    }
}