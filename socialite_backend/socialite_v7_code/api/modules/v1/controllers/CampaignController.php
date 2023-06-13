<?php

namespace api\modules\v1\controllers;
use yii\rest\ActiveController;
use yii;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;
use api\modules\v1\models\Campaign;
use api\modules\v1\models\CampaignSearch;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBearerAuth;
use api\modules\v1\models\User;
use api\modules\v1\models\CampaignComment;
use api\modules\v1\models\CampaignFavorite;


class CampaignController extends ActiveController
{
    public $modelClass = 'api\modules\v1\models\campaign';   
    
    public function actions()
	{
		$actions = parent::actions();
		unset($actions['create'], $actions['update'], $actions['index'], $actions['delete']);                    

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


    public function actionIndex(){


        $model = new CampaignSearch();

        $result = $model->search(Yii::$app->request->queryParams);

        $response['message'] = Yii::$app->params['apiMessage']['common']['listFound'];
        
        $response['campaign']=$result;
        return $response;

        
    }

// add comment

    public function actionAddComment()
    {
        $model = new CampaignComment();
        $userId = Yii::$app->user->identity->id;
        $model->scenario = 'create';
        $model->load(Yii::$app->getRequest()->getBodyParams(), '');
        if (!$model->validate()) {
            $response['statusCode'] = 422;
            $response['errors'] = $model->errors;
            return $response;
        }
        // $campaginerid = @(int) $model->campaigner_id;
         $model->status=10;
        if ($model->save(false)) {
          
            $response['message'] = Yii::$app->params['apiMessage']['post']['commentSuccess'];

            return $response;
        } else {
            $response['statusCode'] = 422;
            $errors['message'][] = Yii::$app->params['apiMessage']['coomon']['actionFailed'];
            $response['errors'] = $errors;
            return $response;
        }
    }


    // comment list 
    public function actionCommentList()
    {   
        
        $model = new CampaignComment();
        $userId = Yii::$app->user->identity->id;
        $model->scenario = 'list';
    
        $model->load(Yii::$app->request->queryParams, '');
        if (!$model->validate()) {
            $response['statusCode'] = 422;
            $response['errors'] = $model->errors;
            return $response;
        }
        // $campaginerid = @(int) $model->campaigner_id;

        $query = $model->find()
            ->joinWith(['user' => function ($query) {
                $query->select(['id', 'name','username', 'image','is_chat_user_online','chat_last_time_online','location','latitude','longitude']);
            }]);

        $result = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        $response['message'] = 'ok';
        $response['comment'] = $result;

        return $response;

    }
    
    // Add  Favrioute
    public function actionAddFavorite()
    {
      
        $userId                 = Yii::$app->user->identity->id; 
        $model                  =   new Campaign();
        $modelCampaignFavorite  =   new CampaignFavorite();
        $modelUser   =   new User();
        //   $model->scenario ='addFavorite';
        if (Yii::$app->request->isPost) {
           
            $model->load(Yii::$app->getRequest()->getBodyParams(), '');
          
            if(!$model->validate()) {
              
                $response['statusCode']=422;
                $response['errors']=$model->errors;
                return $response;
            }
            
           $campaignId = @(int) $model->id;
           
           $resulCampaign     = $model->findOne($campaignId);
           
            if(!$resulCampaign){
                $response['statusCode']=422;
                $errors['message'][] = Yii::$app->params['apiMessage']['common']['noRecord'];
                $response['errors']=$errors;
                return $response;
            
            }

            $resultCount =$modelCampaignFavorite->find()->where(['user_id'=>$userId,'campaign_id'=>$campaignId])->count();

            if($resultCount>0){
                $response['statusCode']=422;
                $errors['message'][] = Yii::$app->params['apiMessage']['podcast']['alreadyFavorite'];
                $response['errors']=$errors;
                return $response;
            
            }

            //resultUser
            $modelCampaignFavorite->user_id       =   $userId;
            $modelCampaignFavorite->campaign_id    =   $campaignId;
            
            if($modelCampaignFavorite->save()){
               
                
                $response['message']=Yii::$app->params['apiMessage']['podcast']['AddFavorite'];
                return $response; 
            }else{

                $response['statusCode']=422;
                $errors['message'][] = Yii::$app->params['apiMessage']['common']['actionFailed'];
                $response['errors']=$errors;
                return $response;
            
            }

            
        }
 
    }

    


    public function actionRemoveFavorite()
    {
        $userId                 = Yii::$app->user->identity->id;
        $model                  =   new Campaign();
        $modelLiveTvFavorite  =   new CampaignFavorite();
        $modelUser   =   new User();
        // $model->scenario ='removeFavorite';
        if (Yii::$app->request->isPost) {
            $model->load(Yii::$app->getRequest()->getBodyParams(), '');
            if(!$model->validate()) {
                $response['statusCode']=422;
                $response['errors']=$model->errors;
                return $response;
            }
             $campaigneId = @(int) $model->id;
       
           $resultampaigner     = $model->findOne($campaigneId);

            if(!$resultampaigner){
                $response['statusCode']=422;
                $errors['message'][] = Yii::$app->params['apiMessage']['common']['noRecord'];
                $response['errors']=$errors;
                return $response;
            
            }


            $resultFavorite =$modelLiveTvFavorite->find()->where(['user_id'=>$userId,'campaign_id'=>$campaigneId])->one();

            if(!$resultFavorite){
                $response['statusCode']=422;
                $errors['message'][] = Yii::$app->params['apiMessage']['common']['noRecord'];
                $response['errors']=$errors;
                return $response;
            
            }


        
            if($resultFavorite->delete()){
               
                
                $response['message']=Yii::$app->params['apiMessage']['podcast']['removedFavorite'];
                return $response; 
            }else{

                $response['statusCode']=422;
                $errors['message'][] = Yii::$app->params['apiMessage']['common']['actionFailed'];
                $response['errors']=$errors;
                return $response;
            
            }

            
        }
        
    }

   
    // public function actionMyFavoriteList(){
    //     $model =  new Campaign();
    //     $modelRes= $model->find()->one();
        
       
    //    $response['message']='ok';
    //     $response['campaign']=$modelRes;
    //     return $response;
    // }

    public function actionMyFavoriteList()
    {

        $model = new CampaignSearch();

        $result = $model->CampaignMyFavorite(Yii::$app->request->queryParams);

        $response['message'] = Yii::$app->params['apiMessage']['common']['listFound'];
        
        $response['campaignFavoriteList']=$result;
        return $response;

        
    }


    


       
    

   





}


