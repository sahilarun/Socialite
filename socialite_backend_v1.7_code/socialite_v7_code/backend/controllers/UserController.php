<?php

namespace backend\controllers;

use Yii;
use app\models\User;
use backend\models\UserSearch;
use backend\models\ChangePassword;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\models\Country;
use common\models\ReportedUser;
use yii\web\UploadedFile;
use yii\imagine\Image;
use common\models\FileUpload;


/**
 * 
 */
class UserController extends Controller
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
        $searchModel = new UserSearch();
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
        $model  = User::find()
        ->joinWith('country')
        ->where(['user.id'=>$id])
        ->one();
        
        return $this->render('view', [
            'model' =>   $model
        ]);
    }


    /**
     * Lists all  models.
     * @return mixed
     */
    public function actionReportedUser()
    {
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->searchReportedUser(Yii::$app->request->queryParams);

        return $this->render('reported-user', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionViewReportedUser($id)
    {
        $model  = User::find()
        ->joinWith('country')
        ->where(['user.id'=>$id])
        ->one();
        
        return $this->render('view-reported-user', [
            'model' =>   $model
        ]);
    }

    
    
    public function actionReportedUserAction($id, $type)
    {
        $modelReportedUser = new ReportedUser();
        $model = $this->findModel($id);
        if($type=='cancel'){
           
            $currentTime = time();
            $modelReportedUser->updateAll(['status' => ReportedUser::STATUS_REJECTED,'resolved_at'=>$currentTime], [ 'report_to_user_id' => $id,'status'=> ReportedUser::STATUS_PENDING]);
            Yii::$app->session->setFlash('success', "Reported request cancelled successfully");
                return $this->redirect(['reported-user']);
        }else if($type=='block'){
            
            $currentTime = time();
            $modelReportedUser->updateAll(['status' => ReportedUser::STATUS_ACEPTED,'resolved_at'=>$currentTime], [ 'report_to_user_id' => $id,'status'=> ReportedUser::STATUS_PENDING]);
            
            $model->status = $model::STATUS_INACTIVE;
            if ($model->save(false)) {
                Yii::$app->session->setFlash('success', "User Inactive successfully");
                return $this->redirect(['reported-user']);
            }
        }
       
        
        
    }


    /**
     * Creates a new Countryy model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new User();
        $modelCountry = new Country();
        $model->scenario = 'create';

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            
            $model->imageFile = UploadedFile::getInstance($model, 'imageFile');
           // $model->image  = $model->upload();
           if($model->imageFile){
				
                $microtime 			= 	(microtime(true)*10000);
                $uniqueimage		=	$microtime.'_'.date("Ymd_His").'_'.substr(md5($microtime),0,10); 
                $imageName 			=	$uniqueimage;
                $model->image 		= 	$imageName.'.'.$model->imageFile->extension; 
                $imagePath 			=	Yii::$app->params['pathUploadUser'] ."/".$model->image;
                $imagePathThumb 	=	Yii::$app->params['pathUploadUserThumb'] ."/".$model->image;
                $imagePathMedium 	=	Yii::$app->params['pathUploadUserMedium'] ."/".$model->image;
                $model->imageFile->saveAs($imagePath,false);
                
                
                Image::thumbnail($imagePath, 500, 500)
                        ->save($imagePathMedium, ['quality' => 100]);

                Image::thumbnail($imagePath, 120, 120)
                        ->save($imagePathThumb, ['quality' => 100]);

            
            }
            
            if($model->save()){
           
            Yii::$app->session->setFlash('success', "USer created successfully");
            return $this->redirect(['index']);
            }
        }

     /*   if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        }*/
        $countryData = $modelCountry->getCountryDropdown();
       

        return $this->render('create', [
            'model' => $model,
            'countryData'=> $countryData 
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
        $preImage = $model->image;
        $modelCountry = new Country();
        
        $countryData = $modelCountry->getCountryDropdown();
       
        
        
        
       /* $res = Yii::$app->fs->read();

        print_r($res);*/
        
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {

            $modelUser = new User();
            $modelUser->checkPageAccess();
            
            
            $model->imageFile = UploadedFile::getInstance($model, 'imageFile');
           // $model->image  = $model->upload();
            if($model->imageFile){
                $modelFileUpload = new FileUpload();
                $type =     FileUpload::TYPE_USER;
                $files = $modelFileUpload->uploadFile($model->imageFile,$type,false);
                
                $model->image 		= 	  $files[0]['file']; 

                /*$microtime 			= 	(microtime(true)*10000);
                $uniqueimage		=	$microtime.'_'.date("Ymd_His").'_'.substr(md5($microtime),0,10); 
                $imageName 			=	$uniqueimage.'.'.$model->imageFile->extension;
                $model->image 		= 	$imageName; 
                $s3 = Yii::$app->get('s3');
                $imagePath = $model->imageFile->tempName;
                $result = $s3->upload('./'.Yii::$app->params['pathUploadUserFolder'].'/'.$imageName, $imagePath);

                $s3->commands()->delete('./'.Yii::$app->params['pathUploadUserFolder'].'/'.$preImage)->execute(); /// delete previous
                */
            
            }
            
            if($model->save()){
           
              Yii::$app->session->setFlash('success', "User updated successfully");
              return $this->redirect(['view', 'id' => $model->id]);
            }
        }

       /* if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }*/
        $statusDropDownData = $model->getStatusDropDownData();
       
        return $this->render('update', [
            'model' => $model,
            'countryData'=>$countryData
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
        $userModel->status =  USER::STATUS_DELETED;
        $userModel->save(false);
        return $this->redirect(['index']);
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
        if (($model = User::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
