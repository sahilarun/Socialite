<?php
namespace api\modules\v1\controllers;
use Yii;
use yii\rest\ActiveController;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\web\UploadedFile;
use yii\imagine\Image;
use yii\data\ActiveDataProvider;
use api\modules\v1\models\User;
use api\modules\v1\models\Story;
use api\modules\v1\models\StorySearch;
//use api\modules\v1\models\CollectionUser;

class StoryController extends ActiveController
{
    public $modelClass = 'api\modules\v1\models\story';   
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
            //'except'=>['ad-search'],
            'authMethods' => [
                HttpBearerAuth::className()
            ],
        ];
        return $behaviors;
    }


    public function actionIndex()
    {

        $model = new StorySearch();
        $results = $model->searchStory(Yii::$app->request->queryParams);

        $userArr=[];
        foreach($results as $result){
         

          $key = array_search($result->user->id, array_column($userArr, 'id'));
          
          if(is_int($key)){
            $prUserPost =  $userArr[$key]['userStory'];
            $prUserPost[]=$result;
            $userArr[$key]['userStory']=$prUserPost;
          }else{
            
            $user= $result->user;
            $resultArray=[];
            $resultArray[] = $result;
            $user['userStory'] = $resultArray;
            $userArr[]=$user;

          }
        }

        $response['message'] = Yii::$app->params['apiMessage']['story']['listFound'];
        $response['story'] = $userArr;
       // $response['post'] = $results;
        return $response;

    }

    public function actionCreate()
    {
        $userId = Yii::$app->user->identity->id;
        $model =   new Story();
        $model->scenario ='createMain';
        
        if (Yii::$app->request->isPost) {
            
            $model->load(Yii::$app->getRequest()->getBodyParams(), '');
         
            $stroyIds=[];
            $isProcess=false;

            foreach($model->stories as $story){
                $modelStory =   new Story();
                $modelStory->type = @$story['type'];
                $modelStory->image = @$story['image'];
                $modelStory->video = @$story['video'];
                $modelStory->description = @$story['description'];
                $modelStory->background_color = @$story['background_color'];
                if($modelStory->save(false)){
                    $stroyIds[]=$modelStory->id;
                    $isProcess=true;
                }
               
            }


            if($isProcess){

                $response['message']=Yii::$app->params['apiMessage']['story']['created'];
                $response['story_ids']=$stroyIds;
                return $response; 
            }else{
                $response['statusCode']=422;
                $errors['message'][] = Yii::$app->params['apiMessage']['common']['actionFailed'];
                $response['errors']=$errors;
                return $response;
            }
            
        }
    }


    
   
   

    public function actionMyStory()
    {

        
        $model = new StorySearch();
        
        
        $result = $model->searchMyStory(Yii::$app->request->queryParams);
        

        
        $response['message'] = Yii::$app->params['apiMessage']['story']['listFound'];
        $response['story'] = $result;
        return $response;

    }

    public function actionMyActiveStory()
    {

        
        $model = new StorySearch();
        
        
        $result = $model->searchMyActiveStory(Yii::$app->request->queryParams);
        

        
        $response['message'] = Yii::$app->params['apiMessage']['story']['listFound'];
        $response['story'] = $result;
        return $response;

    }




    public function actionDelete($id)
    {
        $userId = Yii::$app->user->identity->id;
        
        $model =   Story::find()->where(['id'=>$id,'user_id'=>$userId])->one();

        if( $model){
            $model->status = Story::STATUS_DELETED;
            if($model->save(false)){
                
                $response['message']=Yii::$app->params['apiMessage']['story']['deleted'];
             
                return $response; 
            }else{
                $response['statusCode']=422;
                $errors['message'][] = Yii::$app->params['apiMessage']['common']['actionFailed'];
                $response['errors']=$errors;
                return $response;
            }
        }
      
    }


    protected function findModel($id)
    {
        if (($model = Story::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }


}


