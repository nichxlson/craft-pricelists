<?php
namespace nichxlson\pricelists\assetbundles;

use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

class PricelistsAsset extends AssetBundle
{
    public function init() {
        $this->sourcePath = "@nichxlson/pricelists/resources";

        $this->depends = [
            CpAsset::class,
        ];

        $this->css = [
            'css/pricelists.css',
        ];

        $this->js = [
            'js/pricelists.js',
        ];

        parent::init();
    }
}
