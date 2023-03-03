<?php
namespace api\modules\v1\controllers;
use Yii;
use yii\rest\ActiveController;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBearerAuth;
use api\modules\v1\models\Notification;
use yii\data\ActiveDataProvider;

class NotificationController extends ActiveController
{
    public $modelClass = 'api\modules\v1\models\notification';   
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
            'except'=>['ad-search'],
            'authMethods' => [
                HttpBearerAuth::className()
            ],
        ];
        return $behaviors;
    }
 


    public function actionIndex(){
        
        $userId  = Yii::$app->user->identity->id;
        $model =  new Notification();
        $query = $model->find()->where(['user_id'=>$userId])
            ->orderBy(['id'=>SORT_DESC]);
            

        $dataProvider = new ActiveDataProvider([
                'query' => $query,
                'pagination' => [
                    'pageSize' => 20,
                ]
        ]);
    

        
        $response['message']='Ok';
        $response['notification']=$dataProvider;
        
        return $response;
    }


}


