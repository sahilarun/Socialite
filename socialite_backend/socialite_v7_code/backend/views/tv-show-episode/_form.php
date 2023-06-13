<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use kartik\date\DatePicker;
use kartik\datetime\DateTimePicker;
/* @var $this yii\web\View */
/* @var $model app\models\Countryy */
/* @var $form yii\widgets\ActiveForm */
//echo Yii::$app->urlManagerFrontend->baseUrl;
//echo  Url::base(true);
?>

<div class="countryy-form">
<?php if(isset($_GET['tv_show_id'])){
        $model->tv_show_id = $_GET['tv_show_id'];
  } ?>
    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
    <?= $form->field($model, 'tv_show_id')->dropDownList($tvShowData,['prompt' => 'Select']); ?>
    
    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
    
    <?= $form->field($model, 'episode_period')->textInput() ?>
   
   <?= $form->field($model, 'created_at')->widget(DatePicker::classname(), [
        'options' => ['placeholder' => 'Enter Show date ...'],
        //'size' => 'lg',
        'pluginOptions' => [
            'autoclose' => true,
            'format' => 'yyyy-mm-dd',
        ],
    ]); ?>
     <?= $form->field($model, 'status')->dropDownList($model->getStatusDropDownData()); ?>
    <?= $form->field($model, 'imageFile')->fileInput() ?>
    <?php if(!$model->isNewRecord && $model->image ){ ?>
    
    <p><?= Html::img($model->imageUrl, ['alt' => 'No Image', 'width' => '50px', 'height' => '50px']);?>
    </p>
    <?php }?>
    <?= $form->field($model, 'video')->fileInput(); ?>
    <?php if(!$model->isNewRecord && $model->video ){
      
        if(($model->video)>0){       
                echo '<video width="100" poster="'.$model->imageUrl.'"  height="100" controls>
                <source src="'.$model->video.'" type="video/mp4">
                <source src="movie.ogg" type="video/ogg"></video>' ; 
        }
        ?>
       <?php ?>
    <p><?= Html::img($model->imageUrl, ['alt' => 'No Image', 'width' => '50px', 'height' => '50px']);?>
    </p>
    <?php }?>

    
    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>


<?php
$js=<<< JS
  //  alert('a')
        
  $(document).ready(function(){
    function hideShowDiv() {
        var inputValue = $("#livetv-is_paid"). val();
        
        if(inputValue==1){
            
            $('#coin_text_block').slideDown();
        }else{
            
            $('#coin_text_block').slideUp();
            
        }
    }
    
    $('#livetv-is_paid').change(function(){
        hideShowDiv();
       
    });
    hideShowDiv();

});
JS;
$this->registerJs($js,\yii\web\view::POS_READY);
?>
