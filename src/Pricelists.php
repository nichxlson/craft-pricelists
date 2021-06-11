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
use nichxlson\pricelists\services\PricelistService;
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

        $this->setComponents([
            'pricelistService' => PricelistService::class,
        ]);

        $this->registerEventHandlers();

        Craft::info(
            Craft::t('pricelists', '{name} plugin loaded',
                ['name' => $this->name]
            ),
            __METHOD__
        );
    }

    protected function registerEventHandlers() {
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

        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_CP_URL_RULES,
            function(RegisterUrlRulesEvent $event) {
                $event->rules['pricelists'] = 'pricelists/pricelists/index';
                $event->rules['pricelists/new'] = 'pricelists/pricelists/edit';
                $event->rules['pricelists/<pricelistId:\d+>'] = 'pricelists/pricelists/edit';
            });

        Event::on(
            Elements::class,
            Elements::EVENT_REGISTER_ELEMENT_TYPES,
            function(RegisterComponentTypesEvent $event) {
                $event->types[] = Pricelist::class;
            }
        );

        Event::on(
            CraftVariable::class,
            CraftVariable::EVENT_INIT,
            function(Event $event) {
                $variable = $event->sender;
                $variable->set('pricelist', PricelistVariable::class);
            }
        );

        Event::on(
            Product::class,
            Product::EVENT_DEFINE_BEHAVIORS,
            function(DefineBehaviorsEvent $event) {
                $event->sender->attachBehaviors([
                    ProductBehavior::class
                ]);
            }
        );

        Event::on(
            Variant::class,
            Variant::EVENT_DEFINE_BEHAVIORS,
            function(DefineBehaviorsEvent $event) {
                $event->sender->attachBehaviors([
                    VariantBehavior::class
                ]);
            }
        );
    }
}