<?php

namespace backend\modules\payments\models;

use Yii;

/**
 * This is the model class for table "recipienttypes".
 *
 * @property int $id
 * @property string $typeName
 *
 * @property Packconfig[] $packconfigs
 */
class Recipienttypes extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'recipienttypes';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['typeName'], 'required'],
            [['typeName'], 'string', 'max' => 45],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'typeName' => Yii::t('app', 'Type Name'),
        ];
    }

    /**
     * Gets query for [[Packconfigs]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPackconfigs()
    {
        return $this->hasMany(Packconfig::className(), ['recipientType' => 'id']);
    }
}
