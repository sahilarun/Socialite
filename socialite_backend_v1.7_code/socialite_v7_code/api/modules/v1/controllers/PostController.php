<?php
namespace api\modules\v1\controllers;

use api\modules\v1\models\Competition;
use api\modules\v1\models\CompetitionUser;
use api\modules\v1\models\HashTag;
use api\modules\v1\models\Notification;
use api\modules\v1\models\Post;
use api\modules\v1\models\PostComment;
use api\modules\v1\models\PostGallary;
use api\modules\v1\models\PostLike;
use api\modules\v1\models\PostSearch;
use api\modules\v1\models\PostView;
use api\modules\v1\models\ReportedPost;
use api\modules\v1\models\Setting;
use api\modules\v1\models\User;
use api\modules\v1\models\Follower;
use api\modules\v1\models\MentionUser;

use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\imagine\Image;
use yii\rest\ActiveController;
use yii\web\UploadedFile;
use api\modules\v1\models\FileUpload;

class PostController extends ActiveController
{
    public $modelClass = 'api\modules\v1\models\post';
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
            'except' => ['ad-search'],
            'authMethods' => [
                HttpBearerAuth::className(),
            ],
        ];
        return $behaviors;
    }
    public function actionCreate()
    {
        $userId = Yii::$app->user->identity->id;
        $model = new Post();

        $model->scenario = 'create';

        if (Yii::$app->request->isPost) {
            $model->load(Yii::$app->getRequest()->getBodyParams(), '');
            //$model->imageFile = UploadedFile::getInstanceByName('imageFile');
            // $model->videoFile = UploadedFile::getInstanceByName('videoFile');
            if (!$model->validate()) {
                $response['statusCode'] = 422;
                $response['errors'] = $model->errors;
                return $response;
            }


            if ($model->save()) {

                $postId = $model->id;

                if ($model->hashtag) {
                    $modelHashTag = new HashTag();
                    $modelHashTag->updateHashTag($model->id, $model->hashtag);
                }

                if ($model->mentionUser) {
                    $modelMentionUser = new MentionUser();
                    $userIds = $modelMentionUser->updateMentionUser($model->id, $model->mentionUser);
                  
                            
                    // send notification 
                    

                    if($userIds){

                    
                        $modelNotification = new Notification();
                        $notificationInput = [];
                        $notificationData =  Yii::$app->params['pushNotificationMessage']['mentionUserPost'];
                        $replaceContent=[];   
                        $replaceContent['TITLE'] = $model->title;
                        $notificationData['body'] = $modelNotification->replaceContent($notificationData['body'],$replaceContent);   
                    
                        
                    
                        $notificationInput['referenceId'] = $postId;
                        $notificationInput['userIds'] = $userIds;
                        $notificationInput['notificationData'] = $notificationData;
                        $modelNotification->createNotification($notificationInput);
                        // end send notification 
                    }



                }
                


                if ($model->gallary) {
                    $modelPostGallary = new PostGallary();
                    $modelPostGallary->updateGallary($model->id, $model->gallary);
                }

                $response['message'] = Yii::$app->params['apiMessage']['post']['postCreateSuccess'];
                $response['post_id'] = $model->id;
                //$response['image']=Yii::$app->params['pathUploadVideoThumb'] ."/".$model->image;
                //$response['video']=Yii::$app->params['pathUploadVideo'] ."/".$model->video;
                return $response;
            } else {

                $response['statusCode'] = 422;
                $errors['message'][] = Yii::$app->params['apiMessage']['post']['postCreateFailed'];
                $response['errors'] = $errors;
                return $response;

            }

        }

    }

    public function actionCreate_old()
    {
        $userId = Yii::$app->user->identity->id;
        $model = new Post();

        $model->scenario = 'create';

        if (Yii::$app->request->isPost) {
            $model->load(Yii::$app->getRequest()->getBodyParams(), '');
            //$model->imageFile = UploadedFile::getInstanceByName('imageFile');
            // $model->videoFile = UploadedFile::getInstanceByName('videoFile');
            if (!$model->validate()) {
                $response['statusCode'] = 422;
                $response['errors'] = $model->errors;
                return $response;
            }


            if ($model->save()) {

                $postId = $model->id;

                if ($model->hashtag) {
                    $modelHashTag = new HashTag();
                    $modelHashTag->updateHashTag($model->id, $model->hashtag);
                }

                if ($model->mentionUser) {
                    $modelMentionUser = new MentionUser();
                    $userIds = $modelMentionUser->updateMentionUser($model->id, $model->mentionUser);
                  
                            
                    // send notification 
                    

                    if($userIds){

                    
                        $modelNotification = new Notification();
                        $notificationInput = [];
                        $notificationData =  Yii::$app->params['pushNotificationMessage']['mentionUserPost'];
                        $replaceContent=[];   
                        $replaceContent['TITLE'] = $model->title;
                        $notificationData['body'] = $modelNotification->replaceContent($notificationData['body'],$replaceContent);   
                    
                        
                    
                        $notificationInput['referenceId'] = $postId;
                        $notificationInput['userIds'] = $userIds;
                        $notificationInput['notificationData'] = $notificationData;
                        $modelNotification->createNotification($notificationInput);
                        // end send notification 
                    }



                }
                


                if ($model->gallary) {
                    $modelPostGallary = new PostGallary();
                    $modelPostGallary->updateGallary($model->id, $model->gallary);
                }

                $response['message'] = Yii::$app->params['apiMessage']['post']['postCreateSuccess'];
                $response['post_id'] = $model->id;
                //$response['image']=Yii::$app->params['pathUploadVideoThumb'] ."/".$model->image;
                //$response['video']=Yii::$app->params['pathUploadVideo'] ."/".$model->video;
                return $response;
            } else {

                $response['statusCode'] = 422;
                $errors['message'][] = Yii::$app->params['apiMessage']['post']['postCreateFailed'];
                $response['errors'] = $errors;
                return $response;

            }

        }

    }

    public function actionView($id)
    {

        $model = new PostSearch();

        $result = $model->find()->where(['post.id'=>$id])
        ->joinWith(['user' => function($query){
            $query->select(['name','username','email','image','id','is_chat_user_online','chat_last_time_online','location','latitude','longitude']);
        }])
        ->joinWith(['clubDetail.createdByUser' => function($query){
            $query->select(['name','username','email','image','id','is_chat_user_online','chat_last_time_online','location','latitude','longitude']);
        }])
        
        ->one();
        $response['message'] = Yii::$app->params['apiMessage']['post']['listFound'];
        $response['post'] = $result;
        return $response;

    }
    public function actionDelete($id)
    {
        $userId = Yii::$app->user->identity->id;
        
        $model =   Post::find()->where(['id'=>$id,'user_id'=>$userId])->one();

      

        if( $model){
            $model->status = Post::STATUS_DELETED;
            if($model->save(false)){
                
                $response['message']=Yii::$app->params['apiMessage']['post']['deleted'];
             
                return $response; 
            }else{
                $response['statusCode']=422;
                $errors['message'][] = Yii::$app->params['apiMessage']['common']['actionFailed'];
                $response['errors']=$errors;
                return $response;
            }
        }
      
    }

    public function actionUploadGallary()
    {

        $model = new PostGallary();

        if (Yii::$app->request->isPost) {
            $model->load(Yii::$app->getRequest()->getBodyParams(), '');
            $model->filenameFile = UploadedFile::getInstanceByName('filenameFile');
            if (!$model->validate()) {
                $response['statusCode'] = 422;
                $response['errors'] = $model->errors;
                return $response;
            }
            $files =[];
            if ($model->filenameFile) {

                $modelFileUpload = new FileUpload();
                $type =     FileUpload::TYPE_POST;
                $files = $modelFileUpload->uploadFile($model->filenameFile,$type,false);
                
                $imageName 		= 	  $files[0]['file']; 
                $fileUrl 		= 	  $files[0]['fileUrl']; 


                /*$microtime = (microtime(true) * 10000);
                $uniqueimage = $microtime . '_' . date("Ymd_His") . '_' . substr(md5($microtime), 0, 10);
                $imageName = $uniqueimage . '.' . $model->filenameFile->extension;
                // $model->filename    =     $imageName;
                $s3 = Yii::$app->get('s3');
                $imagePath = $model->filenameFile->tempName;
                $result = $s3->upload('./' . Yii::$app->params['pathUploadImageFolder'] . '/' . $imageName, $imagePath);
                */

            }

            $response['message'] = 'Gallary updated successfully';
            $response['filename'] = $imageName;
            $response['fileUrl'] = $fileUrl;
            return $response;
        }
    }

    public function actionCompetitionImage()
    {
        $userId = Yii::$app->user->identity->id;
        $model = new Post();
        $modelCompetition = new Competition();
        $modelCompetitionUser = new CompetitionUser();

        $model->scenario = 'competitionImage';

        if (Yii::$app->request->isPost) {
            $model->load(Yii::$app->getRequest()->getBodyParams(), '');
            //  $model->imageFile = UploadedFile::getInstanceByName('imageFile');

            if (!$model->validate()) {
                $response['statusCode'] = 422;
                $response['errors'] = $model->errors;
                return $response;
            }
            $currentTime = time();
            $competitionId = @(int) $model->competition_id;
            $resultCompetition = $modelCompetition->find()->where(['id' => $competitionId, 'status' => Competition::STATUS_ACTIVE])->one();
            if (!$resultCompetition) {
                $response['statusCode'] = 422;
                $errors['message'][] = Yii::$app->params['apiMessage']['competition']['noRecord'];
                $response['errors'] = $errors;
                return $response;

            }

            if ($resultCompetition->start_date > $currentTime || $resultCompetition->end_date < $currentTime) {
                $response['statusCode'] = 422;
                $errors['message'][] = Yii::$app->params['apiMessage']['competition']['notAvailable'];
                $response['errors'] = $errors;
                return $response;

            }

            $resultCompetitionUser = $modelCompetitionUser->find()->where(['competition_id' => $competitionId, 'user_id' => $userId])->one();

            if (!$resultCompetitionUser) {
                $response['statusCode'] = 422;
                $errors['message'][] = Yii::$app->params['apiMessage']['competition']['joinCompetition'];
                $response['errors'] = $errors;
                return $response;

            }

            $countPost = $model->find()->where(['competition_id' => $competitionId, 'user_id' => $userId])->count();

            if ($countPost) {
                $response['statusCode'] = 422;
                $errors['message'][] = Yii::$app->params['apiMessage']['competition']['alreadyPosted'];
                $response['errors'] = $errors;
                return $response;

            }

            /*

            if($model->imageFile){
            //print_r($model->imageFile->tempName);
            //die;

            $microtime             =     (microtime(true)*10000);
            $uniqueimage        =    $microtime.'_'.date("Ymd_His").'_'.substr(md5($microtime),0,10);
            $imageName             =    $uniqueimage.'.'.$model->imageFile->extension;
            $model->image         =     $imageName;
            $s3 = Yii::$app->get('s3');
            $imagePath = $model->imageFile->tempName;
            $result = $s3->upload('./'.Yii::$app->params['pathUploadImageFolder'].'/'.$imageName, $imagePath);
            //echo '<pre>';
            //print_r($result);
            //die;
            //$promise = $s3->commands()->upload('./video-thumb/'.$imageName, $imagePath)->async()->execute();
            }
             */

            $model->type = Post::TYPE_COMPETITION;
            if ($model->save()) {

                if ($model->hashtag) {
                    $modelHashTag = new HashTag();
                    $modelHashTag->updateHashTag($model->id, $model->hashtag);
                }

                if ($model->gallary) {
                    $modelPostGallary = new PostGallary();
                    $modelPostGallary->updateGallary($model->id, $model->gallary);
                }

                $response['message'] = Yii::$app->params['apiMessage']['post']['postCreateSuccess'];
                $response['post_id'] = $model->id;
                //$response['image']=Yii::$app->params['pathUploadVideoThumb'] ."/".$model->image;
                //$response['video']=Yii::$app->params['pathUploadVideo'] ."/".$model->video;
                return $response;
            } else {

                $response['statusCode'] = 422;
                $errors['message'][] = Yii::$app->params['apiMessage']['post']['postCreateFailed'];
                $response['errors'] = $errors;
                return $response;

            }

        }

    }

    public function actionMyPost()
    {

        $model = new PostSearch();

        $result = $model->searchMyPost(Yii::$app->request->queryParams);
        
        

        $response['message'] = Yii::$app->params['apiMessage']['post']['listFound'];
        $response['post'] = $result;

        return $response;

    }


    public function actionMyPostMentionUser()
    {

        $model = new PostSearch();

        $result = $model->searchMyPostMentionUser(Yii::$app->request->queryParams);

        $response['message'] = Yii::$app->params['apiMessage']['post']['listFound'];
        $response['post'] = $result;

        return $response;

    }

    /**
     * search post
     */

    public function actionSearchPost()
    {

        $model = new PostSearch();
        $result = $model->search(Yii::$app->request->queryParams);
        $response['message'] = Yii::$app->params['apiMessage']['post']['listFound'];
        $response['post'] = $result;
        return $response;

    }


    /**
     * hash Counter list
     */

    public function actionHashCounterList()
    {

       
        $model = new HashTag();


       $hashtag = Yii::$app->request->queryParams['hashtag'];
        
        $query = $model->find()
        ->select(['hashtag','count(hashtag) as counter']);

    
        $query->where(
            ['like', 'hashtag', $hashtag.'%',false]
        );
        $query->groupBy('hashtag');



        $results = $query->all();
        //$results = $query->asArray()->all();

     /*   ->where(['hashtag' => function($query) use ($isFilter){
            $query->select(['name','username','email','image','id']);
        }])*/
        $result=[];

        $response['message'] = Yii::$app->params['apiMessage']['post']['listFound'];
        $response['results'] = $results;
        //$response['post'] = $results;
        return $response;

    }


    /**
     * Report Post
     */
    public function actionReportPost()
    {

        $model = new ReportedPost();
        $userId = Yii::$app->user->identity->id;

        $model->scenario = 'create';
        $model->load(Yii::$app->getRequest()->getBodyParams(), '');
        if (!$model->validate()) {
            $response['statusCode'] = 422;
            $response['errors'] = $model->errors;

            return $response;
        }

        $postId = @(int) $model->post_id;

        $totalCount = $model->find()->where(['post_id' => $postId, 'user_id' => $userId, 'status' => ReportedPost::STATUS_PENDING])->count();
        if ($totalCount > 0) {
            $response['statusCode'] = 422;
            $errors['message'][] = Yii::$app->params['apiMessage']['post']['alreadyReported'];
            $response['errors'] = $errors;
            return $response;

        }

        $model->status = ReportedPost::STATUS_PENDING;
        if ($model->save(false)) {
            $response['message'] = Yii::$app->params['apiMessage']['post']['reportedSuccess'];
            return $response;
        } else {

            $response['statusCode'] = 422;
            $errors['message'][] = Yii::$app->params['apiMessage']['common']['actionFailed'];
            $response['errors'] = $errors;
        }
    }

    /**
     * like post
     */

    public function actionLike()
    {
        $model = new PostLike();
        $modelFollower = new Follower();
        $userId = Yii::$app->user->identity->id;
        $model->scenario = 'create';
        $model->load(Yii::$app->getRequest()->getBodyParams(), '');
        if (!$model->validate()) {
            $response['statusCode'] = 422;
            $response['errors'] = $model->errors;
            return $response;
        }
        $postId = @(int) $model->post_id;
        $totalCount = $model->find()->where(['post_id' => $postId, 'user_id' => $userId])->count();
        
        if ($totalCount > 0) {
            $response['statusCode'] = 422;
            $errors['message'][] = Yii::$app->params['apiMessage']['post']['postLikeAlready'];
            $response['errors'] = $errors;
            return $response;

        }

        if ($model->save(false)) {
            $modelPost = new Post();
            $totalLike = $modelPost->updateLikeCounter($postId);


            $resultPost = $modelPost->findOne($postId);
           
            // send notification 

            $toUserId=$resultPost->user_id;
            $isFollowing = $modelFollower->find()->where(['user_id'=>$userId,'follower_id'=>$toUserId])->count();

           
            $modelNotification = new Notification();
            $notificationInput = [];
            $notificationData =  Yii::$app->params['pushNotificationMessage']['likePost'];
            $replaceContent=[];   
            $replaceContent['USER'] = Yii::$app->user->identity->username;
            $notificationData['body'] = $modelNotification->replaceContent($notificationData['body'],$replaceContent);   
           
            $userIds=[];
            $userIds[]   =   $resultPost->user_id; 
           
            $notificationInput['referenceId'] = $postId;
            $notificationInput['userIds'] = $userIds;
            $notificationInput['notificationData'] = $notificationData;
            $notificationInput['isFollowing'] = $isFollowing;
            $modelNotification->createNotification($notificationInput);
            // end send notification 



            $response['message'] = Yii::$app->params['apiMessage']['post']['postLikeSuccess'];
            $response['total_like'] = $totalLike;
            return $response;
        } else {
            $response['statusCode'] = 422;
            $errors['message'][] = Yii::$app->params['apiMessage']['post']['postLikeFailed'];
            $response['errors'] = $errors;
            return $response;
        }
    }

    /**
     * unlike post
     */

    public function actionUnlike()
    {

        $model = new PostLike();
        $userId = Yii::$app->user->identity->id;
        $model->scenario = 'create';
        $model->load(Yii::$app->getRequest()->getBodyParams(), '');
        if (!$model->validate()) {
            $response['statusCode'] = 422;
            $response['errors'] = $model->errors;

            return $response;
        }

        $postId = @(int) $model->post_id;

        $result = $model->find()->where(['post_id' => $postId, 'user_id' => $userId])->one();
        if (isset($result->id)) {
            if ($result->delete()) {

                $modelPost = new Post();
                $totalLike = $modelPost->updateLikeCounter($postId, 'unlike');

                $response['message'] = Yii::$app->params['apiMessage']['post']['postUnlikeSuccess'];
                $response['total_like'] = $totalLike;
                return $response;
            } else {

                $response['statusCode'] = 422;
                $errors['message'][] = Yii::$app->params['apiMessage']['common']['actionFailed'];
                $response['errors'] = $errors;
                return $response;

            }

        } else {
            $response['statusCode'] = 422;
            $errors['message'][] = Yii::$app->params['apiMessage']['post']['postUnlikeFailed'];
            $response['errors'] = $errors;
            return $response;

        }

    }

    /**
     * like post
     */

    public function actionViewCounter()
    {
        $model = new PostView();
       
        $userId = Yii::$app->user->identity->id;
        $model->scenario = 'create';
        $model->load(Yii::$app->getRequest()->getBodyParams(), '');
        if (!$model->validate()) {
            $response['statusCode'] = 422;
            $response['errors'] = $model->errors;
            return $response;
        }
        $postId = @(int) $model->post_id;

        $totalCount = $model->find()->where(['post_id' => $postId, 'user_id' => $userId])->count();
        if ($totalCount == 0) {
            $model->save(false);
            $modelPost = new Post();
            $modelPost->updateViewCounter($postId);

           

        }

        $response['message'] = 'ok';
        return $response;

    }


    /**
     * like post
     */

    public function actionPromotionAdView()
    {
        
        $modelSetting = new Setting();

        $settingResult = $modelSetting->find()->one();
        $eachViewCoin = (int) $settingResult->each_view_coin;

       $userId = Yii::$app->user->identity->id;
       if ($userId > 0 && $eachViewCoin > 0) { /// each view get coin
            $modelUser = new User();
            $userResult = $modelUser->findOne($userId);
            $userResult->available_coin = $userResult->available_coin + $eachViewCoin;
            $userResult->save(false);
       }

        $response['message'] = 'ok';
        return $response;

    }


    /**
     * add comment
     */

    public function actionAddComment()
    {
        $model = new PostComment();
        $modelFollower = new Follower();
        $userId = Yii::$app->user->identity->id;
        $model->scenario = 'create';
        $model->load(Yii::$app->getRequest()->getBodyParams(), '');
        if (!$model->validate()) {
            $response['statusCode'] = 422;
            $response['errors'] = $model->errors;
            return $response;
        }
        $postId = @(int) $model->post_id;


        
        
        

        if ($model->save(false)) {
            $modelPost = new Post();
            $totalLike = $modelPost->updateCommentCounter($postId);

            //// push notification
            /*
            $resultPost = Post::findOne($postId);

            $modelUser = new User();
            $userResult = $modelUser->findOne($resultPost->user_id);

            if ($userResult->device_token) {
                $message = $model->comment;
                $title = Yii::$app->user->identity->name . ' write new comment on your post';
                $dataPush['title'] = $title;
                $dataPush['body'] = $message;
                $dataPush['data']['notification_type'] = 'newComment';
                $dataPush['data']['post_id'] = $postId;

                $deviceTokens[] = $userResult->device_token;

                Yii::$app->pushNotification->sendPushNotification($deviceTokens, $dataPush);

            }
            //// end push notification
            /// add notification to list

            $modelNotification = new Notification();
            $modelNotification->user_id = $resultPost->user_id;
            $modelNotification->type = Notification::TYPE_NEW_COMMENT;
            $modelNotification->reference_id = $postId;
            $modelNotification->title = $title;
            $modelNotification->message = $message;
            $modelNotification->save(false);
            /// end add notification to list

            */

            

             // send notification 

             $resultPost = Post::findOne($postId);
             $toUserId=$resultPost->user_id;
            $isFollowing = $modelFollower->find()->where(['user_id'=>$userId,'follower_id'=>$toUserId])->count();
           
             $modelNotification = new Notification();
             $notificationInput = [];
             $notificationData =  Yii::$app->params['pushNotificationMessage']['newComment'];
             $replaceContent=[];   
             $replaceContent['USER'] = Yii::$app->user->identity->username;
             $notificationData['title'] = $modelNotification->replaceContent($notificationData['title'],$replaceContent);   
             // $notificationData['body'] = $modelNotification->replaceContent($notificationData['title'],$replaceContent);   
             $notificationData['body'] = $model->comment;
            
             $userIds=[];
             $userIds[]   =   $resultPost->user_id;
            
             $notificationInput['referenceId'] = $postId;
             $notificationInput['userIds'] = $userIds;
             $notificationInput['notificationData'] = $notificationData;
             $notificationInput['isFollowing'] = $isFollowing;
            
             
             $modelNotification->createNotification($notificationInput);
             // end send notification 

            $response['message'] = Yii::$app->params['apiMessage']['post']['commentSuccess'];

            return $response;
        } else {
            $response['statusCode'] = 422;
            $errors['message'][] = Yii::$app->params['apiMessage']['coomon']['actionFailed'];
            $response['errors'] = $errors;
            return $response;
        }
    }

    /**
     * list comment
     */

    public function actionCommentList()
    {
        $model = new PostComment();
        $userId = Yii::$app->user->identity->id;
        $model->scenario = 'list';

        $model->load(Yii::$app->request->queryParams, '');
        if (!$model->validate()) {
            $response['statusCode'] = 422;
            $response['errors'] = $model->errors;
            return $response;
        }
        $postId = @(int) $model->post_id;

        $query = $model->find()
            ->joinWith(['user' => function ($query) {
                $query->select(['id', 'name','username', 'image','is_chat_user_online','chat_last_time_online','location','latitude','longitude']);
            }])

            ->where(['post_comment.post_id' => $postId])
            ->andWhere(['<>', 'post_comment.status', PostComment::STATUS_DELETED])
            ->select(['post_comment.id', 'post_comment.comment', 'post_comment.user_id', 'post_comment.created_at'])
            ->orderBy(['post_comment.id' => SORT_ASC]);
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

    /**
     * share post
     */

    public function actionShare()
    {
        $model = new Post;
        $userId = Yii::$app->user->identity->id;
        $model->scenario = 'share';
        $model->load(Yii::$app->getRequest()->getBodyParams(), '');
        if (!$model->validate()) {
            $response['statusCode'] = 422;
            $response['errors'] = $model->errors;
            return $response;
        }
        $postId = @(int) $model->id;
        $result = $model->findOne($postId);
        if (!$result) {
            $response['statusCode'] = 422;
            $errors['message'][] = Yii::$app->params['apiMessage']['common']['noRecord'];
            $response['errors'] = $errors;
            return $response;

        }

        $modelPost = new Post;
        $modelPost->user_id = $userId;
        $modelPost->title = $result->title;
        $modelPost->video = $result->video;
        $modelPost->image = $result->image;
        $modelPost->audio_id = $result->audio_id;
        $modelPost->is_share_post = Post::IS_SHARE_POST_YES;
        $modelPost->share_level = $result->share_level + 1;

        $origin_post_id = $result->id;
        if ($result->is_share_post) {
            $origin_post_id = $result->origin_post_id;
        }

        $modelPost->origin_post_id = $origin_post_id;

        if ($modelPost->save(false)) {
            $tags = [];
            foreach ($result->hashtags as $tag) {
                $tags[] = $tag['hashtag'];
            }
            $hashtags = implode(',', $tags);
            $modelHashTag = new HashTag();
            $modelHashTag->updateHashTag($modelPost->id, $hashtags);

            $modelPost->updateShareCounter($postId);
            if ($result->is_share_post) {
                $modelPost->updateShareCounter($result->origin_post_id);
            }

            $response['message'] = Yii::$app->params['apiMessage']['post']['postShareSuccess'];
            $response['post_id'] = $modelPost->id;
            return $response;

        } else {
            $response['statusCode'] = 422;
            $errors['message'][] = Yii::$app->params['apiMessage']['common']['actionFailed'];
            $response['errors'] = $errors;
            return $response;
        }
    }

    protected function findModel($id)
    {
        if (($model = Post::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

}
