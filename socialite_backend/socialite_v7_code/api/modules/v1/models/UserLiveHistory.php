<?php
namespace api\modules\v1\models;
use Yii;
use yii\helpers\ArrayHelper;

use api\modules\v1\models\User;
use api\modules\v1\models\GiftHistory;
//use api\modules\v1\models\Message;

class UserLiveHistory extends \yii\db\ActiveRecord
{
    const STATUS_ONGOING=1;
    const STATUS_COMPLETED=2;
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_live_history';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        
        return [
            [['id','user_id','start_time','end_time','total_time','status'], 'integer']
            //[['message'], 'string']
            
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
           
            
        ];
    }
   
    public function beforeSave($insert)
    {
        if ($insert) {
            $this->start_time = time();
        }

        
        return parent::beforeSave($insert);
    }
    
    
    
    public function fields()
    {
        $fields = parent::fields();

        
       
        return $fields;
    }
    public function extraFields()
    {
        return ['giftSummary'];
    }


    public function getGiftSummary(){

        $modelGiftHistory =  new GiftHistory();
       
        $result = $modelGiftHistory->find()
        ->select(['count(id) as totalGift','sum(coin) as totalCoin'])
        ->where(['live_call_id'=>$this->id,'send_on_type'=>GiftHistory::SEND_TO_TYPE_LIVE])->asArray()->one();
        
        $totalGift = (int)$result['totalGift'];
        $totalCoin = (int)$result['totalCoin'];
        
        $response=[
            'totalGift'=>$totalGift,
            'totalCoin'=>$totalCoin

        ];
        return $response;

     }

   

 
    

    

}
