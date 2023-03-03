<?php
namespace api\modules\v1\controllers;
use Yii;
use yii\rest\ActiveController;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;
use api\modules\v1\models\Payment;
use api\modules\v1\models\WithdrawalPayment;
use api\modules\v1\models\WithdrawalPaymentSearch;
use api\modules\v1\models\PaymentSearch;
use api\modules\v1\models\Package;
use api\modules\v1\models\User;
use api\modules\v1\models\Setting;
use api\modules\v1\models\Notification;
use api\modules\v1\models\StripePayment;
use api\modules\v1\models\PaypalPayment;

use Braintree;






class PaymentController extends ActiveController
{
    public $modelClass = 'api\modules\v1\models\payment';   





    
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

    
    public function actionPackageSubscription()
    {
       
        $model          = new Payment();
        $modelPackage   = new Package();
      

        $userId = Yii::$app->user->identity->id;
        $model->scenario ='packageSubscription';
        $model->load(Yii::$app->getRequest()->getBodyParams(), '');
        if(!$model->validate()) {
            $response['statusCode']=422;
            $response['errors']=$model->errors;
        
            return $response;
        }
        $packageId =  @(int) $model->package_id;
        $packageResult =$modelPackage->findOne($packageId);


        $modelUser =  User::findOne($userId);
        $modelUser->available_coin =  $modelUser->available_coin + $packageResult->coin;

                
        if($modelUser->save(false)){



            $model->type                 =  Payment::TYPE_COIN;
            $model->transaction_type     =  Payment::TRANSACTION_TYPE_CREDIT;
            $model->payment_type         =  Payment::PAYMENT_TYPE_PACKAGE;
            $model->payment_mode         =  Payment::PAYMENT_MODE_IN_APP_PURCHASE;
            $model->coin                 =  $packageResult->coin;
            
            $amount = $model->amount;
            unset($model->amount);
            

            if($model->save(false)){

                $modelPaymentLog          = new Payment();

                $modelPaymentLog->type                 =  Payment::TYPE_PRICE;
                $modelPaymentLog->user_id               =  $model->user_id;
                $modelPaymentLog->package_id            =  $model->package_id;
                
                $modelPaymentLog->transaction_type     =  Payment::TRANSACTION_TYPE_CREDIT;
                
                $modelPaymentLog->payment_type         =  Payment::PAYMENT_TYPE_PACKAGE;
                $modelPaymentLog->payment_mode         =  Payment::PAYMENT_MODE_IN_APP_PURCHASE;
                $modelPaymentLog->transaction_id            =  $model->transaction_id;
                $modelPaymentLog->amount               =  $amount;
                $modelPaymentLog->save(false);

                $response['message']='Package subscribed successfully';
                return $response; 
            }
        }else{
            $response['statusCode']=422;
            $response['message']='Package not subscribed successfully';
            return $response; 

        }
    }


     /**payment my history */

     public function actionPaymentHistory()
     {
        
         $userId = Yii::$app->user->identity->id;
         $modelUser = new User();
         
         $model = new \yii\base\DynamicModel([
             'month', 'string'
              ]);
         $model->addRule(['month'], 'required');
         $model->load(Yii::$app->request->queryParams, '');
         $model->validate();
         if ($model->hasErrors()) {
                 $response['statusCode']=422;
                 $response['errors']=$model->errors;
                 return $response;
             
         }
         
         $modelSearch                           = new PaymentSearch();
         $result = $modelSearch->searchMyPayment(Yii::$app->request->queryParams);
 
         $resultUser = $modelUser->find()->select(['available_balance','available_coin'])->where(['id'=>$userId])->one();
 
         
         $response['message']=  Yii::$app->params['apiMessage']['common']['recordFound'];
         $response['available_balance']=  $resultUser->available_balance;
         $response['available_coin']=  $resultUser->available_coin;
         $response['payment']=  $result;
         
         return $response; 
     }



     /**payment withdrawal request hostory */

     public function actionWithdrawalHistory()
     {

//        $userId = Yii::$app->user->identity->id;
        $modelSearch                           = new WithdrawalPaymentSearch();
        $result = $modelSearch->searchMyWithdrawalPayment(Yii::$app->request->queryParams);
        $response['message']=  Yii::$app->params['apiMessage']['common']['recordFound'];
        
         $response['payment']=  $result;
        return $response; 
    }
 

       /**payment withdrawal request */

       public function actionWithdrawal()
       {
  
            $userId = Yii::$app->user->identity->id;
            $modelUser = new User();
            $resultUer = $modelUser->findOne($userId);
            $resultUer->available_balance; 
            if($resultUer->available_balance <=0){
                $response['statusCode']=422;
                $errors['message'][] = Yii::$app->params['apiMessage']['payment']['amountNotAvailable'];
                $response['errors']=$errors;
                return $response;
            }
            $withdrawalAmount = $resultUer->available_balance;
            $resultUer->available_balance =  0;//$resultUer->available_balance - $model->amount;
            if($resultUer->save(false)){
                $modelWithdrawPayment                       =  new WithdrawalPayment();
                $modelWithdrawPayment->user_id              =  $userId;
                $modelWithdrawPayment->amount               =  $withdrawalAmount;
                $modelWithdrawPayment->save(false);


                $modelPayment                   = new Payment();
                $modelPayment->user_id          =  $userId;
                $modelPayment->type             =  Payment::TYPE_PRICE;
                $modelPayment->amount           =  $withdrawalAmount;
                
                $modelPayment->transaction_type =  Payment::TRANSACTION_TYPE_DEBIT;
                $modelPayment->payment_type     =  Payment::PAYMENT_TYPE_WITHDRAWAL;
                $modelPayment->payment_mode     =  Payment::PAYMENT_MODE_WALLET;
                $modelPayment->save(false);
               


                $response['message']=  Yii::$app->params['apiMessage']['payment']['withdrawRequestSuccess'];
                
            }else{
                $response['statusCode']=422;
                $errors['message'][] = Yii::$app->params['apiMessage']['payment']['withdrawFailed'];
                $response['errors']=$errors;
            }
    
    
        return $response; 
    }


    
       /**redeem coin */

    public function actionRedeemCoin()
    {


        $userId = Yii::$app->user->identity->id;
        $modelUser = new User();
        $resultUer = $modelUser->findOne($userId);

        $modelSetting = new Setting();
        $modelNotification = new Notification();

        $settingResult = $modelSetting->find()->one();
        $minCoinRedeem = (int) $settingResult->min_coin_redeem;

        $redeemCoin = (int)Yii::$app->getRequest()->getBodyParams()['redeem_coin'];


        
        
        
        if($resultUer->available_coin < $redeemCoin){
            $response['statusCode']=422;
            $errors['message'][] = Yii::$app->params['apiMessage']['payment']['coinNotAvailable'];
            $response['errors']=$errors;
            return $response;
        }
        
        //$resultUer->available_coin; 
        if($redeemCoin < $minCoinRedeem){
            $response['statusCode']=422;


            $replaceContent['COIN'] = $minCoinRedeem;
            $message = $modelNotification->replaceContent(Yii::$app->params['apiMessage']['payment']['coinMinRequired'],$replaceContent);

            $errors['message'][] = $message;
            $response['errors']=$errors;
            return $response;
        }


        $totalPrice = $redeemCoin*$settingResult->per_coin_value;
        $resultUer->available_balance =  $resultUer->available_balance+$totalPrice;
        $resultUer->available_coin =  $resultUer->available_coin - $redeemCoin;
        if($resultUer->save(false)){

            
            // redeem coin from wallet

            $modelPayment                   = new Payment();
            $modelPayment->user_id          =  $userId;
            $modelPayment->type             =  Payment::TYPE_COIN;
            $modelPayment->coin             =  $redeemCoin;
            
            $modelPayment->transaction_type =  Payment::TRANSACTION_TYPE_DEBIT;
            $modelPayment->payment_type     =  Payment::PAYMENT_TYPE_REDEEM_COIN;
            $modelPayment->payment_mode     =  Payment::PAYMENT_MODE_WALLET;
            $modelPayment->save(false);
            
            // add price in wallet 

            $modelPayment                   = new Payment();
            $modelPayment->user_id          =  $userId;
            $modelPayment->type             =  Payment::TYPE_PRICE;
            $modelPayment->amount           =  $totalPrice;
            
            $modelPayment->transaction_type =  Payment::TRANSACTION_TYPE_CREDIT;
            $modelPayment->payment_type     =  Payment::PAYMENT_TYPE_REDEEM_COIN;
            $modelPayment->payment_mode     =  Payment::PAYMENT_MODE_WALLET;
            $modelPayment->save(false);



            

            $response['message']=  Yii::$app->params['apiMessage']['payment']['coinRedeemSuccess'];
            
        }else{
            $response['statusCode']=422;
            $errors['message'][] = Yii::$app->params['apiMessage']['payment']['coinRedeemFailed'];
            $response['errors']=$errors;
        }


        return $response; 
    }

     
    public function actionPaymentIntent()
    {
      //  $stripeCustomerId = Yii::$app->user->identity->stripe_customer_id;
        
        $model = new \yii\base\DynamicModel([
            'amount', 'currency',
             ]);
        $model->addRule(['currency','amount'], 'required');
        $model->load(Yii::$app->getRequest()->getBodyParams(), '');
        $model->validate();
        if ($model->hasErrors()) {
            
                $response['statusCode']=422;
                $response['errors']=$model->errors;
                return $response;
            
        }
        //Yii::$app->my->welcome();
        $inputData=[];
        $inputData['amount']                        =    $model->amount;
        $inputData['currency']                      =    $model->currency;
        /*$inputData['stripeCustomerId']              =    $stripeCustomerId;
        $inputData['description']                   =    'order payment';
        $inputData['payment_method_types']          =    'card';

        $inputData['shipping_name']                 =    $model->shipping_name;
        $inputData['shipping_address_line1']        =    $model->shipping_address_line1;
        $inputData['shipping_address_postal_code']  =    $model->shipping_address_postal_code;
        $inputData['shipping_address_city']         =    $model->shipping_address_city;
        $inputData['shipping_address_state']         =    $model->shipping_address_state;
        $inputData['shipping_address_country']      =    $model->shipping_address_country;*/
        
        
        $stripePayment = new StripePayment();  
        $clientSecret  =  $stripePayment->getPaymentIntend($inputData);
        $response['client_secret']=$clientSecret;
        $response['publishable_key']=$stripePayment->publishableKey;
        $response['message']=  'ok';
        return $response; 
    }


    public function actionPaypalClientToken()
    {
        
        $paypalModel = new PaypalPayment();
        
        $clientToken =  $paypalModel->getClientToken();
        $response['client_token']=$clientToken;
        $response['message']=  'ok';
        return $response; 
       // return $response; 
    }
     
    public function actionPaypalPayment()
    {
        $userId = Yii::$app->user->identity->id;
        $modelUser = new User();
        $resultUer = $modelUser->findOne($userId);
        $paypalModel = new PaypalPayment();


        $model = new \yii\base\DynamicModel([
            'amount', 'payment_method_nonce','device_data'
             ]);
        $model->addRule(['amount', 'payment_method_nonce','device_data'], 'required');
        $model->load(Yii::$app->getRequest()->getBodyParams(), '');
        $model->validate();
        if ($model->hasErrors()) {
            
                $response['statusCode']=422;
                $response['errors']=$model->errors;
                return $response;
            
        }

        $inputData=[];
        $inputData['amount']                    =   $model->amount;
        $inputData['paymentMethodNonce']        =   $model->payment_method_nonce;
        $inputData['deviceData']                =   $model->device_data;

        $result =  $paypalModel->getMakePayment($inputData);

        if($result['status']=='success'){
            $paymentId = $result['paymentId'];
            $response['payment_id']=$paymentId;
            $response['message']=  'ok';
            return $response; 

        }else{
            $response['statusCode']=422;
            $errors['message'][] = 'Payment not successfully done';
            $response['errors']=$errors;
            return $response;

        }
       

      
    }


    

    
    public function actionPaypalPayment_old()
    {
        $userId = Yii::$app->user->identity->id;
        $modelUser = new User();
        $resultUer = $modelUser->findOne($userId);
        $modelSetting = new Setting();


        $model = new \yii\base\DynamicModel([
            'amount', 'payment_method_nonce','device_data'
             ]);
        $model->addRule(['amount', 'payment_method_nonce','device_data'], 'required');
        $model->load(Yii::$app->getRequest()->getBodyParams(), '');
        $model->validate();
        if ($model->hasErrors()) {
            
                $response['statusCode']=422;
                $response['errors']=$model->errors;
                return $response;
            
        }

        $inputData=[];
        $inputData['amount']                    =   $model->amount;
        $inputData['paymentMethodNonce']        =   $model->payment_method_nonce;
        $inputData['deviceData']                =   $model->device_data;
        

        /*$gateway = new Braintree\Gateway([
            'environment' => 'sandbox',
            'merchantId' => 'v4n2gh2n648g77qt',
            'publicKey' => '4x2kpwm7srr9p2kk',
            'privateKey' => '04d208045cdf31c67e22b8fc1f6ca3bb'
        ]);*/


        $gateway = new Braintree\Gateway([
            'environment' => 'sandbox',
            'merchantId' => '7c9bdgy6qzqnnm4s',
            'publicKey' => 'ndyxfyd7drtpvm6t',
            'privateKey' => '3f0558e35e33a8861088c6bf0932a932'
        ]);

       // echo $model->payment_method_nonce;

       /*$result = $gateway->customer()->create([
        'firstName' => 'Mike',
        'lastName' => 'Jones',
        'company' => 'Jones Co.',
        'email' => 'mike.jones@example.com',
        'phone' => '281.330.8004',
        'fax' => '419.555.1235',
        'website' => 'http://example.com'
    ]);
    
    $result->success;
    # true
    
    echo $result->customer->id;*/


  //  echo $clientToken = $gateway->clientToken()->generate();


    # Generated customer id

    //die;


        $result = $gateway->transaction()->sale([
            'amount' => $model->amount,
            'paymentMethodNonce' => $model->payment_method_nonce,
            'deviceData' => $model->device_data,
            'options' => [
              'submitForSettlement' => True
            ]
          ]);
          
          if ($result->success) {
            echo 'success';
             //echo $transaction = $result->transaction()->find('the_transaction_id');
           // print_r($result->transaction);

            echo '<br>';
            print_r($result->transaction['paypal']['paymentId']);
            // See $result->transaction for details
          } else {
            echo 'failed';
            // Handle errors
          }

       // print_r($result);

        
        /*$gateway = new Braintree\Gateway([
            'environment' => 'sandbox',
            'merchantId' => 'v4n2gh2n648g77qt',
            'publicKey' => '4x2kpwm7srr9p2kk',
            'privateKey' => '04d208045cdf31c67e22b8fc1f6ca3bb'
        ]);*/




       // return $response; 
    }


        
   
   


}