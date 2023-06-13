<?php

namespace backend\controllers;

use Yii;
use backend\models\Administrator;
use backend\models\AdministratorSearch;
use app\models\User;
use backend\models\ChangePassword;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\ForbiddenHttpException;

/**
 * 
 */
class AdministratorController extends Controller
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
    public function actionIndex()
    {
        $searchModel = new AdministratorSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Countryy model.
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
     * Creates a new Countryy model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        
            
        $modelUser = new User();
        $modelUser->checkPageAccess();
    
        
        $model = new Administrator();
        $model->scenario = 'create';

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Countryy model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

     

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', "Admin has bee updated successfully");
            return $this->redirect(['index']);
        }
        $statusDropDownData = $model->getStatusDropDownData();
       
        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionChangePassword()
    {
        
        $modelUser = new User();
        $modelUser->checkPageAccess();
    
        $model = new ChangePassword();
      
        if ($model->load(Yii::$app->request->post()) && $model->change()) {
            Yii::$app->session->setFlash('success', "Password has been changed successfully");
            return $this->redirect(['profile']);
        }
       
        return $this->render('change-password', [
            'model' => $model,
        ]);
    }

    public function actionProfile()
    {
        $id = Yii::$app->user->identity->id;
        return $this->render('profile', [
            'model' => $this->findModel($id),
        ]);

    }


    /**
     * Deletes an existing Countryy model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $modelUser = new User();
        $modelUser->checkPageAccess();
        
        $userModel= $this->findModel($id);
        $userModel->status =  Administrator::STATUS_DELETED;
        if($userModel->save(false)){
            Yii::$app->session->setFlash('success', "Admin Deleted  successfully");
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
        if (($model = Administrator::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
