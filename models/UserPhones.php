<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "user_phones".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $phone
 *
 * @property Users $user
 */
class UserPhones extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_phones';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['phone'], 'required'],
            ['phone', 'filter', 'filter' => function ($value) {
                    $value = strip_tags($value);
                    if($value){
                        return $value;
                    } else {
                        $this->addError('phone', 'The phone is invalid!');
                    }
                }
            ],
            ['phone', 'trim'],
            [['phone'], 'string', 'max' => 255],
            [['user_id'], 'integer'],            
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'phone' => 'Phone',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(Users::className(), ['id' => 'user_id']);
    }
}
