<?php
namespace common\models;
use Yii;
use yii\helpers\ArrayHelper;
use common\models\User;
use common\models\Post;


class PostComment extends \yii\db\ActiveRecord
{
    const STATUS_ACTIVE=10;
    const STATUS_DELETED=0;
        
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'post_comment';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id','user_id','post_id','status','created_at'], 'integer'],
            [['comment'], 'string','max'=>200]
            

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
            'post_id' => Yii::t('app', 'Post'),
            'comment' => Yii::t('app', 'Comment'),
            'created_at' => Yii::t('app', 'Created at')
        ];
    }
   
    public function beforeSave($insert)
    {
        if ($insert) {
            $this->created_at = time();
          
        }

        
        return parent::beforeSave($insert);
    }
    public function getTotalCommetCount()
    {
        return PostComment::find()->count();
    }
   

    
     /**
     * RELEATION START
     */
    public function getUser()
    {
       
        return $this->hasOne(User::className(), ['id'=>'user_id']);
        
    }

    



   /* public function getImageUrlBig(){
        
        $image = $this->image;
        if(empty($this->image)){
            $image  ='default.png';
        }
        return Yii::$app->urlManagerFrontend->baseUrl.'/uploads/banner/original/'.$image;
        
    }*/

    

}
