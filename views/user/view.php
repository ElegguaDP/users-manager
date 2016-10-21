<?php
use yii\helpers\Url;
$title = $model->username?$model->username:$model->email;
$this->title = 'User '.$model->email;
$this->params['breadcrumbs'][] = 'User '.$title;
?>

<div class="panel panel-default">
    <div class="panel-heading">
        <h4>
            <i class="glyphicon glyphicon-stats"></i>
            <?=' User ' . $title;?>
            <div class="pull-right">
                <a title="Update User" href="<?= Url::toRoute(['user/update', 'id' => $model->id]) ?>"><button type="button" class="edit-item btn btn-success btn-xs"><i class="glyphicon glyphicon-edit"></i></button></a>
            </div>
        </h4>
    </div>
    <div class="panel-body">
        <div class="row">
            <div class="col-sm-6">
                <div class="user-data">
                    <strong>Username:</strong> <?=$model->username?>
                </div>
            </div> 
            <div class="col-sm-6">
                <div class="user-data">
                    <strong>Avatar:</strong><br/>
                    <?if($model->avatar){?>
                    <img src="<?=Url::to('/uploads/' . $model->avatar)?>" class="avatar" alt="avatar">
                    <?}?>
                </div>
            </div>  
        </div>

        <div class="row">
            <div class="col-sm-6">
                <div class="user-data">
                    <strong>E-mail:</strong> <?=$model->email?>
                </div>
            </div>                    
        </div>
        <div class="user-data">
            <strong>Phones:</strong> 
        </div>
        <? foreach ($model->userPhones as $phone) { ?>            
            <div class="panel-body">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="user-data">
                            <?=$phone->phone?>
                        </div>                      
                    </div>                    
                </div>
            </div>
        <? } ?>
    </div>
</div>
