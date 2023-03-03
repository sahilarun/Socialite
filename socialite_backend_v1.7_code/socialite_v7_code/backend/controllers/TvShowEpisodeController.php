<?php

namespace backend\controllers;

use Yii;
//use app\models\User;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\models\TvShow;
use yii\web\UploadedFile;
use common\models\FileUpload;
use yii\helpers\ArrayHelper;
use common\models\TvShowEpisode;
use backend\models\TvShowSearch;
use backend\models\TvShowEpisodeSearch;
/**
 * 
 */
class TvShowEpisodeController extends Controller
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
        // echo "hello";
        $searchModel = new TvShowEpisodeSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        // print_r($dataProvider);
        $modelTvShow = new TvShow();
        $resultTvShow = $modelTvShow->find()->select(['id','name'])->all();
        $tvShowData = ArrayHelper::map($resultTvShow,'id','name');
        // print_r($tvShowData);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'tvShowData'=>$tvShowData
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
        $searchModel = new TvShowEpisodeSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere('tv_show_episode.tv_show_id = '.$id);

        $modelTvShow = new TvShow();
        $resultTvShow = $modelTvShow->find()->select(['id','name'])->where('tv_show.id = '.$id)->one();
        $tvShowData = ArrayHelper::map($resultTvShow,'id','name');
        $tvShowName=  $modelTvShow->findOne($id);
        // print_r($tvShowName);
        return $this->render('view', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'tvShowData'=>$tvShowName
        ]);
    }

        /**
     * Creates a new Countryy model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
       
        $model = new TvShowEpisode();
      
        $model->scenario = 'create';

        $modelTvShow = new TvShow();
        $resultTvShow = $modelTvShow->find()->select(['id','name'])->all();
        $tvShowData = ArrayHelper::map($resultTvShow,'id','name');
    //    print_r($channelData);
    //     die("jdk");
        if ($model->load(Yii::$app->request->post()) ) {
            $model->created_at              = strtotime($model->created_at);
            $model->imageFile = UploadedFile::getInstance($model, 'imageFile');
            $model->video = UploadedFile::getInstance($model, 'video');
            if($model->validate()){
                if($model->imageFile){
                    $modelFileUpload = new FileUpload();
                    $type =     FileUpload::TYPE_TV_SHOW_EPISODE;
                    $files = $modelFileUpload->uploadFile($model->imageFile,$type,false);
                    $model->image 		= 	  $files[0]['file']; 
                    
                }
                if($model->video){
                    $modelFileUpload = new FileUpload();
                    $type =     FileUpload::TYPE_TV_SHOW_EPISODE;
                    $files = $modelFileUpload->uploadFile($model->video,$type,false);
                    $model->video 		= 	  $files[0]['file']; 
                    
                }

                if($model->save()){
                    Yii::$app->session->setFlash('success', "Tv Show Episode created successfully");
                    // return $this->redirect(['index']);
                    $this->redirect(\Yii::$app->urlManager->createUrl(["tv-show"]));
                }
            }
            
        }
        // print_r($channelData);
        // exit("kf;   ");

        return $this->render('create', [
            'model' => $model,
            'tvShowData'=>$tvShowData
            
            
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

            Yii::$app->session->setFlash('success', "Tv Show deleted successfully");

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
        if (($model = TvShowEpisode::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
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
        // $model = new TvShow();
      
        $model->scenario = 'update';
        // $modelCategory = new Category();
        // $resultCategory = $modelCategory->find()->select(['id','name'])->where(['and',['type'=>[3]]])->andWhere(['and', 'status', Category::STATUS_ACTIVE])->all();
        // $categoryData = ArrayHelper::map($resultCategory,'id','name');

        $modelTvShow = new TvShow();
        $resultTvShow = $modelTvShow->find()->select(['id','name'])->all();
        $tvShowData = ArrayHelper::map($resultTvShow,'id','name');
       

        if ($model->load(Yii::$app->request->post()) ) {
            
            $model->imageFile = UploadedFile::getInstance($model, 'imageFile');
            $model->video = UploadedFile::getInstance($model, 'video');
            if($model->validate()){
                $model->created_at  = strtotime($model->created_at);
                if($model->imageFile){
                    $modelFileUpload = new FileUpload();
                    $type =     FileUpload::TYPE_TV_SHOW;
                    $files = $modelFileUpload->uploadFile($model->imageFile,$type,false);
                    $model->image 		= 	  $files[0]['file']; 
                    
                }
                if($model->video){
                    $modelFileUpload = new FileUpload();
                    $type =     FileUpload::TYPE_TV_SHOW_EPISODE;
                    $files = $modelFileUpload->uploadFile($model->video,$type,false);
                    $model->video 		= 	  $files[0]['file']; 
                    
                }
                if($model->save()){
                    Yii::$app->session->setFlash('success', "Tv Show updated data successfully");
                    // return $this->redirect(['index']);
                    return $this->goBack(Yii::$app->request->referrer);
                }
            }
            
        }else{
            $model->created_at              = date('Y-m-d',$model->created_at);
        }  
        return $this->render('update', [
            'model' => $model,
            'tvShowData'=>$tvShowData
    
        ]);
    
    }



}