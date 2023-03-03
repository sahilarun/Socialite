<?php
namespace api\modules\v1\controllers;
use Yii;
use yii\rest\ActiveController;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBearerAuth;
use api\modules\v1\models\FavoriteAd;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;
use api\modules\v1\models\Follower;
use api\modules\v1\models\User;
use api\modules\v1\models\Notification;
use api\modules\v1\models\BlockedUser;




class FollowerController extends ActiveController
{
    public $modelClass = 'api\modules\v1\models\follower';   

    
    public $serializer = [
        'class' => 'yii\rest\Serializer',
        'collectionEnvelope' => 'items',
    ];
    
    
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

    

    public function actionCreate()
    {
       
        $model = new Follower();
        $followerId = Yii::$app->user->identity->id;
        
        $model->scenario ='create';
        $model->load(Yii::$app->getRequest()->getBodyParams(), '');
        if(!$model->validate()) {
            $response['statusCode']=422;
            $response['errors']=$model->errors;
        
            return $response;
        }
        $userId   =  @(int) $model->user_id;
        $totalCount = $model->find()->where(['follower_id'=>$followerId, 'user_id'=>$userId])->count();
        if($totalCount>0){
           $response['statusCode']=422;
           $errors['message'][] = "You have already follow this user";
           $response['errors']=$errors;
          
          return $response; 
        }
         if($model->save(false)){

             //// push notification 
            /*
            
             $modelUser = new User();
             $userResult = $modelUser->findOne($userId);

             $title                                     =   'New follower';
             $message 					                =   Yii::$app->user->identity->name.' has following you now';
 
             if($userResult->device_token){
                 
                
                 $dataPush['title']	        	        	=	$title;
                 $dataPush['body']		                	=	$message;
                 $dataPush['data']['notification_type']		=	'newFollower';
                 $dataPush['data']['follower_id']		      =	$followerId;
                 $deviceTokens[] 					        =    $userResult->device_token;
                
                 Yii::$app->pushNotification->sendPushNotification($deviceTokens, $dataPush);
                 
             }
            /// add notification to list

               $modelNotification                 = new Notification();
               $modelNotification->user_id        =  $userId;
               $modelNotification->type           =   Notification::TYPE_NEW_FOLLOWER;
               $modelNotification->reference_id   =  $followerId;
               $modelNotification->title          =  $title;
               $modelNotification->message        =   $message;
               $modelNotification->save(false);
             /// end add notification to list

             */


             // send notification 
           
            $modelNotification = new Notification();
            $notificationInput = [];
            $notificationData =  Yii::$app->params['pushNotificationMessage']['newFollower'];
            $replaceContent=[];   
            $replaceContent['USER'] = Yii::$app->user->identity->username;
            $notificationData['body'] = $modelNotification->replaceContent($notificationData['body'],$replaceContent);   
           
            $userIds=[];
            $userIds[]   =   $userId;
           
            $notificationInput['referenceId'] = $followerId;
            $notificationInput['userIds'] = $userIds;
            $notificationInput['notificationData'] = $notificationData;

            
            $modelNotification->createNotification($notificationInput);
            
            // end send notification 
 




             $response['message']='Added in your following list';
             return $response; 
         }else{
             $response['statusCode']=422;
             $errors['message'][] = "Not added successfully in your following list";
             $response['errors']=$errors;
             return $response; 
         }
    }

    public function actionFollowMultiple()
    {
       
        $model = new Follower();
        $followerId = Yii::$app->user->identity->id;
        
        $model->scenario ='createMultiple';
        $model->load(Yii::$app->getRequest()->getBodyParams(), '');
        if(!$model->validate()) {
            $response['statusCode']=422;
            $response['errors']=$model->errors;
        
            return $response;
        }
        $userIds   =  $model->user_ids;
        $userIdsArr = explode(',',$userIds);


        $userIdsArr = array_unique($userIdsArr);
        $process=false;
        $alreadyAdded=true;
        
        foreach($userIdsArr as $userId){

        
            $totalCount = $model->find()->where(['follower_id'=>$followerId, 'user_id'=>$userId])->count();
            if($totalCount==0){
                $alreadyAdded=false;
                $modelFollower = new Follower();
                $modelFollower->user_id = $userId;    
                 if($modelFollower->save(false)){
                    $process=true;
                    //// push notification 
                    /*
                    
                    $modelUser = new User();
                    $userResult = $modelUser->findOne($userId);

                    $title                                     =   'New follower';
                    $message 					                =   Yii::$app->user->identity->name.' has following you now';
        
                    if($userResult->device_token){
                        
                        
                        $dataPush['title']	        	        	=	$title;
                        $dataPush['body']		                	=	$message;
                        $dataPush['data']['notification_type']		=	'newFollower';
                        $dataPush['data']['follower_id']		      =	$followerId;
                        $deviceTokens[] 					        =    $userResult->device_token;
                        
                        Yii::$app->pushNotification->sendPushNotification($deviceTokens, $dataPush);
                        
                    }
                    /// add notification to list

                    $modelNotification                 = new Notification();
                    $modelNotification->user_id        =  $userId;
                    $modelNotification->type           =   Notification::TYPE_NEW_FOLLOWER;
                    $modelNotification->reference_id   =  $followerId;
                    $modelNotification->title          =  $title;
                    $modelNotification->message        =   $message;
                    $modelNotification->save(false);
                    /// end add notification to list
                    */


                    
                    // send notification 
                
                    $modelNotification = new Notification();
                    $notificationInput = [];
                    $notificationData =  Yii::$app->params['pushNotificationMessage']['newFollower'];
                    $replaceContent=[];   
                    $replaceContent['USER'] = Yii::$app->user->identity->username;
                    $notificationData['body'] = $modelNotification->replaceContent($notificationData['body'],$replaceContent);   
                
                    $userIds=[];
                    $userIds[]   =   $userId;
                
                    $notificationInput['referenceId'] = $followerId;
                    $notificationInput['userIds'] = $userIds;
                    $notificationInput['notificationData'] = $notificationData;

                    
                    $modelNotification->createNotification($notificationInput);
                    // end send notification 



        

                }
            }
        }

        
             
        if($process){
        
            $response['message']='Added in your following list';
            return $response; 
        }elseif($alreadyAdded){
            $response['statusCode']=422;
            $errors['message'][] = 'User already added in your  following list';
            $response['errors']=$errors;
            return $response; 

            
        }else{
            $response['statusCode']=422;
            $errors['message'][] = "Not added successfully in your following list";
            $response['errors']=$errors;
            return $response; 
            }
    }

    public function actionUnfollow()
    {
       
        $model = new Follower();
        $followerId = Yii::$app->user->identity->id;
        $model->scenario ='create';
        $model->load(Yii::$app->getRequest()->getBodyParams(), '');
        if(!$model->validate()) {
            $response['statusCode']=422;
            $response['errors']=$model->errors;
        
            return $response;
        }

        $userId   =  @(int) $model->user_id;
        $result = $model->find()->where(['follower_id'=>$followerId, 'user_id'=>$userId])->one();

        if(isset($result->id)){
            if($result->delete()){
       
                $response['message']='Unfollow successfully';
                return $response; 
            }else{
                $response['statusCode']=422;
                $errors['message'][] = "Unfollwo not successfully done";
                $response['errors']=$errors;
                
                return $response; 
            }

        }else{
            $response['statusCode']=422;
            $errors['message'][] = "Action Failed";
            $response['errors']=$errors;
            return $response; 

        }
    }


    


    public function actionMyFollower()
    {
        
        //$userId = Yii::$app->user->identity->id;
        $userId =  (int)Yii::$app->request->queryParams['user_id'];
        $model = new Follower();

        
        $modleBlockedUser = new BlockedUser();
        $userIdsBlockedMe = $modleBlockedUser->getUserIdsWhomeBlockMe($userId);

       
        $query = $model->find()
        ->where(['user_id'=>$userId])
        ->andWhere(['NOT',['follower_id'=>$userIdsBlockedMe]])
        ->with(['followerUserDetail'=> function ($query) {
            $query->select(['user.id','user.username','user.email','user.bio','user.description','user.image','user.is_verified','user.country_code','user.phone','user.country','user.city','user.sex','user.dob','user.is_chat_user_online','user.chat_last_time_online','user.location','user.latitude','user.longitude']);
            
        }]);
        
        

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 20,
            ]
        ]);

        
        $response['message']=Yii::$app->params['apiMessage']['common']['listFound'];
        $response['follower']= $dataProvider;
        return $response;
       
    }

    public function actionMyFollowingLive()
    {
        
        $userId =  (int)Yii::$app->request->queryParams['user_id'];
        $modleBlockedUser = new BlockedUser();
        $userIdsBlockedMe = $modleBlockedUser->getUserIdsWhomeBlockMe($userId);
        
        $model = new Follower();
       
        $query = $model->find()
        ->where(['follower_id'=>$userId])
        ->andWhere(['NOT',['follower.user_id'=>$userIdsBlockedMe]])
        ->with(['followingUserDetail'=> function ($query) {
            $query->select(['user.id','user.username','user.email','user.bio','user.description','user.image','user.is_verified','user.country_code','user.phone','user.country','user.city','user.sex','user.dob','user.is_chat_user_online','user.chat_last_time_online','user.location','user.latitude','user.longitude']);
            
        }])
        ->joinWith('followingUserDetail.userLiveDetail');
       
        $result = $query->all();
        
        
        
        $response['message']=Yii::$app->params['apiMessage']['common']['listFound'];
        $response['following']= $result;
        return $response;
       
    }

    public function actionMyFollowing()
    {
        //$userId = Yii::$app->user->identity->id;
        $userId =  (int)Yii::$app->request->queryParams['user_id'];
        $modleBlockedUser = new BlockedUser();
        $userIdsBlockedMe = $modleBlockedUser->getUserIdsWhomeBlockMe($userId);
        
        $model = new Follower();
       
        $query = $model->find()
        ->where(['follower_id'=>$userId])
        ->andWhere(['NOT',['user_id'=>$userIdsBlockedMe]])
        ->with(['followingUserDetail'=> function ($query) {
            $query->select(['user.id','user.username','user.email','user.bio','user.description','user.image','user.is_verified','user.country_code','user.phone','user.country','user.city','user.sex','user.dob','user.is_chat_user_online','user.chat_last_time_online','user.location','user.latitude','user.longitude']);
            
        }]);
        

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 20,
            ]
        ]);

        
        $response['message']=Yii::$app->params['apiMessage']['common']['listFound'];
        $response['following']= $dataProvider;
        return $response;
       
    }


}


