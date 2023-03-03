<?php
namespace api\modules\v1\models;
use Yii;
use yii\helpers\ArrayHelper;

use api\modules\v1\models\Package;
use api\modules\v1\models\Ad;

class Payment extends \yii\db\ActiveRecord
{
    
    const  TYPE_PRICE                        =1;
    const  TYPE_COIN                         =2;

    const  TRANSACTION_TYPE_CREDIT  =1;
    const  TRANSACTION_TYPE_DEBIT   =2;

    const  PAYMENT_TYPE_PACKAGE             =1;
    const  PAYMENT_TYPE_AWARD               =2;
    const  PAYMENT_TYPE_WITHDRAWAL          =3;
    const  PAYMENT_TYPE_WITHDRAWAL_REFUND   =4;
    const  PAYMENT_TYPE_LIVE_TV_SUBSCRIBE   =5;
    const  PAYMENT_TYPE_GIFT                =6;
    const  PAYMENT_TYPE_REDEEM_COIN         =7;
    const  PAYMENT_TYPE_EVENT_TICKET         =8;
    const  PAYMENT_TYPE_EVENT_TICKET_REFUND  =9;
    
 
    const  PAYMENT_MODE_IN_APP_PURCHASE      =1;
    const  PAYMENT_MODE_PAYPAL               =2;
    const  PAYMENT_MODE_WALLET               =3;
    const  PAYMENT_MODE_STRIPE               =4;
    const  PAYMENT_MODE_RAZORPAY             =5;
    
    

   
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'payment';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id','type','coin','user_id','package_id','transaction_type','payment_type','payment_mode','live_tv_id','gift_history_id','event_ticket_booking_id','created_at'], 'integer'],
            [['amount'], 'number'],
            [['transaction_id'], 'string'],
            [['package_id','amount','transaction_id'], 'required','on'=>'packageSubscription'],
            [['package_id'], 'checkPackage','on'=>'packageSubscription'],

            

        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => Yii::t('app', 'User'),
            'package_id' => Yii::t('app', 'Package'),
            'transaction_type' => Yii::t('app', 'Transaction Type'),
            'payment_type' => Yii::t('app', 'Payment Type'),
            'payment_mode' => Yii::t('app', 'Payment Mode'),
            'created_at'=> Yii::t('app', 'Created At'),
            
        ];
    }
   
    public function beforeSave($insert)
    {
        if ($insert) {
            $this->created_at = time();
            $this->user_id =   Yii::$app->user->identity->id;
          
        }

        
        return parent::beforeSave($insert);
    }

    

    public function checkPackage($attribute, $params, $validator)
    {
        if(!$this->hasErrors()){
           
            $count= Package::find()->where(['id'=>$this->$attribute])->count();
            if($count <= 0){
                $this->addError($attribute, 'Invalid Package');     
            }
            
        }
       
    }


    public function checkAd($attribute, $params, $validator)
    {
        if(!$this->hasErrors()){
           
            $count= Ad::find()->where(['id'=>$this->$attribute])->count();
            if($count <= 0){
                $this->addError($attribute, 'Invalid Ad');     
            }
            
        }
       
    }


}
