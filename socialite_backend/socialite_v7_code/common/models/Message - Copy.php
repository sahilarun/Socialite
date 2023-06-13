<?php
namespace common\models;
use Yii;
use yii\helpers\ArrayHelper;
//namespace app\models\User;

class Message extends \yii\db\ActiveRecord
{
    const IS_READ_NO = 0;
    const IS_READ_YES = 1;
    
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'message';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id','sender_id','receiver_id','is_read','created_at'], 'integer'],
            [['message'], 'string']
            

        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'sender_id' => Yii::t('app', 'Sender'),
            'receiver_id' => Yii::t('app', 'Receiver'),
            'message' => Yii::t('app', 'Message'),
            'is_read'=> Yii::t('app', 'Read'),
            'created_at'=> Yii::t('app', 'Created At'),
            
        ];
    }
   
    public function beforeSave($insert)
    {
        if ($insert) {
            $this->created_at = time();
            $this->sender_id =   Yii::$app->user->identity->id;
          
        }

        
        return parent::beforeSave($insert);
    }
    

    public function getIsReadString()
    {
       if($this->is_read==$this::IS_READ_YES){
           return 'Yes';
       } else{
            return 'No';    
        }
    }

    public function getReceiverUser()
    {
        return $this->hasOne(User::className(), ['id'=>'sender_id']);
        
    }
    

    

}
