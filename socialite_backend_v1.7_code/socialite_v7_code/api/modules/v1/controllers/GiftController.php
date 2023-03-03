<?php
namespace api\modules\v1\controllers;
use Yii;
use yii\rest\ActiveController;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;

use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBearerAuth;
use api\modules\v1\models\User;
use api\modules\v1\models\Gift;
use api\modules\v1\models\GiftSearch;
use api\modules\v1\models\GiftHistory;
use api\modules\v1\models\GiftHistorySearch;

use api\modules\v1\models\Payment;
use api\modules\v1\models\Notification;



/**
 * Controller API
 *
 
 */
class GiftController extends ActiveController
{
    public $modelClass = 'api\modules\v1\models\gift';   
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


    public function actionIndex(){


        $model = new GiftSearch();

        $result = $model->search(Yii::$app->request->queryParams);

        $response['message'] = Yii::$app->params['apiMessage']['common']['listFound'];
        
        $response['gift']=$result;
        return $response;

        
    }


    public function actionPopular(){

        $model = new GiftSearch();
        $result = $model->searchPopular(Yii::$app->request->queryParams);
        $response['message'] = Yii::$app->params['apiMessage']['common']['listFound'];
        $response['gift']=$result;
        return $response;

        
    }

      
    public function actionSendGift()
    {
        $userId                = Yii::$app->user->identity->id;
        $model                =   new GiftHistory();
        $modelGift               =   new Gift();
       
        $modelUser   =   new User();
        $resultUser = $modelUser->findOne($userId);
       
        $model->scenario ='sendGift';
        
        if (Yii::$app->request->isPost) {
            $model->load(Yii::$app->getRequest()->getBodyParams(), '');
            if(!$model->validate()) {
                $response['statusCode']=422;
                $response['errors']=$model->errors;
                return $response;
            }
            
           $giftId =  @(int) $model->gift_id;
           $resultGift     = $modelGift->findOne($giftId);
           
            if(!$resultGift){
                $response['statusCode']=422;
                $errors['message'][] = Yii::$app->params['apiMessage']['common']['noRecord'];
                $response['errors']=$errors;
                return $response;
            
            }
           

            if($resultGift->coin > $resultUser->available_coin){
                $response['statusCode']=422;
                $errors['message'][] = Yii::$app->params['apiMessage']['gift']['notEnoughBalance'];
                $response['errors']=$errors;
                return $response;
            
            }

           
            //resultUser
            $modelGiftHistory                =   new GiftHistory();
            $modelGiftHistory->sender_id        =   $userId;
            $modelGiftHistory->reciever_id      =   $model->reciever_id;
            $modelGiftHistory->gift_id          =   $giftId;
            $modelGiftHistory->coin             =   $resultGift->coin;
            $sendOnType                         = $model->send_on_type;
            $modelGiftHistory->send_on_type     =  $sendOnType;
            
            $modelGiftHistory->live_call_id =null;
            $modelGiftHistory->post_id =null;
            
            $onTypeString = '';
            
            if($sendOnType == $modelGiftHistory::SEND_TO_TYPE_LIVE){
                $modelGiftHistory->live_call_id     =   $model->live_call_id;
                $onTypeString = 'live call';

            }else if($sendOnType == $modelGiftHistory::SEND_TO_TYPE_PROFILE){
                //$modelGiftHistory->reciever_id     =   /same as its profile id
                $onTypeString = 'profile';

            }else if($sendOnType == $modelGiftHistory::SEND_TO_TYPE_POST){
                $modelGiftHistory->post_id     =   $model->post_id;
                $onTypeString = 'post';
            }

            if($modelGiftHistory->save()){

                $giftHistoryId = $modelGiftHistory->id;

                //for sender 

                $resultUser->available_coin  =  $resultUser->available_coin-$resultGift->coin;
                if($resultUser->save(false)){
                    $modelPayment          = new Payment();
                    $modelPayment->type                 =  Payment::TYPE_COIN;
                    $modelPayment->user_id               =  $userId;
                    $modelPayment->transaction_type     =  Payment::TRANSACTION_TYPE_DEBIT;
                    $modelPayment->payment_type         =  Payment::PAYMENT_TYPE_GIFT;
                    $modelPayment->payment_mode         =  Payment::PAYMENT_MODE_WALLET;
                    $modelPayment->coin                 =  $resultGift->coin;
                    $modelPayment->gift_history_id      =  $modelGiftHistory->id;
                    
                    $modelPayment->save(false);

                }


                //for reciever 
                $resultRecieverUser = $modelUser->findOne($model->reciever_id);

                $resultRecieverUser->available_coin  =  $resultRecieverUser->available_coin+$resultGift->coin;
                if($resultRecieverUser->save(false)){
                    $modelPayment          = new Payment();
                    $modelPayment->type                 =  Payment::TYPE_COIN;
                    $modelPayment->user_id              =  $model->reciever_id;
                    $modelPayment->transaction_type     =  Payment::TRANSACTION_TYPE_CREDIT;
                    $modelPayment->payment_type         =  Payment::PAYMENT_TYPE_GIFT;
                    $modelPayment->payment_mode         =  Payment::PAYMENT_MODE_WALLET;
                    $modelPayment->coin                 =  $resultGift->coin;
                    $modelPayment->gift_history_id      =  $modelGiftHistory->id;
                    $modelPayment->save(false);

                }

                 // send notification 
                $userIds=[];   
                $userIds[]=$model->reciever_id;

                 if($userIds){

                    
                   

                   
                    
                    $modelNotification = new Notification();
                    $notificationInput = [];
                    $notificationData =  Yii::$app->params['pushNotificationMessage']['giftRecieved'];
                    $replaceContent=[];   
                    $replaceContent['ON_TYPE'] = $onTypeString;
                    $notificationData['body'] = $modelNotification->replaceContent($notificationData['body'],$replaceContent);   
                
                    
                
                    $notificationInput['referenceId'] = $giftHistoryId;
                    $notificationInput['userIds'] = $userIds;
                    $notificationInput['notificationData'] = $notificationData;
                    $modelNotification->createNotification($notificationInput);
                    // end send notification 
                }




                $response['message']=Yii::$app->params['apiMessage']['gift']['sent'];
                return $response; 

            }else{
                $response['statusCode']=422;
                $errors['message'][] = Yii::$app->params['apiMessage']['common']['actionFailed'];
                $response['errors']=$errors;
                return $response;
            }

            
        }

       
        
    }


    public function actionRecievedGift()
    {
        
        
        $model = new GiftHistorySearch();
        $result = $model->search(Yii::$app->request->queryParams);
        $response['message']=Yii::$app->params['apiMessage']['common']['recordFound'];
        $response['gift']=$result;
        return $response; 

        

    }

   
    

}


