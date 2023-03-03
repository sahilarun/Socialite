<?php
namespace api\modules\v1\controllers;
use Yii;
use yii\rest\ActiveController;
use api\modules\v1\models\User;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\web\UploadedFile;
use yii\imagine\Image;
use api\modules\v1\models\Payment;
use api\modules\v1\models\Package;
use api\modules\v1\models\Subscription;
use api\modules\v1\models\ReportedUser;
use api\modules\v1\models\Follower;
use api\modules\v1\models\UserSearch;
use api\modules\v1\models\BlockedUser;
use api\modules\v1\models\FileUpload;



use Twilio\Rest\Client;

/**
 * User Controller API
 *

 */
class UserController extends ActiveController
{
    public $modelClass = 'api\modules\v1\models\user';   
    
    public function actions()
	{
		
        //$headers = apache_request_headers();
        /*$headers =Yii::$app->request->headers;
        print_r($headers);
        die;*/

        
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
            'only'=>['profile','view','profile-update','update-token','update-location','logout','update-password','update-payment-detail','update-profile-image','nearest-user','update-mobile','verify-otp','search-user','find-friend','report-user','sugested-user','push-notification-status','delete-account'],
            'authMethods' => [
                HttpBearerAuth::className()
            ],
        ];
        return $behaviors;
    }
    /**
     * Login user
     */
    public function actionLogin(){
        
        $model =  new User();
        $model->scenario ='login';
        
        $request = Yii::$app->request;
        
        $params = $request->bodyParams;
        $model->attributes=$params;
        if(!$model->validate()) {
            $response['statusCode']=422;
            $response['errors']=$model->errors;
            return $response;
        }
     
       $user =  $model->checkLogin();

     
      
       if($user){
            

            if ($user->is_email_verified == User::IS_EMAIL_VERIFIED_YES) {    

               /*$response['statusCode']=401;
                $errors['message'][] = "You have not verfified your email, Please verified your and setup you new password from forgot password";
                $response['errors']=$errors;
                //$response['message']='Email/password incorrect';
                return $response;
                }*/
                $authKey = Yii::$app->security->generateRandomString();
                $user->auth_key = $authKey;
                $user->last_active = time();
                $user->device_token = $params['device_token'];
                $user->device_type = $params['device_type'];
                $user->device_token_voip_ios = $params['device_token_voip_ios'];
                
            
                if($user->save(false)){
                    
                    $userProfile = $model->getProfile($user->id);
                    $response['message']='Looged in successfully';
                    $response['user']= $userProfile;
                    $response['auth_key']= $userProfile->auth_key;
                    return $response; 

                }

            }else{

                
                $otp = mt_rand(1000, 9999);
                $token = md5(time() . rand(10, 100));
                $expirytTime = time() + 900;
                $token = $token . '_' . $expirytTime;
                $user->password_reset_token = $token;
                $user->verification_with = User::VERIFICATION_WITH_EMAIL;
                /*if(Yii::$app->params['siteMode']==2){
                $otp=Yii::$app->params['testOtp'];
                }*/
                $user->verification_token = $otp;

                if ($user->save(false)) {

                    //$from = Yii::$app->params['senderEmail'];
                    $fromMail = Yii::$app->params['senderEmail'];
                    $fromName = Yii::$app->params['senderName'];
                    $from = array($fromMail =>$fromName);

                    Yii::$app->mailer->compose()
                        ->setSubject('Registration confirmation')
                        ->setFrom($from)
                        ->setTo($user->email)
                        ->setHtmlBody('Hi ' . $user->username . '<br>Please use following OTP Code confirm you registration.<br> OTP Code is : ' . $otp)
                        ->send();

                    $response['statusCode'] = 401;
                    $errors['message'][] = Yii::$app->params['apiMessage']['user']['emailNotVerified'];
                    $response['token'] = $token;
                    $response['errors'] = $errors;
                    return $response;
                }
                
            }


         } else {
            $response['statusCode']=401;
            $errors['message'][] = "Email/password incorrect";
            $response['errors']=$errors;
            //$response['message']='Email/password incorrect';
            return $response;

       }

        
    }
    /**
     * logout
     */

    public function actionLogout(){
      
        $id = Yii::$app->user->identity->id;
        $model = $this->findModel($id);
        
        

        $model->auth_key=NULL;
        $model->device_token=NULL;
        $model->device_token_voip_ios=NULL;
        $model->is_chat_user_online=0;
        
        
        if($model->save(false)){
            
        
            $response['message']='User logout successfully';
            return $response; 

        }
        
    }

/**
     * Login user
     */
    public function actionLoginSocial(){
        
        $model =  new User();
        $model->scenario ='loginSocial';
        
        $request = Yii::$app->request;
        
        $params = $request->bodyParams;
        $model->attributes=$params;
        if(!$model->validate()) {
            $response['statusCode']=422;
            $response['errors']=$model->errors;
            return $response;
        }
       $users =  $model->checkLoginSocail($params);
       $user = $users['users_details'];
       if(isset($users['login_first_time'])){
            $loginTime = 1;
       }else{
            $loginTime = 0;
       }
       if($user){

            $authKey = Yii::$app->security->generateRandomString();
            $user->auth_key = $authKey;
            $user->last_active = time();
            $user->device_token = $params['device_token'];
            $user->device_type = $params['device_type'];
            $user->device_token_voip_ios = $params['device_token_voip_ios'];
            $user->is_login_first_time = $loginTime; // if user login more than one time 

          
            if($user->save(false)){
                
                $userProfile = $model->getProfile($user->id);
                
                $response['message']='Looged in successfully';
                $response['user']= $userProfile;
                $response['auth_key']= $userProfile->auth_key;
                $response['is_login_first_time'] = $userProfile->is_login_first_time;
                return $response; 

            }



       } else {
            $response['statusCode']=401;
            $errors['message'][] = "Something is wrong to login";
            $response['errors']=$errors;
            return $response;

       }

        
    }


    /**
     * Forgot password
     */
    public function actionForgotPassword_OLD_DIRECT_EMAIL(){
        
     

       /* $sid    = "ACce0b623128f6307fd027b60e9e0e8ddb";
        $token  = "62d690bc5318bb305a50e659a82ab5a5";
        $twilio = new Client($sid, $token);

        $message = $twilio->messages
                        ->create("+919417649265", // to
                        ["from" => "+12058947840", "body" => "body"]
                        );

                        print_r($message->sid);

*/

  
        $model =  new User();
        $model->scenario ='forgotPassword';
        
        $request = Yii::$app->request;
        
        $params = $request->bodyParams;
        
        $model->attributes=$params;
        if(!$model->validate()) {
            $response['statusCode']=422;
            $response['errors']=$model->errors;
            return $response;
        }
        $user = $model->find()->where(['email' => $params['email'], 'status' => User::STATUS_ACTIVE])->one();
        if($user){

            
           $password = Yii::$app->security->generateRandomString(8);
          
           $user->password_hash = Yii::$app->security->generatePasswordHash($password);


           $fromMail = Yii::$app->params['senderEmail'];
           $fromName = Yii::$app->params['senderName'];
           $from = array($fromMail =>$fromName);

           if($user->save(false)){
                     Yii::$app->mailer->compose()
                     ->setSubject('Passowrd Reset')
                     ->setFrom($from)
                    ->setTo($model->email)
                    ->setHtmlBody('Hi '.$user->name.'<br>Your password has been successfully upated.<br> New password is : <b>'.$password.'</b>')
                    ->send();              
                $response['message']='New Password has sent on your email';
               // $response['pass']=$password;
                return $response; 
        

           }else{
                $response['statusCode']=422;
                $errors['message'][] = "Action failed, Please try again";
                $response['errors']=$errors;
                return $response; 
           }
        }else{
            $response['statusCode']=422;
            $errors['message'][] = "Email not registered with us";
            $response['errors']=$errors;

            return $response; 
        }
    }
    /**
     * Register user
     */
    public function actionRegister(){
        $model =  new User();
        $modelPackage =  new Package();

        
        $model->scenario ='register';
        $request = Yii::$app->request;
        $params = $request->bodyParams;
        $model->attributes=$params;
        if(!$model->validate()) {
            $response['statusCode']=422;
            $response['errors']=$model->errors;
            return $response;
        }
        $model->role =  $model::ROLE_CUSTOMER;
        $model->status = $model::STATUS_ACTIVE;
     //   $model->name =  'Guest';
        $defaultPackage = $modelPackage->getDefaultPackage();
        if($defaultPackage){
            $model->available_coin =  $defaultPackage->coin;
        }

        $otp = mt_rand(1000, 9999);
        $token = md5(time() . rand(10, 100));
        $expirytTime = time() + 900;
        $token = $token . '_' . $expirytTime;
        $model->password_reset_token = $token;
        
        /*if(Yii::$app->params['siteMode']==2){
          $otp=Yii::$app->params['testOtp'];
        }*/

        $model->verification_token = $otp;
        
    
       
        if($model->save()){

             //$from = Yii::$app->params['senderEmail'];
             $fromMail = Yii::$app->params['senderEmail'];
             $fromName = Yii::$app->params['senderName'];
             $from = array($fromMail =>$fromName);
 
             Yii::$app->mailer->compose()
                 ->setSubject('Registration confirmation')
                 ->setFrom($from)
                 ->setTo($model->email)
                 ->setHtmlBody('Hi ' . $model->username . '<br>Thank you for the registration.<br> Please use following OTP Code confirm you registration.<br> OTP Code is : ' . $otp)
                 ->send();

            $response['message'] = 'Your have registeted successfully, Please verified you email to complete the registration.';
            $response['token'] = $token;
            //$response['user']= $userProfile;
            //$response['auth_key']= $userProfile->auth_key;
            return $response;    
           
             // $userProfile = $model->getProfile($model->id);
 
            
            /*$userProfile = $model->getProfile($model->id);
            
            $response['message']='User Register successfully';
            $response['user']   = $userProfile;
            $response['auth_key']= $userProfile->auth_key;
            return $response; */

        }
    }

      /**
     * delete account
     */
    
    public function actionDeleteAccount(){
      
        $id = Yii::$app->user->identity->id;
        $model = $this->findModel($id);
        
        

        $model->status=$model::STATUS_DELETED;
        $model->auth_key=NULL;
        
        if($model->save(false)){
            
        
            $response['message']='Account deleted successfully';
            return $response; 

        }
        
    }
    

    public function actionCheckUsername()
    {
        $model = new User();

       
        $model->scenario = 'checkUsername';
        $request = Yii::$app->request;
        $params = $request->bodyParams;
        $model->attributes = $params;

        if (!$model->validate()) {
            $response['statusCode'] = 422;
            $response['errors'] = $model->errors;
            return $response;
        }else{
            $response['message'] = 'Username available';
            return $response;

        }
      
         


        
    }

      /**
     * verify registration OTP
     */
    public function actionVerifyRegistrationOtp()
    {
        //$id = Yii::$app->user->identity->id;
        $model = new User();
        $model->scenario = 'verifyRegistrationOtp';
        $request = Yii::$app->request;
        $params = $request->bodyParams;
        $model->attributes = $params;
        if (!$model->validate()) {
            $response['statusCode'] = 422;
            $response['errors'] = $model->errors;
            return $response;
        }

        $tokenExprity = @explode('_', $model->token)[1];
        if (time() > $tokenExprity) {
            $response['statusCode'] = 422;
            $errors['message'][] = 'Your token has been expired';
            $response['errors'] = $errors;
            return $response;
        }

        $user = $model->find()->where(['password_reset_token' => $model->token, 'verification_token' => $model->otp, 'status' => User::STATUS_ACTIVE])->one();
        if ($user) {

            $authKey = Yii::$app->security->generateRandomString();
            $user->auth_key = $authKey;
           
            $user->password_reset_token = null;
            $user->verification_token = null;
            $user->is_email_verified = $model::IS_EMAIL_VERIFIED_YES;


            if ($user->save(false)) {



                $userProfile = $model->getProfile($user->id);
                //echo $userProfile->auth_key;
                $userProfile->auth_key = $userProfile->auth_key;
                $response['message'] = 'Looged in successfully';
                $response['user'] = $userProfile;
                $response['auth_key'] = $userProfile->auth_key;
                return $response;

            } else {
                $response['statusCode'] = 422;
                $errors['message'][] = Yii::$app->params['apiMessage']['common']['actionFailed'];
                $response['errors'] = $errors;
                return $response;

            }
        } else {

            $response['statusCode'] = 422;
            $errors['message'][] = 'Verification process failed';
            $response['errors'] = $errors;
            return $response;
        }
    }


    

    /**
     * Forgot password request
     */
    public function actionForgotPasswordRequest()
    {

        $model = new User();
        $model->scenario = 'forgotPassword';

        $request = Yii::$app->request;

        $params = $request->bodyParams;

        $model->attributes = $params;
        if (!$model->validate()) {
            $response['statusCode'] = 422;
            $response['errors'] = $model->errors;
            return $response;
        }
        $verification_with = $params['verification_with'];
        $user = [];
        if ($verification_with == User::VERIFICATION_WITH_EMAIL) { // email
            $user = $model->find()->where(['email' => $params['email'], 'status' => User::STATUS_ACTIVE])->one();
        } else if ($verification_with == User::VERIFICATION_WITH_PHONE) { // phone number
            $user = $model->find()->where(['country_code' => $params['country_code'], 'phone' => $params['phone'], 'status' => User::STATUS_ACTIVE])->one();
        }

        if ($user) {

            $otp = mt_rand(1000, 9999);
            ///$user->password_hash = Yii::$app->security->generatePasswordHash($);
            $token = md5(time() . rand(10, 100));

            $expirytTime = time() + 900;
            $token = $token . '_' . $expirytTime;
            $user->password_reset_token = $token;

            if ((Yii::$app->params['siteMode'] == 2 ) && $verification_with == User::VERIFICATION_WITH_PHONE) { // test mode and with mobile number verification
                $otp = Yii::$app->params['testOtp'];
            }

            $user->verification_token = $otp;
            $user->verification_with = $verification_with;

            if ($user->save(false)) {

                if ($verification_with == User::VERIFICATION_WITH_EMAIL) { // email
                    $fromMail = Yii::$app->params['senderEmail'];
                    $fromName = Yii::$app->params['senderName'];
                    $from = array($fromMail =>$fromName);
                    Yii::$app->mailer->compose()
                        ->setSubject('Passowrd Reset')
                        ->setFrom($from)
                        ->setTo($user->email)
                        ->setHtmlBody('Hi ' . $user->username . '<br>We have received request for reset password. If you requested then use following OTP Code confirm you request.<br> OTP Code is : ' . $otp)
                        ->send();
                    $response['message'] = Yii::$app->params['apiMessage']['user']['sentEmailForgotPassword'];
                    $response['token'] = $token;
                    return $response;

                } else if ($verification_with == User::VERIFICATION_WITH_PHONE) { // phone number

                    if (Yii::$app->params['siteMode'] == 1 || Yii::$app->params['siteMode'] == 3) { // sent msg on live mode

                        $sid = Yii::$app->params['twilioSid'];
                        $tokenTwilio = Yii::$app->params['twilioToken'];
                        $smsFromTwilio = Yii::$app->params['smsFromTwilio'];
                        $twilio = new Client($sid, $tokenTwilio);

                        $toNumber = '+' . $params['country_code'] . $params['phone'];

                        $otpString = "OTP:" . $otp;
                        $message = $twilio->messages
                            ->create($toNumber, // to
                                ["from" => $smsFromTwilio, "body" => $otpString]
                            );
                        if ($message->sid) {

                            $response['message'] = "OTP has been sent on your mobile for confirmation.";
                            $response['token'] = $token;
                            return $response;

                        } else {

                            $response['statusCode'] = 422;
                            $errors['message'][] = "Sending otp is failed, Please try again";
                            $response['errors'] = $errors;
                            return $response;

                        }

                    } else {
                        $response['message'] = "OTP has been sent on your mobile for confirmation.";
                        $response['token'] = $token;
                        return $response;
                    }
                }

            } else {
                $response['statusCode'] = 422;
                $errors['message'][] = Yii::$app->params['apiMessage']['common']['actionFailed'];
                $response['errors'] = $errors;
                return $response;

            }
        } else {
            $response['statusCode'] = 422;
            $errors['message'][] = Yii::$app->params['apiMessage']['user']['notRegisterWithUs'];
            $response['errors'] = $errors;
            return $response;

        }
    }

    /**
     * Resend OTP
     */
    public function actionResendOtp()
    {
        $model = new User();
        $model->scenario = 'resendOtp';
        $model->load(Yii::$app->getRequest()->getBodyParams(), '');
        if (!$model->validate()) {
            $response['statusCode'] = 422;
            $response['errors'] = $model->errors;
            return $response;
        }

        $tokenExprity = @explode('_', $model->token)[1];
        if (time() > $tokenExprity) {
            $response['statusCode'] = 422;
            $errors['message'][] = Yii::$app->params['apiMessage']['user']['tokenExpired'];
            $response['errors'] = $errors;
            return $response;
        }

        $user = $model->find()->where(['password_reset_token' => $model->token, 'status' => User::STATUS_ACTIVE])->one();
        if ($user) {
            $otp = $user->verification_token;
            if ($user->verification_with == User::VERIFICATION_WITH_EMAIL) { // email
                $fromMail = Yii::$app->params['senderEmail'];
                $fromName = Yii::$app->params['senderName'];
                $from = array($fromMail =>$fromName);
                Yii::$app->mailer->compose()
                    ->setSubject('One Time OTP')
                    ->setFrom($from)
                    ->setTo($user->email)
                    ->setHtmlBody('Hi ' . $user->username . '<br>We have received request for OTP. If you requested then use following OTP Code confirm you request.<br> OTP Code is : ' . $otp)
                    ->send();
                $response['message'] = Yii::$app->params['apiMessage']['user']['otpSentEamilSuccess'];
                return $response;

            } else if ($user->verification_with == User::VERIFICATION_WITH_PHONE) { // phone number

                if (Yii::$app->params['siteMode'] == 1 || Yii::$app->params['siteMode'] == 3) { // sent msg on live mode

                    $sid = Yii::$app->params['twilioSid'];
                    $token = Yii::$app->params['twilioToken'];
                    $smsFromTwilio = Yii::$app->params['smsFromTwilio'];
                    $twilio = new Client($sid, $token);

                    $toNumber = '+' . $user->country_code . $user->phone;

                    $otpString = "OTP:" . $otp;
                    $message = $twilio->messages
                        ->create($toNumber, // to
                            ["from" => $smsFromTwilio, "body" => $otpString]
                        );
                    if ($message->sid) {
                        

                        $response['message'] = Yii::$app->params['apiMessage']['user']['otpSentMobileSuccess'];
                        return $response;

                    } else {

                        $response['statusCode'] = 422;
                        $errors['message'][] = "Sending otp is failed, Please try again";
                        $response['errors'] = $errors;
                        return $response;

                    }

                } else {
                    $response['message'] = Yii::$app->params['apiMessage']['user']['otpSentMobileSuccess'];
                    return $response;
                }
            }

            
        } else {
            $response['statusCode'] = 422;
            $errors['message'][] = Yii::$app->params['apiMessage']['common']['actionFailed'];
            $response['errors'] = $errors;
            return $response;

        }
    }


    /**
     * Forgot password request verify OTP
     */
    public function actionForgotPasswordVerifyOtp()
    {
        $model = new User();
        $model->scenario = 'forgotPasswordVerifyOtp';
        $model->load(Yii::$app->getRequest()->getBodyParams(), '');
        if (!$model->validate()) {
            $response['statusCode'] = 422;
            $response['errors'] = $model->errors;
            return $response;
        }

        $tokenExprity = @explode('_', $model->token)[1];
        if (time() > $tokenExprity) {
            $response['statusCode'] = 422;
            $errors['message'][] = Yii::$app->params['apiMessage']['user']['tokenExpired'];
            $response['errors'] = $errors;
            return $response;
        }

        $user = $model->find()->where(['password_reset_token' => $model->token, 'verification_token' => $model->otp, 'status' => User::STATUS_ACTIVE])->one();
        if ($user) {

            
            $otp = mt_rand(1000, 9999);
            $token = md5(time() . rand(10, 100));
            $expirytTime = time() + 900;
            $token = $token . '_' . $expirytTime;
            $user->password_reset_token = $token;
            $user->verification_token = null;

            if ($user->save(false)) {
                $response['message'] = Yii::$app->params['apiMessage']['user']['optVerifyToChangePassword'];
                $response['token'] = $token;
                return $response;
            } else {
                $response['statusCode'] = 422;
                $errors['message'][] = Yii::$app->params['apiMessage']['common']['actionFailed'];
                $response['errors'] = $errors;
                return $response;
            }
        } else {
            $response['statusCode'] = 422;
            $errors['message'][] = Yii::$app->params['apiMessage']['user']['optVerifyFailed'];
            $response['errors'] = $errors;
            return $response;

        }
    }

    /**
     * Forgot password set NEw password
     */
    public function actionSetNewPassword()
    {
        $model = new User();
        $model->scenario = 'forgotPasswordNewPassword';
        $model->load(Yii::$app->getRequest()->getBodyParams(), '');
        if (!$model->validate()) {
            $response['statusCode'] = 422;
            $response['errors'] = $model->errors;
            return $response;
        }

        $tokenExprity = @explode('_', $model->token)[1];
        if (time() > $tokenExprity) {
            $response['statusCode'] = 422;
            $errors['message'][] = Yii::$app->params['apiMessage']['user']['tokenExpired'];
            $response['errors'] = $errors;
            return $response;
        }

        $user = $model->find()->where(['password_reset_token' => $model->token, 'status' => User::STATUS_ACTIVE])->one();
        if ($user) {

            $user->password_hash = Yii::$app->security->generatePasswordHash($model->password);
            $user->auth_key = null;
            $user->password_reset_token = null;
            if ($user->verification_with == User::VERIFICATION_WITH_EMAIL) { // email
                $user->is_email_verified = User::IS_EMAIL_VERIFIED_YES;
            }
            $user->verification_with = null;
          //  $user->last_password_updated_at = time();

            if ($user->save(false)) {
                $response['message'] = Yii::$app->params['apiMessage']['user']['passwordChanged'];
                return $response;
            } else {
                $response['statusCode'] = 422;
                $errors['message'][] = Yii::$app->params['apiMessage']['common']['actionFailed'];
                $response['errors'] = $errors;
                return $response;
            }
        } else {
            $response['statusCode'] = 422;
            $errors['message'][] = Yii::$app->params['apiMessage']['common']['notAllowed'];
            $response['errors'] = $errors;
            return $response;

        }
    }


    /**
     * my Profile
     *      */
    public function actionProfile(){
       
        $model =  new User();
        $modelPackage =  new Package();
        $id = Yii::$app->user->identity->id;
        $userProfile = $model->getFullProfileMy($id);
        //$response['user']= $userProfile;
        $response['user']=   $userProfile; 
        return $response; 

        
    }


    /**
     * user Profile 
     */
    public function actionView($id){
        $model =  new User();
        $modelPackage =  new Package();
        $userProfile = $model->getFullProfile($id);
        
        $response['user']=   $userProfile; 
        return $response; 

        
    }


    /**
     * Profile user
     */
    public function actionProfileUpdate(){
        $id = Yii::$app->user->identity->id;
        $model = $this->findModel($id);
        $model->scenario ='profileUpdate';
        /*$request = Yii::$app->request;
        $params = $request->bodyParams;
        $model->attributes=$params;
        */
        
        $model->load(Yii::$app->getRequest()->getBodyParams(), '');
        if(!$model->validate()) {
            $response['statusCode']=422;
            $response['errors']=$model->errors;
            return $response;
        }
        if($model->save(false)){
          //  $modelUserLocation =  new UserLocation();
            //$modelUserLocation->updateUserLocation($id,$params['locations']);
            
            $response['message']='Profile Updated successfully';
            return $response; 

        }
        
    }


    /**
     * udate user device tokens
     */
    public function actionUpdateToken(){
        $id = Yii::$app->user->identity->id;
        $model = $this->findModel($id);
        
            
        $model->load(Yii::$app->getRequest()->getBodyParams(), '');
        
        
        if(!$model->validate()) {
            $response['statusCode']=422;
            $response['errors']=$model->errors;
            return $response;
        }
        
        if($model->save(false)){
            
            
            $response['message']='Device token Updated successfully';
            return $response; 

        }
        
    }

     /**
     * udate user location
     */
    public function actionUpdateLocation(){
        $id = Yii::$app->user->identity->id;
        $model = $this->findModel($id);
        
            
        $model->load(Yii::$app->getRequest()->getBodyParams(), '');
        
        
        if(!$model->validate()) {
            $response['statusCode']=422;
            $response['errors']=$model->errors;
            return $response;
        }
        if(!$model->latitude){
            $model->location=null;
            $model->latitude=null;
            $model->longitude=null;
        }
        if($model->save(false)){
            
            
            $response['message']='User location updated successfully';
            return $response; 

        }
        
    }



   

    
    

    /**
     * Profile user
     */
    public function actionPushNotificationStatus(){
        $id = Yii::$app->user->identity->id;
        $model = $this->findModel($id);
        
        $model->scenario ='pushNotificationStatusUpdate';
            
        $model->load(Yii::$app->getRequest()->getBodyParams(), '');
        
        
        if(!$model->validate()) {
            $response['statusCode']=422;
            $response['errors']=$model->errors;
            return $response;
        }
        
        if($model->save(false)){
            
            $response['message']='Notification status Updated successfully';
            return $response; 

        }
        
    }

    

    /**
     * Profile user
     */
    public function actionUpdatePaymentDetail(){
        $id = Yii::$app->user->identity->id;
        $model = $this->findModel($id);
        
        $model->scenario ='profilePaymentDetail';
            
        $model->load(Yii::$app->getRequest()->getBodyParams(), '');
        
        if(!$model->validate()) {
            $response['statusCode']=422;
            $response['errors']=$model->errors;
            return $response;
        }
        
        if($model->save(false)){
            
            $response['message']='Payment detail Updated successfully';
            return $response; 

        }
        
    }


    

    /**
     * update mobile
     */
    public function actionUpdateMobile(){
        $id = Yii::$app->user->identity->id;
       $model= new User();
        $model->scenario ='updateMobile';
       $request = Yii::$app->request;
       $params = $request->bodyParams;
       $model->attributes=$params;
        if(!$model->validate()) {
            $response['statusCode']=422;
            $response['errors']=$model->errors;
            return $response;
        }
        $string=$params['phone'].'#'.$params['country_code'];

       $token   =  base64_encode($string);
       $otp = mt_rand(1000, 9999);
       if(Yii::$app->params['siteMode'] == 2 ) { // test mode and with mobile number verification
         $otp = Yii::$app->params['testOtp'];
       }
       $modelRes = $this->findModel($id);
       $modelRes->verification_token  = $otp;

       if($modelRes->save(false)){
         //$token1=  base64_decode($token);


             //=============


            if (Yii::$app->params['siteMode'] == 1 || Yii::$app->params['siteMode'] == 3) { // sent msg on live mode

                $sid = Yii::$app->params['twilioSid'];
                $tokenTwilio = Yii::$app->params['twilioToken'];
                $smsFromTwilio = Yii::$app->params['smsFromTwilio'];
                $twilio = new Client($sid, $tokenTwilio);

                $toNumber = '+' . $params['country_code'] . $params['phone'];

                $otpString = "OTP:" . $otp;
                $message = $twilio->messages
                    ->create($toNumber, // to
                        ["from" => $smsFromTwilio, "body" => $otpString]
                    );
                if ($message->sid) {

                    $response['message'] = "OTP has been sent on your mobile for confirmation.";
                    $response['verify_token'] = $token;
                    return $response;

                } else {

                    $response['statusCode'] = 422;
                    $errors['message'][] = "Sending otp is failed, Please try again";
                    $response['errors'] = $errors;
                    return $response;

                }

            } else {
                $response['message'] = "OTP has been sent on your mobile for confirmation.";
                $response['verify_token'] = $token;
                return $response;
            }


       }else{

            $response['statusCode'] = 422;
            $errors['message'][] = Yii::$app->params['apiMessage']['common']['actionFailed'];
            $response['errors'] = $errors;
            return $response;
         
       }
        
    }



    
    /**
     * update mobile without verifcation
     */
    public function actionChangeMobile(){
        $id = Yii::$app->user->identity->id;
       $model= new User();
        $model->scenario ='updateMobile';
       $request = Yii::$app->request;
       $params = $request->bodyParams;
       $model->attributes=$params;
        if(!$model->validate()) {
            $response['statusCode']=422;
            $response['errors']=$model->errors;
            return $response;
        }
        $string=$params['phone'].'#'.$params['country_code'];

       $token   =  base64_encode($string);
       $otp = mt_rand(1000, 9999);
       
       $modelRes = $this->findModel($id);
       $modelRes->phone  = $params['phone'];
       $modelRes->country_code =$params['country_code'];
       if($modelRes->save(false)){
            $response['message']="Mobile number has been updated successfully";
            return $response; 

       }else{
        
            $response['statusCode']=422;
            $errors['message'][] = "Process failed, Please try again";
            $response['errors']=$errors;
            return $response; 
       }
        
    }

     /**
     * update mobile and verify
     */
    public function actionVerifyOtp(){

       
        
        $id = Yii::$app->user->identity->id;
        $model = $this->findModel($id);
        $model->scenario ='verifyMobileOtp';
        $request = Yii::$app->request;
        $params = $request->bodyParams;
        $model->attributes=$params;
        if(!$model->validate()) {
            $response['statusCode']=422;
            $response['errors']=$model->errors;
            return $response;
        }
        $verify_token =$params['verify_token'];
        $token_arr =  explode('#',base64_decode($verify_token));
       
        
       $model->verification_token  = null;
       $model->phone  = $token_arr[0];
       $model->country_code  = $token_arr[1];
       $model->is_phone_verified  = 1;
       
       
       if($model->save(false)){
            $response['message']="Phone has been updated successfully";
            return $response; 
       }else{
            $response['statusCode']=422;
            $errors['message'][] = "Phone update process failed";
            $response['errors']=$errors;
            return $response; 
       }
        
    }

      /**
     * update password
     */
    public function actionUpdatePassword()
    {
        $id = Yii::$app->user->identity->id;
        $model = $this->findModel($id);
        $model->scenario = 'changePassword';
        $request = Yii::$app->request;
        $params = $request->bodyParams;
        $model->attributes = $params;
        if (!$model->validate()) {
            $response['statusCode'] = 422;
            $response['errors'] = $model->errors;
            return $response;
        }

        $password = $params['password'];
        $oldPassword = $params['old_password'];
        if (!$model->validatePassword($oldPassword, $model->password_hash)) {
            $response['statusCode'] = 422;
            $errors['message'][] = Yii::$app->params['apiMessage']['user']['oldPasswordIncorrect'];
            $response['errors'] = $errors;
            return $response;
        }

        $model->password_hash = Yii::$app->security->generatePasswordHash($password);
        //$model->last_password_updated_at = time();
        if ($model->save(false)) {
            $response['message'] = Yii::$app->params['apiMessage']['user']['passwordChanged'];
            return $response;
        } else {

            $response['statusCode'] = 422;
            $errors['message'][] = Yii::$app->params['apiMessage']['common']['actionFailed'];
            $response['errors'] = $errors;
            return $response;

        }

    }

    /**
     * update  Profile image user
     */
    public function actionUpdateProfileImage(){
        $id = Yii::$app->user->identity->id;
        $model = $this->findModel($id);

        $preImage = $model->image;
        $model->scenario ='updateProfileImage';

        
        if (Yii::$app->request->isPost) {
            $model->load(Yii::$app->getRequest()->getBodyParams(), '');
            $model->imageFile = UploadedFile::getInstanceByName('imageFile'); 
            if(!$model->validate()) {
                $response['statusCode']=422;
                $response['errors']=$model->errors;
                return $response;
            }

            if($model->imageFile){

                $modelFileUpload = new FileUpload();
                $type =     FileUpload::TYPE_USER;
                $files = $modelFileUpload->uploadFile($model->imageFile,$type,false);
                
                $model->image 		= 	  $files[0]['file']; 

             
               //  $s3->commands()->delete('./'.Yii::$app->params['pathUploadUserFolder'].'/'.$preImage)->execute(); /// delete previous
                //$promise = $s3->commands()->upload('./video-thumb/'.$imageName, $imagePath)->async()->execute();
            }

                

            if($model->save()){
            
            $response['message']='Profile image updated successfully';
            return $response; 

            }

      
      }

        
    }

    /**
     * nearest user
     */
    public function actionNearestUser(){
        $userId= Yii::$app->user->identity->id;
        $model = new User();
        $model = $this->findModel($userId);
        $location = $model->userLocation;
        $cityIds=[];
        foreach($model->userLocation as $location)
        {
            $cityIds[] = $location->city_id;
        }
        $cityIds   = array_unique($cityIds);

        $user = $model->find()
            ->innerJoinWith('userLocation') 
            ->select(['user.id','user.name','user.username','user.email','user.description','user.phone','user.image'])
             ->where(['user_location.city_id' => $cityIds,'user.status'=>User::STATUS_ACTIVE,'user.role'=>User::ROLE_CUSTOMER ])
             ->andWhere(['<>','user.id',$userId])
             ->all();
    
        

            
        $response['message']='User list found successfully';
        $response['user']=$user;
        return $response; 


        
    }


    /**
     * nearest user
     */
    public function actionSearchUser(){
        $userId= Yii::$app->user->identity->id;
        $model = new User();
        $modleBlockedUser = new BlockedUser();
        $userIdsBlockedMe = $modleBlockedUser->getUserIdsWhomeBlockMe($userId);


        //$model->scenario ='searchUser';
        $model->load(Yii::$app->getRequest()->getBodyParams(), '');
        if(!$model->validate()) {
            $response['statusCode']=422;
            $response['errors']=$model->errors;
            return $response;
        }


        $is_following_user =@(int)Yii::$app->getRequest()->getBodyParams()['is_following_user'];
        $is_follower_user =@(int)Yii::$app->getRequest()->getBodyParams()['is_follower_user'];
        $is_popular_user =@(int)Yii::$app->getRequest()->getBodyParams()['is_popular_user'];
        
        
        $query = $model->find()
            //->select(['user.id','user.name','user.username','user.email','user.description','user.phone','user.image'])
            ->select(['user.id','user.name','user.email','user.bio','user.description','user.image','user.is_verified','user.country_code','user.phone','user.country','user.city','user.sex','user.dob','user.is_chat_user_online','user.chat_last_time_online','user.location','user.latitude','user.longitude'])
             ->where(['user.role'=>User::ROLE_CUSTOMER])
             ->andwhere(['user.status'=>User::STATUS_ACTIVE])
             ->andWhere(['NOT',['user.id'=>$userIdsBlockedMe]]);

            if($model->name){
             $query->andWhere(['like','user.name',$model->name]);
            }elseif($is_following_user){
                
                $query->innerJoinWith('follower'); 
                $query->andWhere(['follower.follower_id'=>$userId]);
            }elseif($is_follower_user){
                
                $query->innerJoinWith('following'); 
                $query->andWhere(['follower.user_id'=>$userId]);
            }

        $user = $query->all();
    
        
            
        $response['message']='User list found successfully';
        $response['user']=$user;
        return $response; 


        
    }



    

    /**
     * sugested user
     */
    public function actionSugestedUser(){
         $userId= Yii::$app->user->identity->id;
        $model = new User();
        //$model->scenario ='searchUser';
        
        $modleBlockedUser = new BlockedUser();
        $userIdsBlockedMe = $modleBlockedUser->getUserIdsWhomeBlockMe($userId);
       
        $modelFollower = new Follower();

        $followingUserIds = $modelFollower->getFollowingUserIDs($userId);
        $followingUserIds[]=$userId;


        $query = $model->find()
            ->select(['user.id','user.name','user.username','user.email','user.bio','user.description','user.image','user.is_verified','user.country_code','user.phone','user.country','user.city','user.sex','user.dob','user.is_chat_user_online','user.chat_last_time_online','user.location','user.latitude','user.longitude'])
            ->addSelect(['sum(post.popular_point) as totalpopularPoint'])
             ->where(['user.role'=>User::ROLE_CUSTOMER])
             ->andwhere(['user.status'=>User::STATUS_ACTIVE])
             ->andWhere(['NOT',['user.id'=>$followingUserIds]])
             ->andWhere(['NOT',['user.id'=>$userIdsBlockedMe]])
             ->joinWith('userPost')
             ->groupBy('user.id')
             ->orderBy('totalpopularPoint desc')
             ->limit(10);

        $topUser = $query->all();
    

         /////top winners
         
         
         $query = $model->find()
             ->select(['user.id','user.name','user.username','user.email','user.bio','user.description','user.image','user.is_verified','user.country_code','user.phone','user.country','user.city','user.sex','user.dob','user.is_chat_user_online','user.chat_last_time_online','user.location','user.latitude','user.longitude'])
           //  ->addSelect(['sum(competition_user.is_winner) as totalWinner'])
             ->addSelect(['count(competition_winner_position.winner_user_id) as totalWinner'])
              ->where(['user.role'=>User::ROLE_CUSTOMER])
              ->andwhere(['user.status'=>User::STATUS_ACTIVE])
              ->andWhere(['NOT',['user.id'=>$followingUserIds]])
              ->andWhere(['NOT',['user.id'=>$userIdsBlockedMe]])
             // ->joinWith('userCompetition')
              ->joinWith('competitionWinnerUser')
             
              ->groupBy('user.id')
              
              ->orderBy('totalWinner desc')
              ->limit(10);
       
         $topWinner = $query->all();
            
        $response['message']='User list found successfully';
        $response['topUser']=$topUser;
        $response['topWinner']=$topWinner;
        return $response; 

        
        
    }


     /**
     * find a frined
     */
    public function actionFindFriend(){
        $userId= Yii::$app->user->identity->id;
        $modelSearch = new UserSearch();
        
      //  print_r(Yii::$app->request->queryParams);
       // $model->load(Yii::$app->request->queryParams, '');
        $result = $modelSearch->searchFindFriend(Yii::$app->request->queryParams);

  
            
        $response['message']='User list found successfully';
        $response['user']=$result;
        return $response; 


        
    }


    /**
     * Report user
     */
    public function actionReportUser()
    {
        
        
        $model = new ReportedUser();
        $userId = Yii::$app->user->identity->id;
        
        $model->scenario ='create';
        $model->load(Yii::$app->getRequest()->getBodyParams(), '');
        if(!$model->validate()) {
            $response['statusCode']=422;
            $response['errors']=$model->errors;
        
            return $response;
        }

       $reportToUserId =  @(int) $model->report_to_user_id;
       
       $totalCount = $model->find()->where(['report_to_user_id'=>$reportToUserId, 'user_id'=>$userId,'status'=>ReportedUser::STATUS_PENDING])->count();
       if($totalCount>0){
        $response['statusCode']=422;
        $errors['message'][] = Yii::$app->params['apiMessage']['user']['alreadyReported'];
        $response['errors']=$errors;
         return $response; 

       }

       
        $model->status = ReportedUser::STATUS_PENDING;
        if($model->save(false)){
            $response['message']=Yii::$app->params['apiMessage']['user']['reportedSuccess'];
            return $response; 
        }else{

            $response['statusCode']=422;
            $errors['message'][] = Yii::$app->params['apiMessage']['common']['actionFailed'];
            $response['errors']=$errors;
        }
    }




    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }


}


