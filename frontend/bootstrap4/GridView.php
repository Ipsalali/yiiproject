<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace frontend\bootstrap4;

use Yii;
use yii\base\InvalidConfigException;
use yii\base\Model;
use yii\helpers\{Html,ArrayHelper};
use yii\helpers\Json;
use yii\helpers\Url;
use yii\i18n\Formatter;
use frontend\bootstrap4\LinkPager;

class GridView extends \yii\grid\GridView
{

    /**
     * Renders the pager.
     * @return string the rendering result
     */
    public function renderPager()
    {
        $pagination = $this->dataProvider->getPagination();
        if ($pagination === false || $this->dataProvider->getCount() <= 0) {
            return '';
        }
        /* @var $class LinkPager */
        $pager = $this->pager;
        $class = ArrayHelper::remove($pager, 'class', LinkPager::className());
        $pager['pagination'] = $pagination;
        $pager['view'] = $this->getView();

        return $class::widget($pager);
    }
}
