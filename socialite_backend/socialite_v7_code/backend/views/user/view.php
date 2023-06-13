<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Countryy */

$this->title = 'User Detail : '. $model->name;
$this->params['breadcrumbs'][] = ['label' => 'User', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>


<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <!-- <div class="box-header">
                <h3 class="box-title"><?= Html::encode($this->title) ?></h3>

            </div>
             -->
            <div class="box-body">



    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
        
        <?= Html::a('View all post', ['post/index', 'PostSearch[user_id]' => $model->id], ['class' => 'btn btn-primary']) ?>

    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'name',
            'email',
            [
                'attribute'  => 'is_verified',
                'value'  => function ($data) {
                    
                    
                    return $data->verifiedStatus;
                },
            ],
            
             [
                'attribute'  => 'country',
                'value'  => function ($data) {
                    
                    
                    return $data->country;
                },
            ],
           // 'website',
            
           /* [
                'attribute'  => 'sex',
                'value'  => function ($data) {
                    return $data->getSex();
                },
            ],*/

            /*'phone',
            'address',
            'postcode',
            'country',
            'city',*/
            'bio',
            'description',
            'available_balance',
            'available_coin',
            'last_active:datetime',
            'created_at:datetime',
            'updated_at:datetime',
            [
                'attribute'=>'image',
                'value'=> function ($model) {
                    
                     return Html::img($model->imageUrl, ['alt' => 'No Image', 'width' => '50px', 'height' => '50px']);
                    // return Html::img(Yii::$app->urlManagerFrontend->baseUrl.'/uploads/promotional-banner/thumb/'.$model->image, ['alt' => 'No Image', 'width' => '50px', 'height' => '50px']);
                },
                'format' => 'raw',
             ]
            
        ],
    ]) ?>

</div>


</div>

</div>
</div>
