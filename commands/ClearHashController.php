<?php

namespace app\commands;

use Yii;
use yii\console\Controller;
use app\models\UserHashes;
use yii\helpers\Console;

class ClearHashController extends Controller {
    /*
     * Clear user hash if hash expiration date is over
     */

    public function actionIndex() {
        $hashList = UserHashes::findAll();
        if ($hashList) {
            foreach ($hashList as $hash) {
                if (($hash->hash_exp_date - time()) >= Yii::$app->params['hashLifeTime']) {
                    $hash->delete();
                }
            }
        }
        $result = $this->ansiFormat("Success\n", Console::FG_YELLOW);

        echo $result;
    }

}
