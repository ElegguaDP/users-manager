<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "user_hashes".
 *
 * @property integer $id
 * @property string $email
 * @property string $hash
 * @property integer $is_confirmed
 * @property integer $created_at
 *
 */
class UserHashes extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'user_hashes';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
                [['email', 'hash', 'created_at'], 'required'],
                [['is_confirmed', 'created_at'], 'integer'],
                [['email', 'hash'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'email' => 'Email',
            'hash' => 'Hash',
            'is_confirmed' => 'Is Confirmed',
            'created_at' => 'Created At',
        ];
    }

}
