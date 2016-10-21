<?php
/* @var $this yii\web\View */

use yii\helpers\Url;

$this->title = "User's Manager";
?>
<div class="site-index">

    <div class="jumbotron">
        <h1>Congratulations!</h1>

        <p class="lead">You have successfully registered in our Web Application!.</p>

        <p><a class="btn btn-lg btn-success" href="<?= Url::toRoute(['/user/index', 'id' => Yii::$app->user->id]) ?>">View Profile</a></p>
        <p><a class="btn btn-lg btn-success" href="<?= Url::toRoute(['/user/update', 'id' => Yii::$app->user->id]) ?>">Update Profile</a></p>
    </div>

</div>
