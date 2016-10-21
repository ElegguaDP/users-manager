<?php

namespace app\controllers\api;

use Yii;
use app\models\Users;
use app\models\UserHashes;
use app\models\UserPhones;
use yii\helpers\Url;

class UserController extends ApiController {

//get auth link
    public function actionLogin() {
        $request = Yii::$app->request->post();
        $validator = new yii\validators\EmailValidator();
        if ($request['email'] && $validator->validate($request['email'])) {
            $hash = $this->generateLink();
            $url = Url::toRoute(['hashcheck', 'hash' => $hash], true);
            $userhash = new UserHashes();
            $userhash->email = $request["SignupForm"]['email'];
            $userhash->hash = $hash;
            $userhash->created_at = time();
            $userhash->save();
            /*
             * send email with link
             */
            $mail = Yii::$app->mailer->compose()
                    ->setTo($request["SignupForm"]['email'])
                    ->setFrom("users-manager@example.cmo")
                    ->setSubject("Auth link")
                    ->setTextBody("Please, follow this temporary link:" . "\r\n"
                    . $url . "\r\n"
                    . "Link expiration time is only 1 hour.");
            if ($mail->send()) {
                $this->sendResponse(200, false, null, 'E-mail sent successful');
            } else {
                $errorMessage = 'Something was wrong. please try again later';
                $this->sendResponse(400, false, null, $errorMessage);
            }
        } else {
            $errorMessage = 'Wrong e-mail';
            $this->sendResponse(400, false, null, $errorMessage);
        }
    }

//check hash link and auth
    public function actionHashcheck() {
        $request = Yii::$app->request->post();
        if ($request['hash']) {
            $userhash = UserHashes::find(['hash' => $request['hash']])->one();
            if ($userhash) {
                $user = Users::findByEmail($userhash->email);
                if ($user) {
                    Yii::$app->session->setFlash('success', 'Welcome again!');
                    Yii::$app->user->login($user, Yii::$app->params['rememberMe'] ? 3600 * 24 * 30 : 0);
                    $userhash->delete(); //remove hash link
                    return $this->redirect('/user/' . Yii::$app->user->id);
                } else {
                    $user = new Users();
                    $user->email = $userhash->email;
                    $user->mobile_token = Users::generateAccessToken();
                    $user->save();                    
                    $userhash->delete(); //remove hash link
                }
                $this->sendResponse(200, false, null, 'E-mail sent successful');
            }
        }
    }

//get user
    public function actionGetUser() {
        $request = Yii::$app->request->post();
    }

    public function actionUpdateUser() {
        $request = Yii::$app->request->post();
    }

    //hash generator
    public function generateLink() {
        $link = md5(Yii::$app->security->generateRandomString(Yii::$app->params['linkLenght']));
        return $link;
    }

}
