<?php

namespace backend\modules\payments\models;

use Yii;

/**
 * This is the model class for table "packconfig".
 *
 * @property int $id
 * @property int $packId
 * @property int $rankId
 * @property int $relevel Relative level
 * @property int $cmpsntype
 * @property int $recipientType
 * @property int $units
 * @property float $amount
 * @property string $itemcntrl Ensures distinct Items: format xxyyzzaabbcc; xx=Package,yy=Rank, zz=Level, aa=CompensationType, bb= Recipient Type, cc = CompensationUnits 
 * @property int $recordBy
 * @property string $recordDate
 * @property int|null $changedBy
 * @property string|null $changeDate
 *
 * @property Compensationtypes $cmpsntype0
 * @property Packages $pack
 * @property Ranks $rank
 * @property Recipienttypes $recipientType0
 * @property Compensationunits $units0
 */
class Packconfig extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'packconfig';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['packId', 'rankId', 'relevel', 'cmpsntype', 'recipientType', 'units', 'recordBy', 'changedBy'], 'integer'],
            [['relevel', 'cmpsntype', 'recipientType', 'units', 'amount', 'itemcntrl', 'recordBy', 'recordDate'], 'required'],
            [['amount'], 'number'],
            [['recordDate', 'changeDate'], 'safe'],
            [['itemcntrl'], 'string', 'max' => 12],
            [['itemcntrl'], 'unique'],
            [['cmpsntype'], 'exist', 'skipOnError' => true, 'targetClass' => Compensationtypes::className(), 'targetAttribute' => ['cmpsntype' => 'id']],
            [['packId'], 'exist', 'skipOnError' => true, 'targetClass' => Packages::className(), 'targetAttribute' => ['packId' => 'id']],
            [['rankId'], 'exist', 'skipOnError' => true, 'targetClass' => Ranks::className(), 'targetAttribute' => ['rankId' => 'id']],
            [['recipientType'], 'exist', 'skipOnError' => true, 'targetClass' => Recipienttypes::className(), 'targetAttribute' => ['recipientType' => 'id']],
            [['units'], 'exist', 'skipOnError' => true, 'targetClass' => Compensationunits::className(), 'targetAttribute' => ['units' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'packId' => Yii::t('app', 'Pack ID'),
            'rankId' => Yii::t('app', 'Rank ID'),
            'relevel' => Yii::t('app', 'Relevel'),
            'cmpsntype' => Yii::t('app', 'Cmpsntype'),
            'recipientType' => Yii::t('app', 'Recipient Type'),
            'units' => Yii::t('app', 'Units'),
            'amount' => Yii::t('app', 'Amount'),
            'itemcntrl' => Yii::t('app', 'Itemcntrl'),
            'recordBy' => Yii::t('app', 'Record By'),
            'recordDate' => Yii::t('app', 'Record Date'),
            'changedBy' => Yii::t('app', 'Changed By'),
            'changeDate' => Yii::t('app', 'Change Date'),
        ];
    }

    /**
     * Gets query for [[Cmpsntype0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCmpsntype0()
    {
        return $this->hasOne(Compensationtypes::className(), ['id' => 'cmpsntype']);
    }

    /**
     * Gets query for [[Pack]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPack()
    {
        return $this->hasOne(Packages::className(), ['id' => 'packId']);
    }

    /**
     * Gets query for [[Rank]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRank()
    {
        return $this->hasOne(Ranks::className(), ['id' => 'rankId']);
    }

    /**
     * Gets query for [[RecipientType0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRecipientType0()
    {
        return $this->hasOne(Recipienttypes::className(), ['id' => 'recipientType']);
    }

    /**
     * Gets query for [[Units0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUnits0()
    {
        return $this->hasOne(Compensationunits::className(), ['id' => 'units']);
    }
}
