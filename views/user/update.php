<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\file\FileInput;
use wbraganca\dynamicform\DynamicFormWidget;

$this->title = 'Update User '.$model->email;
$title = $model->username?$model->username:$model->email;
$this->params['breadcrumbs'][] = ['label' => 'User '.$title, 'url' => '/user/'.$model->id];
$this->params['breadcrumbs'][] = 'Update user '.$title;
?>

<div class="user-form">

    <?php
    $form = ActiveForm::begin(
        [
            'id' => 'dynamic-form',
            'enableAjaxValidation' => true,
            'options' => [
                'enctype'=>'multipart/form-data'
            ]
        ]
    );
    ?>
    <div class="row">
        <div class="col-sm-6">
            <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-6">
            <?= $form->field($model, 'username')->textInput(['maxlength' => true]) ?>
        </div>
    </div> 
    <div class="row">
        <div class="col-sm-6">
            <?if($model->avatar){?>
                <img src="<?=Url::to('/uploads/' . $model->avatar)?>" class="avatar" alt="avatar">
                <div class="clearfix"></div>
            <?}?>
            <?= $form->field($model, 'image')->widget(FileInput::classname(), [
                'options' => [
                    'accept' => 'image/*',
                    'multiple' => false,               
                ],
                'pluginOptions' => [
                    'showUpload' => false,
                    'buttonOptions' => ['label' => false],
                    'removeOptions' => ['label' => false],
                ]
            ]);?>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading"><h4><i class="glyphicon glyphicon-phone"></i> Phones</h4></div>
        <div class="panel-body">
             <?php DynamicFormWidget::begin([
                'widgetContainer' => 'dynamicform_wrapper', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
                'widgetBody' => '.container-items', // required: css class selector
                'widgetItem' => '.item', // required: css class
                //'limit' => 4, // the maximum times, an element can be cloned (default 999)
                'min' => 1, // 0 or 1 (default 1)
                'insertButton' => '.add-item', // css class
                'deleteButton' => '.remove-item', // css class
                'model' => $phones[0],
                'formId' => 'dynamic-form',
                'formFields' => [
                    'phone'
                ],
            ]); ?>

            <div class="container-items"><!-- widgetContainer -->
            <?php foreach ($phones as $i => $phone){ ?>
                <div class="item panel panel-default"><!-- widgetBody -->
                    
                    <div class="panel-body">
                        <?php
                            // necessary for update action.
                            if (! $phone->isNewRecord) {
                                echo Html::activeHiddenInput($phone, "[{$i}]id");
                            }
                        ?>
                        <div class="row">
                            <div class="col-sm-6">    
                                <?= $form->field($phone, "[{$i}]phone")->textInput(['maxlength' => true]) ?>
                            </div>      
                            <div class="pull-right">
                                <button type="button" class="add-item btn btn-success btn-xs"><i class="glyphicon glyphicon-plus"></i></button>
                                <button type="button" class="remove-item btn btn-danger btn-xs"><i class="glyphicon glyphicon-minus"></i></button>
                            </div>
                        </div><!-- .row -->
                    </div>
                </div>
            <?php } ?>
            </div>
            <?php DynamicFormWidget::end(); ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>