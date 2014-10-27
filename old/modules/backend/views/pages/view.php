<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/**
 * @var yii\web\View $this
 * @var maddoger\website\models\Page $model
 */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => \Yii::t('maddoger/website', 'Pages'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="page-view">

    <p>
        <?= Html::a(\Yii::t('maddoger/website', 'Update'), ['update', 'id' => $model->id],
            ['class' => 'btn btn-primary']) ?>
        <?php echo Html::a(\Yii::t('maddoger/website', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data-confirm' => \Yii::t('maddoger/website', 'Are you sure to delete this item?'),
            'data-method' => 'post',
        ]); ?>
    </p>

    <?php
    /**
     * @var $createdUser null|\maddoger\user\models\User
     * @var $updatedUser null|\maddoger\user\models\User
     */
    $createdUser = $model->created_by_user_id > 0 ? \maddoger\user\models\User::findOne($model->created_by_user_id) : null;
    $updatedUser = $model->updated_by_user_id > 0 ? \maddoger\user\models\User::findOne($model->updated_by_user_id) : null;

    echo DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'title',
            'window_title',
            [
                'attribute' => 'slug',
                'format' => 'html',
                'value' => '<a target="_blank" href="' . Html::encode($model->slug) . '">' . Html::encode($model->slug) . '</a>',
            ],
            [
                'label' => Yii::t('maddoger/website', 'Published'),
                'format' => 'html',
                'value' => '<span class="label ' .
                    ($model->published == 0 ? 'label-danger' : ($model->published == 3 ? 'label-success' : 'label-warning')) . '">' .
                    $model->getPublishedValue() . '</span>'
            ],
            'meta_keywords',
            'meta_description',
            'locale',
            'layout',
            'created_at:datetime',
            [
                'attribute' => 'created_by_user_id',
                'format' => 'html',
                'value' => $createdUser ? Html::a($createdUser->username,
                    ['/user/users/view', 'id' => $createdUser->id]) : '-',
            ],
            'updated_at:datetime',
            [
                'attribute' => 'updated_by_user_id',
                'format' => 'html',
                'value' => $updatedUser ? Html::a($updatedUser->username,
                    ['/user/users/view', 'id' => $updatedUser->id]) : '-',
            ]
        ],
    ]); ?>

    <?php
    echo $model->text;
    ?>

</div>
