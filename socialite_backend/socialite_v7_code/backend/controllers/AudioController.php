<?php
namespace backend\controllers;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\models\Audio;
use backend\models\AudioSearch;
use yii\data\ActiveDataProvider;
use yii\imagine\Image;
use yii\web\UploadedFile;
use common\models\Category;
use yii\helpers\ArrayHelper;
use common\models\FileUpload;

/**
 * 
 */
class AudioController extends Controller
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
        
        $searchModel = new AudioSearch();
        $modelCategory = new Category();
        
        $resultCategory = $modelCategory->find()->select(['id','name'])->where(['type'=>Category::TYPE_REEL_AUDIO])->andWhere(['<>', 'status', Category::STATUS_DELETED])->all();
        $mainCategoryData = ArrayHelper::map($resultCategory,'id','name');
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'mainCategoryData'=> $mainCategoryData
        ]);
        
      
    }

    /**
     * Displays 
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model  = $this->findModel($id);
        
        return $this->render('view', [
            'model' =>   $model
        ]);
    }

    /**
     
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
       
        $model = new Audio();
        $modelCategory = new Category();
       
        
        $resultCategory = $modelCategory->find()->select(['id','name'])->where(['type'=>Category::TYPE_REEL_AUDIO])->andWhere(['<>', 'status', Category::STATUS_DELETED])->all();
        $mainCategoryData = ArrayHelper::map($resultCategory,'id','name');

        
        $modelFileUpload = new FileUpload();
      
        $model->scenario = 'create';

        if ($model->load(Yii::$app->request->post()) ) {
            
            $model->audioFile = UploadedFile::getInstance($model, 'audioFile');
            $model->imageFile = UploadedFile::getInstance($model, 'imageFile');

            
            if($model->validate()){
            
            
                if($model->audioFile){
                    
                    $imageType =     FileUpload::TYPE_REEL_AUDIO;
                    $files = $modelFileUpload->uploadFile($model->audioFile,$imageType,false);
                    $model->audio 		= 	  $files[0]['file']; 

                }
                if($model->imageFile){
                    $imageType =     FileUpload::TYPE_REEL_AUDIO;
                    $files = $modelFileUpload->uploadFile($model->imageFile,$imageType,false);
                    $model->image 		= 	  $files[0]['file']; 

                }
                
                
                if($model->save(false)){
            
                Yii::$app->session->setFlash('success', "Audio created successfully");
                return $this->redirect(['index']);
                }
            }
        }
        return $this->render('create', [
            'model' => $model,
            'mainCategoryData'=>$mainCategoryData
            
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
        
        $modelFileUpload = new FileUpload();
        $model = $this->findModel($id);
        $modelCategory = new Category();


        $resultCategory = $modelCategory->find()->select(['id','name'])->where(['type'=>Category::TYPE_REEL_AUDIO])->andWhere(['<>', 'status', Category::STATUS_DELETED])->all();
        $mainCategoryData = ArrayHelper::map($resultCategory,'id','name');


        $preAudio = $model->audio;
        $preImage = $model->image;

        if ($model->load(Yii::$app->request->post())) {
            $model->audioFile = UploadedFile::getInstance($model, 'audioFile');
            $model->imageFile = UploadedFile::getInstance($model, 'imageFile');
            
            if($model->validate()){
                if($model->audioFile){
                        
                    $imageType =     FileUpload::TYPE_REEL_AUDIO;
                    $files = $modelFileUpload->uploadFile($model->audioFile,$imageType,false);
                    $model->audio 		= 	  $files[0]['file']; 
    
                }
                if($model->imageFile){
                    $imageType =     FileUpload::TYPE_REEL_AUDIO;
                    $files = $modelFileUpload->uploadFile($model->imageFile,$imageType,false);
                    $model->image 		= 	  $files[0]['file']; 
                  
                }
                
            
                if($model->save()){
                    
                    Yii::$app->session->setFlash('success', "Audio updated successfully");
                    return $this->redirect(['index']);
                };
                
            }
        }
    
      
        return $this->render('update', [
            'model' => $model,
            'mainCategoryData'=>$mainCategoryData
    
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
        $model= $this->findModel($id);
        $model->status =  $model::STATUS_DELETED;
        if($model->save(false)){

            Yii::$app->session->setFlash('success', "Audio deleted successfully");

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
        if (($model = Audio::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
