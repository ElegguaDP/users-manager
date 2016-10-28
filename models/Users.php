<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "users".
 *
 * @property integer $id
 * @property string $email
 * @property string $avatar
 * @property string $username
 * @property string $mobile_token
 *
 * @property UserPhones[] $userPhones
 */
class Users extends \yii\db\ActiveRecord implements \yii\web\IdentityInterface {

    public $auth_key;
    public $accesToken;
    public $image;
    
    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'users';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['email'], 'required'],
            [['email'], 'email'],
            [['email'], 'trim'],
            [['email', 'username', 'avatar', 'mobile_token'], 'string', 'max' => 255],
            [['email', 'username', 'avatar', 'mobile_token'], 'safe'],
            [['username', 'avatar'], 'filter', 'filter' => function ($value) {
                $value = strip_tags($value);
                return $value;
            }],
            [['email'], 'unique'],
            [['avatar'], 'file', 'extensions'=>'jpg, gif, png, jpeg, bmp'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'email' => 'Email',
            'avatar' => 'Avatar',
            'username' => 'User Name',
            'image' => 'Avatar',
            'mobile_token' => 'Mobile Token'
        ];
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id) {
        return static::findOne($id);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null) {
        return static::findOne(['access_token' => $token]);
    }

    public function getId() {
        return $this->getPrimaryKey();
    }

    public function getAuthKey() {
        return $this->auth_key;
    }

    public function validateAuthKey($authKey) {
        return $this->getAuthKey() === $authKey;
    }

    public function generateAuthKey() {
        $this->auth_key = Security::generateRandomKey();
    }

    /**
     * relation with user's phones
     * @return \yii\db\ActiveQuery
     */
    public function getUserPhones() {
        return $this->hasMany(UserPhones::className(), ['user_id' => 'id']);
    }
    
    /*
     * generate mobile access token
     */
    public static function generateAccessToken() {
        return Yii::$app->getSecurity()->generateRandomString() . time();
    }    
    
    public static function findByEmail($email) {
        return static::findOne(['email' => $email]);
    }
    
    public static function findByMobileToken($token) {
        return static::findOne(['mobile_token' => $token]);
    }

}
