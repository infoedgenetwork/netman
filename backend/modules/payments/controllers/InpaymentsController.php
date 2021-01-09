<?php

namespace app\modules\payments\controllers;

use Yii;
use backend\modules\payments\models\Inpayments;
use backend\modules\payments\models\InpaymentsSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;

use common\models\Category;
use backend\modules\payments\models\Failedpayreasons;

/**
 * InpaymentsController implements the CRUD actions for Inpayments model.
 */
class InpaymentsController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Inpayments models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new InpaymentsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    
    /**
     * Lists all Inpayments models.
     * @return mixed
     */
    public function actionCheckpay()
    {
        
        $searchModel = new InpaymentsSearch();
        $dataProvider = $searchModel->searchUnpaid(Yii::$app->request->queryParams);

        return $this->render('checkpay', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Inpayments model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Inpayments model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Inpayments();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }
    
    /**
     * 
     * @return type
     */
    public function actionConfirmpay($memberId)
    {
        $session = Yii::$app->session;
        $memberDetails = Yii::$app->memberdetails;
        $model = Inpayments::find()->where(['member'=>$memberId])->one();
        $model->scenario ='confirmpay';
        //$model->confirmed = 0;
        //$model->scenario= 'confirmpay';
        if ($model->load(Yii::$app->request->post())&& $model->validate()) {
            $model->confirmBy = Yii::$app->user->id;
            $model->confirmDate = date('Y-m-d H:i:s');
            $model->save();
            $session->setFlash('success', 'Payment successfully confirmed!');
            if ($model->confirmed) {
                // form inputs are valid, do something here
                /*$session->addFlash('success',*/$this->updateSponsor($model);//);
                
            }else{
                $this->logFailedResons($model);
                $session->addFlash('warning','Payment was not confirmed');
            }
            //$this->redirect(['checkpay']);
        }

        return $this->render('confirmpay', [
            'model' => $model,
        ]);
    }

    private function logFailedResons(&$model){
        if($model->dirtyAttributes){
            $mylogModel=new Failedpayreasons();
            $mylogModel->inpaymentId = $model->id;
            $mylogModel->rejectedReason = $model->comments;
            $mylogModel->rejectedDate = $model->confirmDate;
            $mylogModel->rejectedBy =   $model->confirmBy;
            $mylogModel->save();
        }
    }
    /**
     * Updates an existing Inpayments model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Inpayments model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Inpayments model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Inpayments the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Inpayments::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
    
    public function addCategory($parentName){
        //if category table is empty create root node;
        $model =new Category();
        if($this->isRootRequired()){
            $model->makeRoot(['name'=>$parentName]);
        }else
            {
                $parent = Category::findOne($parent_id);
                $model->appendTo($parent);
            }
    }
    private function isRootRequired()
    {
        $itemCnt =  (new \yii\db\Query())
                ->select(['*'])
                ->from('category')
                ->count();
        return $itemCnt==0?1:0;
    }
    public function updateSponsor($model){
                $msg='';
                $memberDetails=Yii::$app->memberdetails;
                $memberId = $model->member;
                $msg.='MemberId: '.$memberId;
                $userId = Yii::$app->user->id;
                // update sponsorship table
                $sponsorNo=$memberDetails->getTempSponsorDetails2($memberId);
                $msg.='<br>sponsorNo: '.$sponsorNo;
                $side = $memberDetails->getTempSponsorDetails2($memberId,2);
                $msg.='<br>Side: '.$side;
                //$msg= 'Sponsor found(tempSponsor): '.$sponsor.'<br>';
                $sponsorId = $memberDetails->getMemberPartsUsingMemberNo($sponsorNo);
                $msg.='<br>sponsorId: '.$sponsorId;
                $parentNo=$memberDetails->getTempSponsorDetails2($memberId);
                $msg.='<br>ParentNo: '.$parentNo;
                $parentId = $memberDetails->getTempSponsorDetails2($memberId,3)>0?$memberDetails->getMemberPartsUsingMemberNo($memberDetails->getTempSponsorDetails2($memberId,3)):0;
                $msg.='<br>ParentId: '.$parentId;
                $parent= $memberDetails->getParent($sponsorId,$parentId);
                $msg.='<br>Parent: '.$parent;
                $position = $memberDetails->getNextPosition($parentId,$side);
                $msg.='<br>Position: '.$position;
                $msg .= $memberDetails->addChild($memberId,$parent,$sponsorId,$position);
                return $msg;
    }
    
}
