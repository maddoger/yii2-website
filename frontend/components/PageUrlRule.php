<?php

namespace maddoger\website\frontend\components;

use yii\web\UrlRule;

class PageUrlRule extends UrlRule
{
    public $pattern = '<slug:.*?>';

    public $route = 'website/page/index';

    public $connectionID = 'db';

    public $mode = parent::PARSING_ONLY;
}