<?php
namespace api\modules\v1\controllers;
use Yii;
use yii\rest\ActiveController;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBearerAuth;
use api\modules\v1\models\User;
use api\modules\v1\models\FileUpload;


use yii\web\UploadedFile;
class FileUploadController extends ActiveController
{
    //public $modelClass = 'api\modules\v1\models\fileUpload';   
     public $modelClass = 'common\models\FileUpload';   
    
    public function actions()
	{
		$actions = parent::actions();

		// disable default actions
		unset($actions['create'], $actions['update'], $actions['index'], $actions['delete'], $actions['view']);                    

		return $actions;
	}    

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => CompositeAuth::className(),
            'except'=>[],
            'authMethods' => [
                HttpBearerAuth::className()
            ],
        ];
        return $behaviors;
    }





    
    public function actionUploadFile()
    {
        
       

        $model = new \yii\base\DynamicModel([
            'mediaFile','type'
        ]);
        $model->addRule(['mediaFile','type'], 'required')
            //->addRule(['mediaFile'], 'file');
            ->addRule(['mediaFile'], 'file');

           
        $modelFileUpload = new FileUpload();
        
        if (Yii::$app->request->isPost) {
           
            //$model->mediaFile = UploadedFile::getInstances($model, 'mediaFile');  
             $model->mediaFile = UploadedFile::getInstanceByName('mediaFile'); 
            
            $model->load(Yii::$app->getRequest()->getBodyParams(), '');
            $model->mediaFile = UploadedFile::getInstanceByName('mediaFile'); 
            if(!$model->validate()) {
                $response['statusCode']=422;
                $response['errors']=$model->errors;
                return $response;
            }
            $fileUrl;


            $type = ($model->type)?$model->type:1;
            $files = $modelFileUpload->uploadFile($model->mediaFile,$type,false);

            $response['message']='File uploaded successfully';
            $response['files']=$files;
            return $response; 


         
        }
    }   

   

}


