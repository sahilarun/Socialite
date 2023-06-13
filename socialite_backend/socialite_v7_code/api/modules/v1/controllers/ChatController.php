<?php
namespace api\modules\v1\controllers;
use Yii;
use yii\rest\ActiveController;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBearerAuth;
use api\modules\v1\models\ChatRoom;
use api\modules\v1\models\ChatMessage;
use api\modules\v1\models\ChatMessageUser;
use api\modules\v1\models\ChatRoomUser;
use api\modules\v1\models\CallHistory;
use api\modules\v1\models\User;
use yii\web\UploadedFile;
use yii\data\ActiveDataProvider;
use yii\db\Expression;
class ChatController extends ActiveController
{
    public $modelClass = 'api\modules\v1\models\chatRoom';   

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



    public function actionCreateRoom()
    {
        
        $userId    =     Yii::$app->user->identity->id;
        
        $model  =   new ChatRoom();
        $modelChatRoomUser  =   new ChatRoomUser();

        
        $model->scenario = 'createRoom';

        $model->load(Yii::$app->getRequest()->getBodyParams(), '');

        if(!$model->validate()) {
            $response['statusCode']=422;
            $response['errors']=$model->errors;
            return $response;
        }
        $roomId= 0;
        if($model->type == ChatRoom::TYPE_PERSONAL){
            $receiverId =$model->receiver_id;
            $query =$model->find()
            ->joinWith(['chatRoomUser'])
            ->where(['chat_room.type'=>ChatRoom::TYPE_PERSONAL])
            ->andWhere(['chat_room_user.user_id'=>$userId]);
            $results = $query->all();
          //  print_r($results);
            
            foreach($results as $result ) {
               // print_r($result->chatRoomUser);

                $roomId= 0;


               //#  //end check my room delete/or not  and action if deleted

                //start check  room delete/or not by reciever  and action if deleted 

                $isUserExist=$modelChatRoomUser->getIsUserInRoom($result->chatRoomUser,$receiverId);
               
                if($isUserExist){
                    
                    if($isUserExist->status!=ChatRoomUser::STATUS_ACTIVE){
                        $isUserExist->status=ChatRoomUser::STATUS_ACTIVE;
                        $isUserExist->save();
                    }


                    $roomId= $result->id;
                     
                    //start check my room delete/or not  and action if deleted
                    $myUserInRoom=$modelChatRoomUser->getMyUserInRoom($result->chatRoomUser,$roomId,$userId);
                    if($myUserInRoom){
                        if($myUserInRoom->status!=ChatRoomUser::STATUS_ACTIVE){
                            $myUserInRoom->status=ChatRoomUser::STATUS_ACTIVE;
                            $myUserInRoom->save();
                        }
    
                        
                    }
                    //END check my room delete/or not  and action if deleted






                    break;
               }
            }
           if($roomId==0){
                if($model->save(false)){
                    $roomId = $model->id;
                    
                   /* $modelChatRoomUser->room_id = $roomId;
                    $modelChatRoomUser->user_id =  $userId;
                    $modelChatRoomUser->save();
                    */
                    
                    
                }
    
            }
            
           // echo  'ROOM: '.$roomId;




          //  print_r($result);



        }else if($model->type == ChatRoom::TYPE_GROUP){
            if($model->save(false)){
                $roomId = $model->id;
                /*$modelChatRoomUser->room_id = $roomId;
                $modelChatRoomUser->user_id =  $userId;
                $modelChatRoomUser->save();*/
                
                
            }

        }

        
        //if($model->save(false)){
         
            

        
        if($roomId){
            $roomResult =$model->findOne($roomId);

            $response['message']=Yii::$app->params['apiMessage']['chat']['roomCreated'];
            $response['room_id'] = $roomId;
            $response['room']=$roomResult;
            
        }else{
            $response['statusCode']=422;
            $errors['message'][] = Yii::$app->params['apiMessage']['common']['actionFailed'];
            $response['errors']=$errors;
        }
        
        return $response;
      
        

    }

    public function actionChatMessage()
    {
        $userId    =     Yii::$app->user->identity->id;
        
        $model = new \yii\base\DynamicModel([
            'room_id', 'last_message_id',
             ]);
        $model->addRule(['room_id','last_message_id'], 'required');
        
        $model->load(Yii::$app->request->queryParams, '');
        $model->validate();
        if ($model->hasErrors()) {
            
                $response['statusCode']=422;
                $response['errors']=$model->errors;
                return $response;
            
        }
        

        $roomId                 =    (int)$model->room_id;
        $lastMessageId         =    (int)$model->last_message_id;

       
        $modelChatMessage  =   new ChatMessage();
        
  
        $query =$modelChatMessage->find()
        ->where(['chat_message.room_id'=>$roomId]);
        if($lastMessageId>0){
            $query->andWhere(['<','chat_message.id',$lastMessageId]);
        }
            
        $query->joinWith(['chatMessageUser' => function($query){
           // $query->select(['id','username','email','image','is_chat_user_online','chat_last_time_online']);
             $query->where(['<>','chat_message_user.status',ChatRoomUser::STATUS_DELETED]);

        }])
        ->joinWith(['user' => function($query){
             $query->select(['id','username','email','image','is_chat_user_online','chat_last_time_online']);
              
 
         }])
        ->andWhere(['chat_message_user.user_id'=>$userId])
        ->orderBy(['chat_message.id'=>SORT_DESC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 100,
            ]
        ]);

        
        $response['message']=Yii::$app->params['apiMessage']['common']['listFound'];
        $response['chatMessage']= $dataProvider;
        return $response;
        
        
       
      

    }


    

    public function actionUpdateRoom($id)
    {
        
        $userId    =     Yii::$app->user->identity->id;
        
        $model  =   new ChatRoom();
        $modelChatRoomUser  =   new ChatRoomUser();

        
      

        $model =   ChatRoom::find()->where(['id'=>$id])->one();

        //print_r($model);

        
        $canUpdate=false;

        if($model->created_by==$userId){
            $canUpdate=true;
        }else{

            $isAdmin = $modelChatRoomUser->find()->where(['user_id'=>$userId,'access_group'=>ChatRoom::ACCESS_GROUP_ADMIN,'status'=>ChatRoomUser::STATUS_ACTIVE])->count();
            
            if($isAdmin){
                $canUpdate=true;
            }
            
        }
        
        if(!$canUpdate){
            $response['statusCode'] = 422;
            $errors['message'][] = Yii::$app->params['apiMessage']['common']['notAllowed'];
            $response['errors'] = $errors;
            return $response;

        }

       
        $model->load(Yii::$app->getRequest()->getBodyParams(), '');


        if(!$model->validate()) {
            $response['statusCode']=422;
            $response['errors']=$model->errors;
            return $response;
        }

        if($model->save(false)){
             $roomResult =$model->findOne($id);
            $response['message']=Yii::$app->params['apiMessage']['chat']['roomUpdated'];
            $response['room']=$roomResult;
            
        }else{
            $response['statusCode']=422;
            $errors['message'][] = Yii::$app->params['apiMessage']['common']['actionFailed'];
            $response['errors']=$errors;
        }

      
        
        return $response;
      
        

    }

    public function actionRoom()
    {
        $userId    =     Yii::$app->user->identity->id;
        $model  =   new ChatRoom();
        $modelChatRoomUser  =   new ChatRoomUser();

        
          
        $query =$model->find()->where(['status'=>ChatRoom::STATUS_ACTIVE])
        ->joinWith(['chatRoomUser'])
        ->joinWith(['lastMessage'])
        ->joinWith(['chatRoomUser.user' => function($query){
            $query->select(['id','username','email','image','is_chat_user_online','chat_last_time_online']);

        }])
        ->joinWith(['createdByUser' => function($query){
            $query->select(['id','username','email','image','is_chat_user_online','chat_last_time_online']);
        }])
        ->where(['chat_room_user.status'=>ChatRoomUser::STATUS_ACTIVE,'chat_room_user.user_id'=>$userId])
        ->orderBy(['chat_message.created_at'=>SORT_DESC]);
        $results = $query->all();
        
        
       
        $response['message']=Yii::$app->params['apiMessage']['common']['listFound'];
        $response['room'] = $results;
        
        return $response;
      

    }

    public function actionRoomDetail($room_id)
    {
        $id=(int)$room_id;

        
        $userId    =     Yii::$app->user->identity->id;
        $model  =   new ChatRoom();
        $modelChatRoomUser  =   new ChatRoomUser();

        
          
        $results =$model->find()->where(['chat_room.status'=>ChatRoom::STATUS_ACTIVE])
        ->joinWith(['chatRoomUser'])
        ->joinWith(['lastMessage'])
        ->joinWith(['chatRoomUser.user' => function($query){
            $query->select(['id','username','email','image','is_chat_user_online','chat_last_time_online']);
            

        }])
        ->joinWith(['createdByUser' => function($query){
            $query->select(['id','username','email','image','is_chat_user_online','chat_last_time_online']);
             

        }])
       
        ->where(['chat_room.id'=>$id])->one();
        
         
        
        
       
        $response['message']=Yii::$app->params['apiMessage']['common']['listFound'];
        $response['room'] = $results;
        
        return $response;
      

    }


    public function actionDeleteRoom()
    {
        $userId    =     Yii::$app->user->identity->id;
        $model  =   new ChatRoom();
        $modelChatRoomUser  =   new ChatRoomUser();

        //print_r($id);

         $roomId=@(int)Yii::$app->getRequest()->queryParams['room_id'];

        if($roomId){

            $roomUser = $modelChatRoomUser->find()->where(['room_id'=>$roomId,'user_id'=>$userId])->one();
            
            $roomUser->status=ChatRoomUser::STATUS_DELETED;
            $roomUser->save();

        }
        
        //request->queryParams;
       
        $response['message']=Yii::$app->params['apiMessage']['chat']['roomDeleted'];
       // $response['room'] = $results;
        
        return $response;
      

    }



    // call history

    public function actionCallHistory()
    {
        $userId    =     Yii::$app->user->identity->id;
        $model  =   new CallHistory();
        
  
        $query =$model->find()
        ->where(
            ['or',
                
                ['call_detail.caller_id'=>$userId],
                ['call_detail.receiver_id'=>$userId]
                
            ])
        ->joinWith(['callerDetail' => function($query){
            $query->select(['id','username','email','image','is_chat_user_online','chat_last_time_online']);

        }])
        ->joinWith(['receiverDetail' => function($query){
            $query->select(['id','username','email','image','is_chat_user_online','chat_last_time_online']);

        }])

        ->orderBy(['call_detail.id'=>SORT_DESC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 20,
            ]
        ]);

        
        $response['message']=Yii::$app->params['apiMessage']['common']['listFound'];
        $response['callHistory']= $dataProvider;
        return $response;
        
        
       
      

    }


    
    public function actionUploadMediaFile()
    {
        
       

        $model = new \yii\base\DynamicModel([
            'mediaFile'
        ]);
        $model->addRule(['mediaFile'], 'required')
            ->addRule(['mediaFile'], 'file');

        
        if (Yii::$app->request->isPost) {
            $model->load(Yii::$app->getRequest()->getBodyParams(), '');
            $model->mediaFile = UploadedFile::getInstanceByName('mediaFile'); 
            if(!$model->validate()) {
                $response['statusCode']=422;
                $response['errors']=$model->errors;
                return $response;
            }

            if($model->mediaFile){
                    
                $microtime 			= 	(microtime(true)*10000);
                $uniqueimage		=	$microtime.'_'.date("Ymd_His").'_'.substr(md5($microtime),0,10); 
                $imageName 			=	$uniqueimage;
                $mediaFileName 		= 	$imageName.'.'.$model->mediaFile->extension; 
                $imagePath 			=	Yii::$app->params['pathUploadChatMedia'] ."/".$mediaFileName;
                $model->mediaFile->saveAs($imagePath,false);
                
            }
            $response['message']=Yii::$app->params['apiMessage']['chat']['fileUploaded'];
            $response['image']=Yii::getAlias('@siteUrl').Yii::$app->urlManagerFrontend->baseUrl.'/uploads/chat/'.$mediaFileName;
            return $response; 
        }
    }   

    

    // live  user 

    public function actionLiveUser()
    {
        $userId    =     Yii::$app->user->identity->id;
        $modelUser  =   new User();

        $query = $modelUser->find()
        
        ->select(['user.id','user.name','user.username','user.email','user.bio','user.description','user.image','user.is_verified','user.country_code','user.phone','user.country','user.city','user.sex','user.dob','user.is_chat_user_online','user.chat_last_time_online'])
         ->where(['user.role'=>User::ROLE_CUSTOMER])
         ->andwhere(['user.status'=>User::STATUS_ACTIVE])
         ->andWhere(['<>','user.id',$userId])
         ->andwhere(['user.is_chat_user_online'=>User::COMMON_YES])
         ->joinWith(['userLiveDetail']);
         


        //$user = $query->all();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 20,
            ]
        ]);

    
        
        $response['message']=Yii::$app->params['apiMessage']['common']['listFound'];
        $response['user']=$dataProvider;
        return $response; 
      

    }


    // online  user 

    public function actionOnlineUser()
    {
        $userId    =     Yii::$app->user->identity->id;
        $modelUser  =   new User();

        $query = $modelUser->find()
        //->select(['user.id','user.name','user.username','user.email','user.description','user.phone','user.image'])
        ->select(['user.id','user.name','user.username','user.email','user.bio','user.description','user.image','user.is_verified','user.country_code','user.phone','user.country','user.city','user.sex','user.dob','user.is_chat_user_online','user.chat_last_time_online'])
        ->where(['user.role'=>User::ROLE_CUSTOMER])
        ->andwhere(['user.status'=>User::STATUS_ACTIVE])
        ->andwhere(['user.is_chat_user_online'=>User::COMMON_YES])
        ->andWhere(['<>','user.id',$userId])
        ->limit(50)
        ->orderBy(new Expression('rand()'));


        $user = $query->all();


        
        $response['message']=Yii::$app->params['apiMessage']['common']['listFound'];
        $response['user']=$user;
        return $response; 
    

    }


    
    public function actionDeleteRoomChat()
    {
        
        $userId    =     Yii::$app->user->identity->id;
        
       // $model  =   new ChatRoom();
        $modelChatMessageUser  =   new ChatMessageUser();

        $model = new \yii\base\DynamicModel([
            'room_id',
             ]);
        $model->addRule(['room_id'], 'required');
        
        
        $model->load(Yii::$app->getRequest()->getBodyParams(), '');
        $model->validate();
        if ($model->hasErrors()) {
            
                $response['statusCode']=422;
                $response['errors']=$model->errors;
                return $response;
            
        }

        $roomId = $model->room_id;

        $modelChatMessage  =   new ChatMessage();
        
  
        $query =$modelChatMessage->find()
        ->select(['chat_message.id'])
        ->where(['chat_message.room_id'=>$roomId]);
        
        $result  =$query->asArray()->all();

        $messageIds=[];
        foreach($result as $result){
            $messageIds[]=$result['id'];
        }



       $modelChatMessageUser->updateAll(['status'=>ChatMessageUser::STATUS_DELETED],['chat_message_id'=>$messageIds,'user_id'=>$userId]);
       
       $response['message']=Yii::$app->params['apiMessage']['chat']['roomChatDeleted'];
       return $response;
      
        

    }


   

    /*


    public function actionMessageGroup()
    {
        $userId    =     Yii::$app->user->identity->id;
        $modelMessageGroup  =   new MessageGroup();
        $groupResult =  $modelMessageGroup->getActiveGroup($userId);

        $response['message']='Message active session found successfully';
        $response['group']=$groupResult;
        return $response; 


    }
   

    public function actionMessageHistory()
    {
        $userId    =     Yii::$app->user->identity->id;
        $model  =   new Message();
        
        $last_time = Yii::$app->getRequest()->get('last_time', 0);
        $group_id = Yii::$app->getRequest()->get('group_id', 0);
        $model->scenario = 'messageHistory';

       
        $model->load(Yii::$app->getRequest()->get(), '');
        
        if(!$model->validate()) {
            $response['statusCode']=422;
            $response['errors']=$model->errors;
        
            return $response;
        }


        $result = $model->find()
        ->where(['group_id'=>$group_id])
        ->andWhere(['>=','created_at',$last_time])->all();



        $response['message']='Message list found successfully';
        $response['messages']=$result;
        $response['last_time']=time();

        
        return $response; 


    }*/
   



}


