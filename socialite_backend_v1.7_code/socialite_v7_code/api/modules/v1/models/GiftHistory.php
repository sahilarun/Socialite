<?php
namespace api\modules\v1\models;
use Yii;
use yii\helpers\ArrayHelper;


use api\modules\v1\models\Gift;



class GiftHistory extends \yii\db\ActiveRecord
{
    const SEND_TO_TYPE_LIVE=1;
    const SEND_TO_TYPE_PROFILE=2;
    const SEND_TO_TYPE_POST=3;
    
   
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'gift_history';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            

            [[ 'id','reciever_id','sender_id','coin','gift_id','send_on_type','live_call_id','post_id','created_at'], 'integer'],
            [['gift_id','reciever_id','send_on_type' ], 'required','on'=>['sendGift']],
            //[['reciever_id','send_on_type'], 'safe'],
            //[['id','reciever_id','send_on_type' ], 'required','on'=>['sendGift']],
            //[['transaction_id'], 'safe'],
            
            
            
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
            $this->created_at = time();
          
        }
        return parent::beforeSave($insert);
    }
    
    public function fields()
    {
        
        $fields = parent::fields();
        unset($fields['parent_id'],$fields['status'],$fields['priority'],$fields['leval']);
       
        return $fields;
    }


    public function extraFields()
    {
        return ['giftDetail','senderDetail'];
    }

    public function getGiftDetail()
    {
        return $this->hasOne(Gift::className(), ['id'=>'gift_id']);
    }

    public function getSenderDetail()
    {
        return $this->hasOne(User::className(), ['id'=>'sender_id']);
        
    }
   
 
    

}
