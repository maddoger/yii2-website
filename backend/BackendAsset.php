<?php
/**
 * @copyright Copyright (c) 2014 Vitaliy Syrchikov
 * @link http://syrchikov.name
 */

namespace maddoger\website\backend;
use Yii;
use yii\web\AssetBundle;

/**
 * BackendAsset.php
 *
 * @author Vitaliy Syrchikov <maddoger@gmail.com>
 * @link http://syrchikov.name
 * @package 
 */
class BackendAsset extends AssetBundle
{
    public $css = [
        'menu-editor.css'
    ];

    public function init()
    {
        $this->sourcePath = __DIR__.'/assets/dist';
        parent::init();
    }
}