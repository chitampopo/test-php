<?php
/**
 * Created by PhpStorm.
 * User: phuocnguyen
 * Date: 05/09/2018
 * Time: 7:30 AM
 */
use yii\grid\GridView;
use application\utilities\UrlUtils;

$colCheckbox = array(
    'class' => 'yii\grid\CheckboxColumn',
    'checkboxOptions' => function ($model) {
        return ["value" => $model->id];
    },
    'headerOptions' => [
        'width' => 10
    ],
);
$stt = array(
    'class' => 'yii\grid\SerialColumn',
    'header' => '#',
    'headerOptions' => [
        'width' => 10
    ],
    'contentOptions' => [
        'style' => 'text-align:center'
    ]
);
$name = array(
    'content' => function ($model) {
        return UrlUtils::buildEditLink('chanel', $model->id, $model->name);
    }
);
$desc = array(
    'attribute' => 'description',
);

echo GridView::widget([
    'id' => 'list-data',
    'dataProvider' => $data,
    'columns' => [
        $colCheckbox,
        $stt,
        $name,
        $desc
    ],
]);