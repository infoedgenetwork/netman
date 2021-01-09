<?php

namespace app\modules\payments\controllers;

use Yii;
use frontend\modules\payments\models\Inpayments;
use frontend\modules\payments\models\InpaymentsSearch;
use frontend\modules\payments\models\Packregistration;
use frontend\modules\payments\models\Packconfig;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\db\Exception;
use yii\helpers\Json;

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

    public function actionPackregistration($member)
    {
         $session = Yii::$app->session;
         ////
         $memberDetails=Yii::$app->memberdetails;
         /////
        $model = new Packregistration();
        $model->member = $member;
        $model->ptype = 1;
        if ($model->load(Yii::$app->request->post())&& $model->validate()) {
            try{
                $model->keepValues();
                $session->setFlash('success', 'Payment details successfully updated. Please await approval');
                $this->redirect(['awaitapproval']);
            }
            catch (Exception $e){
                $session->setFlash('error', 'Error saving payment: '.$e->getMessage());
            }
        }

        return $this->render('packregistration', [
            'model' => $model,
            /////
            'memberDetails' => $memberDetails,
            /////
        ]);
    }
    
    public function actionAwaitapproval()
    {
        $model = new Packregistration();
        $memberDetails = Yii::$app->memberdetails;
         return $this->render('awaitapproval',
                 [
                     'model' => $model,
                     'memberDetails'=>$memberDetails,
                     ]);
    }
    
    /**
     * Creates a new Inpayments model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $session = Yii::$app->session;
        $model = new Inpayments();

        if ($model->load(Yii::$app->request->post()) ) {
            $model->recordBy = Yii::$app->user->id;
            $model->recordDate = date('Y-m-d H:i:s');
            $model->save();
            $session->setFlash('success','Payment Recorded');
            //return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
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
    public function actionPackValue($packid,$ptype){
        $model = Packconfig::find()
                ->where([
                    'packId'=>$packid,
                    'trxType'=>$ptype
                    ])
                ->one();
        echo Json::encode($model);
    }
}
