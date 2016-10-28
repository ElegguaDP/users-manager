<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use app\models\Users;
use app\models\UserPhones;
use app\models\MultipleForm;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\web\UploadedFile;
use yii\helpers\Url;

class UserController extends Controller {

    public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['update', 'index'],
                'rules' => [
                        [
                        'allow' => false,
                        'roles' => ['?'],
                    ],
                        [
                        'actions' => ['update', 'index'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Displays a single Person model.
     * @param integer $id
     * @return mixed
     */
    public function actionIndex($id) {
        if ($id != Yii::$app->user->id) {
            return $this->redirect(Yii::$app->user->id);
        }
        return $this->render('view', [
                    'model' => $this->findModel($id),
        ]);
    }

    /**
     * Update a single Person model with phones.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id) {
        if ($id != Yii::$app->user->id) {
            return $this->redirect(Yii::$app->user->id);
        }
        $model = $this->findModel($id);        
        $phones = $model->userPhones;
        if ($model->load(Yii::$app->request->post())) {
            $image = UploadedFile::getInstance($model, 'image');
            if($image){
                $fname = explode(".", $image->name);
                $extension = end($fname);
                $model->avatar = Yii::$app->security->generateRandomString().".{$extension}";
            }
            $path = Yii::$app->params['uploadPath'] . $model->avatar;
                       
            $oldIDs = ArrayHelper::map($phones, 'id', 'id');
            $phones = MultipleForm::createMultiple(UserPhones::classname(), $phones);
            MultipleForm::loadMultiple($phones, Yii::$app->request->post());
            $deletedIDs = array_diff($oldIDs, array_filter(ArrayHelper::map($phones, 'id', 'id')));
            // ajax validation
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ArrayHelper::merge(
                    ActiveForm::validateMultiple($phones),
                    ActiveForm::validate($model)
                );
            }

            // validate all models
            $valid = $model->validate();
            $valid = MultipleForm::validateMultiple($phones) && $valid;
            if ($valid) {
                $transaction = \Yii::$app->db->beginTransaction();
                try {
                    if ($flag = $model->save(false)) {
                        if($image){
                            $image->saveAs($path);
                        }
                        if (!empty($deletedIDs)) {
                            UserPhones::deleteAll(['id' => $deletedIDs]);
                        }
                        foreach ($phones as $phone) {
                            $phone->user_id = $model->id;
                            if (! ($flag = $phone->save(false))) {
                                $transaction->rollBack();
                                break;
                            }
                        }
                    }
                    if ($flag) {
                        $transaction->commit();
                        return $this->redirect(['index', 'id' => $model->id]);
                    }
                } catch (Exception $e) {
                    $transaction->rollBack();
                    unlink($path);
                }
            }
        }
        
        return $this->render('update', [
            'model' => $this->findModel($id),
            'phones' => (empty($phones)) ? [new UserPhones] : $phones
        ]);
    }

    /**
     * Finds the Person model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Person the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = Users::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
