<?php

namespace app\controllers\api;

use Yii;
use app\models\Users;
use app\models\UserHashes;
use app\models\UserPhones;
use yii\helpers\Url;

class UserController extends ApiController {

    /**
     * get hash link to email
     */
    public function actionLogin() {
        $request = Yii::$app->request->post();
        $validator = new yii\validators\EmailValidator();
        if ($request && $request['email'] && $validator->validate($request['email'])) {
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

    /**
     * check hash link and auth
     */
    public function actionHashcheck() {
        $request = Yii::$app->request->post();
        if ($request && $request['hash']) {
            $userhash = UserHashes::find(['hash' => $request['hash']])->one();
            if ($userhash) {
                $user = Users::findByEmail($userhash->email);
                if ($user) {
                    $user->mobile_token = Users::generateAccessToken();
                    $user->update();
                    $userhash->delete(); //remove hash link
                } else {
                    $user = new Users();
                    $user->email = $userhash->email;
                    $user->mobile_token = Users::generateAccessToken();
                    $user->save();
                    $userhash->delete(); //remove hash link
                }
                $data = json_encode(
                        [
                            'mobile_token' => $user->mobile_token
                        ]
                );
                $this->sendResponse(200, false, $data, 'E-mail sent successful');
            }
        }
        $errorMessage = 'Wrong or expired hash';
        $this->sendResponse(400, false, null, $errorMessage);
    }

    /**
     * get user's data
     */
    public function actionGetUser() {
        $request = Yii::$app->request->post();
        if ($request && $request['mobile_token']) {
            $user = Users::findByEmail($request['mobile_token']);
            if ($user) {
                $phonesData = $user->userPhones;
                $phones = [];
                foreach ($phonesData as $phone) {
                    $phones[$phone->id] = $phone->phone;
                }
                $data = json_encode(
                        [
                            'username' => $user->username,
                            'email' => $user->email,
                            'avatar' => Yii::$app->params['uploadPath'] . $user->avatar,
                            'phones' => $phones
                        ]
                );
                $this->sendResponse(200, false, $data);
            }
        }
        $errorMessage = 'Wrong mobile token';
        $this->sendResponse(403, false, null, $errorMessage);
    }

    /**
     * update user's name, email, phones
     */
    public function actionUpdateUser() {
        $request = Yii::$app->request->post();
        if ($request && $request['mobile_token'] && $request['data']) {
            $user = Users::findByMobileToken($request['mobile_token']);
            if ($user) {
                $data = json_decode($request['data']);
                $user->username = $data->username?$data->username:$user->username;
                $user->email = $data->email?$data->email:$user->email;
                $user->update();
                
                if($data->phones){
                    $oldPhones = UserPhones::deleteAll('user_id = :uid', ['uid' => $user->id]);
                    foreach ($data->phones as $phone) {
                        $newPhone = new UserPhones();
                        $newPhone->user_id = $user->id;
                        $newPhone->phone = $phone;
                        $newPhone->save();
                    }
                }
                $this->sendResponse(200, false, null, 'Success');
            }
        }
        $errorMessage = 'Wrong mobile token';
        $this->sendResponse(403, false, null, $errorMessage);
    }

    /**
     * update user's avatar
     */
    public function actionUploadAvatar() {
        $request = Yii::$app->request->post();
        if ($request && $request['mobile_token'] && $request['data']) {
            $user = Users::findByMobileToken($request['mobile_token']);
            $personData = json_decode($request['data']);
            if ($user) {
                if ($personData->avatar) {
                    $filename = Yii::$app->params['uploadPath'];
                    $imageFile = base64_decode($personData->img_blob);
                    $f = finfo_open();
                    $mime_type = finfo_buffer($f, $imageFile, FILEINFO_MIME_TYPE);
                    $ext = $this->type_to_ext($mime_type);
                    $name = Yii::$app->getSecurity()->generateRandomString() . $ext;
                    $file = $filename . $name;
                    file_put_contents($file, $imageFile);
                    if (!$user->avatar) {
                        $user->avatar = $name;
                    } else {
                        unlink(Yii::$app->params['uploadPath'] . $user->avatar);
                    }
                    $user->update();
                    $this->sendResponse(200, false, null, 'Success');
                }
            }
        }
        $errorMessage = 'Wrong mobile token';
        $this->sendResponse(403, false, null, $errorMessage);
    }

    //hash generator
    public function generateLink() {
        $link = md5(Yii::$app->security->generateRandomString(Yii::$app->params['linkLenght']));
        return $link;
    }

}
