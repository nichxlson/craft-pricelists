<?php

namespace nichxlson\pricelists;

use Craft;
use craft\base\Plugin;
use craft\commerce\elements\Product;
use craft\commerce\elements\Variant;
use craft\events\DefineBehaviorsEvent;
use craft\events\RegisterComponentTypesEvent;
use craft\events\RegisterCpNavItemsEvent;
use craft\events\RegisterUrlRulesEvent;
use craft\services\Elements;
use craft\web\twig\variables\Cp;
use craft\web\twig\variables\CraftVariable;
use craft\web\UrlManager;
use nichxlson\pricelists\behaviors\ProductBehavior;
use nichxlson\pricelists\behaviors\VariantBehavior;
use nichxlson\pricelists\elements\Pricelist;
use nichxlson\pricelists\variables\PricelistVariable;
use yii\base\Event;

class Pricelists extends Plugin
{
    public static $plugin;

    public $schemaVersion = '1.0.0';

    public $hasCpSettings = false;

    public $hasCpSection = false;

    public function init() {
        parent::init();

        $this->registerEventHandlers();

        Craft::info(
            Craft::t('pricelists', '{name} plugin loaded',
                ['name' => $this->name]
            ),
            __METHOD__
        );

        Event::on(
            Cp::class,
            Cp::EVENT_REGISTER_CP_NAV_ITEMS,
            function(RegisterCpNavItemsEvent $e) {
                $e->navItems['pricelists'] = [
                    'label' => Craft::t('pricelists', 'Pricelists'),
                    'url' => 'pricelists'
                ];
            }
        );
    }

    protected function registerEventHandlers() {

    }
}