<?php

namespace nichxlson\pricelists\controllers;

use Craft;
use craft\commerce\elements\Product;
use craft\commerce\elements\Variant;
use craft\commerce\Plugin;
use craft\elements\User;
use craft\web\Controller as BaseController;
use nichxlson\pricelists\elements\Pricelist;
use nichxlson\pricelists\Pricelists;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class PricelistsController extends BaseController
{
    public function actionIndex(): Response {
        return $this->renderTemplate('pricelists/index');
    }

    public function actionEdit(int $pricelistId = null, Pricelist $pricelist = null, array $variables = []): Response {
        $this->requireCpRequest();

        if($pricelistId) {
            if(!$pricelist = Pricelists::getInstance()->pricelistService->getPricelistById($pricelistId)) {
                throw new NotFoundHttpException();
            }
        } else {
            $pricelist = $pricelist ?? new Pricelist();
        }

        $customers = Craft::$app->getUsers();

        // Set the "Continue Editing" URL
        $siteSegment = Craft::$app->getIsMultiSite() && Craft::$app->getSites()->getCurrentSite()->id != $site->id ? "/{$site->handle}" : '';
        $continueEditingUrl = 'pricelists/{id}' . $siteSegment;

        return $this->renderTemplate('pricelists/_edit', [
            'pricelist' => $pricelist,
            'customers' => $customers,
            'customerElementType' => User::class,
            'continueEditingUrl' => $continueEditingUrl,
            'siteIds' => Craft::$app->getSites()->getAllSiteIds(),
            'enabledSiteIds' => Craft::$app->getSites()->getAllSiteIds(),
        ]);
    }

    public function actionSave() {
        $this->requireCpRequest();
        $this->requirePostRequest();

        $request = Craft::$app->getRequest();

        $pricelistId = $request->getBodyParam('pricelistId');
        $siteId = $request->getBodyParam('siteId');

        if($pricelistId) {
            $pricelist = Pricelists::getInstance()->pricelistService->getPricelistById($pricelistId, $siteId);

            if(!$pricelist) {
                throw new \Exception(Craft::t('pricelists', 'No pricelist with the ID “{id}”', ['id' => $pricelistId]));
            }
        } else {
            $pricelist = new Pricelist();
        }

        $pricelist->title = $request->getBodyParam('title', $pricelist->title);
        $pricelist->setCustomers($request->getBodyParam('customers'));
        $pricelist->setFieldValuesFromRequest('fields');

        $pricelist->siteId = $siteId ?? $pricelist->siteId;

        if(!Pricelists::getInstance()->pricelistService->save($pricelist)) {
            Craft::$app->getSession()->setError(\Craft::t('pricelists', 'Couldn’t save pricelist.'));
            Craft::$app->getUrlManager()->setRouteParams([
                'pricelist' => $pricelist
            ]);

            return null;
        }

        Craft::$app->getSession()->setNotice(\Craft::t('pricelists', 'Pricelists saved.'));

        return $this->redirectToPostedUrl($pricelist);
    }
}