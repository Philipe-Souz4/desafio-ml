<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property string $access_token
 * @property string $refresh_token
 * @property int $updated_at
 */
class MeliTokens extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%meli_tokens}}';
    }

    public function rules()
    {
        return [
            [['access_token', 'refresh_token', 'updated_at'], 'required'],
            [['access_token'], 'string'],
            [['refresh_token'], 'string', 'max' => 255],
            [['updated_at'], 'integer'],
        ];
    }
}