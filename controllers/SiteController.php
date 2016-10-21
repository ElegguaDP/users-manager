<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\SignupForm;
use app\models\Users;
use app\models\UserHashes;
use yii\helpers\Url;

class SiteController extends Controller {

    /**
     * @inheritdoc
     */
    public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                        [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions() {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ]
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex() {
        if (Yii::$app->user->isGuest) {
            return $this->redirect('login');
        }
        return $this->render('index');
    }

    /**
     * Login action. Generate and send auth link to user email
     *
     * @return string
     */
    public function actionLogin() {
        if (!Yii::$app->user->isGuest) {
            return $this->redirect('/user/' . Yii::$app->user->id);
        }

        $model = new SignupForm();
        $request = Yii::$app->request->post();
        if ($model->load($request) && $model->validate()) {
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
                Yii::$app->session->setFlash('success', 'Auth link successfuly sent.', true);
                return $this->refresh();
            } else {
                Yii::$app->session->setFlash('warning', 'Something was wrong. please try again later', true);
                return $this->refresh();
            }
        }
        return $this->render('login', [
                    'model' => $model,
        ]);
    }

    /**
     * Email hash verification action.
     *
     * @return string
     */
    public function actionHashcheck($hash) {
        if (!Yii::$app->user->isGuest) {
            return $this->redirect('/user/' . Yii::$app->user->id);
        }
        $hash = trim(strip_tags($hash));
        if ($hash) {
            $userhash = UserHashes::find(['hash' => $hash])->one();
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
                    $user->save();
                    Yii::$app->session->setFlash('success', 'Congratulations! Your account successfuly created.');
                    Yii::$app->user->login($user, Yii::$app->params['rememberMe'] ? 3600 * 24 * 30 : 0);
                    $userhash->delete(); //remove hash link
                    return $this->redirect('/user/' . Yii::$app->user->id);
                }
            }
        }
        Yii::$app->session->setFlash('warning','Auth link was wrong or expired.');
        return $this->redirect('login');
    }

    /**
     * Logout action.
     *
     * @return string
     */
    public function actionLogout() {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    //hash generator
    public function generateLink() {
        $link = md5(Yii::$app->security->generateRandomString(Yii::$app->params['linkLenght']));
        return $link;
    }

}
