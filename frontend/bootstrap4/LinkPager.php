<?php

namespace frontend\bootstrap4;

use Yii;
use yii\base\InvalidConfigException;
use yii\base\Widget;
use yii\data\Pagination;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 * LinkPager displays a list of hyperlinks that lead to different pages of target.
 *
 * LinkPager works with a [[Pagination]] object which specifies the total number
 * of pages and the current page number.
 *
 * Note that LinkPager only generates the necessary HTML markups. In order for it
 * to look like a real pager, you should provide some CSS styles for it.
 * With the default configuration, LinkPager should look good using Twitter Bootstrap CSS framework.
 *
 * For more details and usage information on LinkPager, see the [guide article on pagination](guide:output-pagination).
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class LinkPager extends \yii\widgets\LinkPager
{
    
    /**
     * @var array HTML attributes which will be applied to all link containers
     * @since 2.0.13
     */
    public $linkContainerOptions = [];
    /**
     * @var array HTML attributes for the link in a pager container tag.
     * @see \yii\helpers\Html::renderTagAttributes() for details on how attributes are being rendered.
     */
    public $linkOptions = ['class'=>'page-link'];
    /**
     * @var string the CSS class for the each page button.
     * @since 2.0.7
     */
    public $pageCssClass = 'page-item';
    

    /**
     * @var string the CSS class for the disabled page buttons.
     */
    public $disabledPageCssClass = 'disabled';
   
    /**
     * @var array the options for the disabled tag to be generated inside the disabled list element.
     * In order to customize the html tag, please use the tag key.
     *
     * ```php
     * $disabledListItemSubTagOptions = ['tag' => 'div', 'class' => 'disabled-div'];
     * ```
     * @since 2.0.11
     */
    public $disabledListItemSubTagOptions = ['tag'=>'a','class'=>'page-link disable'];
    
}
