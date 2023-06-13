<?php
namespace common\models;
use Yii;
use yii\helpers\ArrayHelper;

//use api\modules\v1\models\Package;
//use api\modules\v1\models\Ad;
use common\models\User;

class Payment extends \yii\db\ActiveRecord
{
    
    const  TRANSACTION_TYPE_CREDIT  =1;
    const  TRANSACTION_TYPE_DEBIT   =2;

    const  PAYMENT_TYPE_PACKAGE             =1;
    const  PAYMENT_TYPE_AWARD               =2;
    const  PAYMENT_TYPE_WITHDRAWAL          =3;
    const PAYMENT_TYPE_WITHDRAWAL_REFUND  = 4;

    
    const  PAYMENT_MODE_IN_APP_PURCHASE      =1;
    const  PAYMENT_MODE_PAYPAL               =2;
    const  PAYMENT_MODE_WALLET               =3;
    const  PAYMENT_MODE_STRIPE               =4;
    const  PAYMENT_MODE_RAZORPAY             =5;
    


    
    const  TYPE_PRICE                        =1;
    const  TYPE_COIN                        =2;


    
    
   
    

    
   
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
            [['id','type','user_id','package_id','transaction_type','payment_type','payment_mode','created_at'], 'integer'],
            [['amount'], 'number'],
            [['transaction_id'], 'string'],
          //  [['package_id','amount','transaction_id'], 'required','on'=>'packageSubscription'],
          //  [['package_id'], 'checkPackage','on'=>'packageSubscription'],

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
           // $this->user_id =   Yii::$app->user->identity->id;
          
        }

        
        return parent::beforeSave($insert);
    }


    public function getLastTweleveMonth()
    {
        $month =  strtotime("+1 month");
        for ($i = 1; $i <= 12; $i++) {
            $months[(int)date("m", $month)] = date("M", $month);
            $month = strtotime('+1 month', $month);
        }
        return $months;
        
    }

    public function getLastTweleveMonthPayments()
    {
        
        $totalAds = [];
        $monthArr =[];
        $months = $this->getLastTweleveMonth();
        $res= Yii::$app->db->createCommand("SELECT month(from_unixtime(created_at)) as month, sum(amount) as total FROM payment where transaction_type=1 and from_unixtime(created_at) >= DATE_SUB(CURDATE(), INTERVAL 1 YEAR) group by month")->queryAll();
        foreach($months as $key => $month){
            $found_key = array_search($key, array_column($res, 'month'));  

            if(is_int($found_key)){
                $totalAd =   round($res[$found_key]['total']);
            }else{
                $totalAd = 0;
            }
            $totalAds[]=$totalAd;
            $monthArr[]=$month;

        }
        $output=[];

        $output['data'] = $totalAds;
        $output['dataCaption'] = $monthArr;
        return $output;

        
    }

    public function getTotalEarning()
    {
        
           
        return Payment::find()->where(['transaction_type'=>self::TRANSACTION_TYPE_CREDIT])->sum('amount');
        
    }

    
    
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id'=>'user_id']);
        
    }


    public function getPaymentTypeString()
    {
       if($this->payment_type==Payment::PAYMENT_TYPE_PACKAGE){
           return 'Package Subscription';
       }else if($this->payment_type==Payment::PAYMENT_TYPE_FEATURE_AD){
           return 'Feature Ad';    
       }else if($this->payment_type==Payment::PAYMENT_TYPE_BANNER_AD){
        return 'Banner Ad';    
      }
    }

    public function getPaymentModeString()
    {
       if($this->payment_mode==Payment::PAYMENT_MODE_IN_APP_PURCHASE){
           return 'Inapp Purchase';
       }else if($this->payment_mode==Payment::PAYMENT_MODE_PAYPAL){
           return 'Paypal';    
       }else if($this->payment_mode==Payment::PAYMENT_MODE_WALLET){
            return 'Wallet';    
        }else if($this->payment_mode==Payment::PAYMENT_MODE_STRIPE){
            return 'Stripe';    
        }else if($this->payment_mode==Payment::PAYMENT_MODE_RAZORPAY){
            return 'Rozarpay';    
        }

       
    }

}
