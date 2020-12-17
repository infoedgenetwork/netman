<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace frontend\modules\payments\models;

use Yii;
use yii\base\Model;

/**
 * Description of Packregistration
 *
 * @author Apache1
 */
class Packregistration extends Model{
    public $member;//memberId
    public $package; 
    public $ptype;
    public $amount;
    public $pMethod;
    public $trxNo;
    //public $comments;


    public function rules() {
        return [
            
            [[ 'amount', 'package'], 'required'],
            [['member', 'ptype','package'], 'integer'],
            [['amount'], 'number'],
            
            [['trxNo'], 'string', 'max' => 30],
            //[['comments'], 'string', 'max' => 45],
            [['member'], 'exist', 'skipOnError' => true, 'targetClass' => \frontend\modules\basic\models\People::className(), 'targetAttribute' => ['member' => 'id']],
            [['pMethod'], 'exist', 'skipOnError' => true, 'targetClass' => Paymethods::className(), 'targetAttribute' => ['pMethod' => 'id']],
            [['ptype'], 'exist', 'skipOnError' => true, 'targetClass' => Paymenttypes::className(), 'targetAttribute' => ['ptype' => 'id']],
            [['package'], 'exist', 'skipOnError' => true, 'targetClass' => Packages::className(), 'targetAttribute' => ['package' => 'id']],
        
        ];
    }
    
    public function attributeLabels()
    {
        return [
           
            'member' => Yii::t('app', 'Member'),
            'ptype' => Yii::t('app', 'Payment For'),
            'package' => Yii::t('app', 'Package'),
            'amount' => Yii::t('app', 'Amount ($)'),
            
            'pMethod' => Yii::t('app', 'Payment Method'),
            'trxNo' => Yii::t('app', 'Transaction No'),
            //'comments' => Yii::t('app', 'Comments'),
            
        ];
    }
    
    public function keepValues(){
        $dbmodel = new Inpayments();
        $dbmodel->member= $this->member;
        $dbmodel->amount = $this->amount;
        $dbmodel->pdate = date('Y-m-d H:i::s');
        $dbmodel->pMethod = $this->pMethod;
        $dbmodel->package = $this->package;
        $dbmodel->ptype = $this->ptype;
        $dbmodel->transactionNo = $this->trxNo;
        $dbmodel->recordBy = Yii::$app->user->id;
        $dbmodel->recordDate = date('Y-m-d H:i::s');
        $dbmodel->save();
        
    }
}
