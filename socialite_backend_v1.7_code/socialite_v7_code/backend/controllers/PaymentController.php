<?php
namespace backend\controllers;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\models\Payment;
use backend\models\PaymentSearch;
use yii\data\ActiveDataProvider;
use yii\imagine\Image;
use yii\web\UploadedFile;

/**
 * 
 */
class PaymentController extends Controller
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
     * Lists all  models.
     * @return mixed
     */
    public function actionIndex($type='payment_received')
    {
        
        $searchModel = new PaymentSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams,$type);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'type'=> $type
        ]);
    }

    

    public function actionUpdate($id)
    {
       
        $model = $this->findModel($id);
      //  $model->scenario = 'update';
       
       
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', "Payment  updated successfully");
            return $this->redirect(['index']);
            
                
        }
       
        return $this->render('update', [
            'model' => $model
            
       
        ]);
    }

    

    
    
    /**
     * Deletes payment
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model= $this->findModel($id);
    
        if($model->delete(false)){

            Yii::$app->session->setFlash('success', "Payment deleted successfully");

            return $this->redirect(['index']);
        }
        
    }
    
    
    
    /**
     * Finds the Countryy model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Countryy the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Payment::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
