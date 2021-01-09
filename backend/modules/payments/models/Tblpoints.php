<?php

namespace backend\modules\payments\models;

use Yii;

/**
 * This is the model class for table "tblpoints".
 *
 * @property int $id
 * @property int $sponsor
 * @property int $memberFrom
 * @property int $trxType
 * @property string $dateEarned
 * @property string|null $trxDate Date Transfered to wallet
 * @property int|null $trxBy Transfered to wallet by whom
 *
 * @property People $memberFrom0
 * @property People $sponsor0
 * @property Pointtrxtypes $trxType0
 */
class Tblpoints extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tblpoints';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'sponsor', 'memberFrom', 'trxType', 'dateEarned'], 'required'],
            [['id', 'sponsor', 'memberFrom', 'trxType', 'trxBy'], 'integer'],
            [['dateEarned', 'trxDate'], 'safe'],
            [['id'], 'unique'],
            [['memberFrom'], 'exist', 'skipOnError' => true, 'targetClass' => People::className(), 'targetAttribute' => ['memberFrom' => 'id']],
            [['sponsor'], 'exist', 'skipOnError' => true, 'targetClass' => People::className(), 'targetAttribute' => ['sponsor' => 'id']],
            [['trxType'], 'exist', 'skipOnError' => true, 'targetClass' => Pointtrxtypes::className(), 'targetAttribute' => ['trxType' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'sponsor' => Yii::t('app', 'Sponsor'),
            'memberFrom' => Yii::t('app', 'Member From'),
            'trxType' => Yii::t('app', 'Trx Type'),
            'dateEarned' => Yii::t('app', 'Date Earned'),
            'trxDate' => Yii::t('app', 'Trx Date'),
            'trxBy' => Yii::t('app', 'Trx By'),
        ];
    }

    /**
     * Gets query for [[MemberFrom0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMemberFrom0()
    {
        return $this->hasOne(People::className(), ['id' => 'memberFrom']);
    }

    /**
     * Gets query for [[Sponsor0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSponsor0()
    {
        return $this->hasOne(People::className(), ['id' => 'sponsor']);
    }

    /**
     * Gets query for [[TrxType0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrxType0()
    {
        return $this->hasOne(Pointtrxtypes::className(), ['id' => 'trxType']);
    }
}
