<?php

namespace nichxlson\pricelists\controllers;

use Craft;
use craft\web\Controller as BaseController;
use nichxlson\pricelists\elements\Pricelist;
use nichxlson\pricelists\Pricelists;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class PricelistsController extends BaseController
{
    public function actionIndex(): Response {
        $this->requireCpRequest();

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

        $variables['pricelist'] = $pricelist;
        $variables['customers'] = $pricelist->getCustomers();
        $variables['products'] = $pricelist->getProducts();
        $variables['productRowHtml'] = Pricelists::getInstance()->pricelistService->getProductRowHtml();
        $variables['siteIds'] = Craft::$app->getSites()->getAllSiteIds();
        $variables['enabledSiteIds'] = Craft::$app->getSites()->getAllSiteIds();

        return $this->renderTemplate('pricelists/_edit', $variables);
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
        $pricelist->setProducts($request->getBodyParam('products'));
        $pricelist->setFieldValuesFromRequest('fields');

        $pricelist->siteId = $siteId ?? $pricelist->siteId;

        $test = $request->getBodyParam('products');

        if(!Pricelists::getInstance()->pricelistService->save($pricelist)) {
            Craft::$app->getSession()->setError(\Craft::t('pricelists', 'Couldn’t save pricelist.'));
            Craft::$app->getUrlManager()->setRouteParams([
                'pricelist' => $pricelist
            ]);

            return null;
        }

        Craft::$app->getSession()->setNotice(\Craft::t('pricelists', 'Pricelist saved.'));

        return $this->redirectToPostedUrl($pricelist);
    }
}