<?php
namespace api\modules\v1\models;
use Yii;
use yii\helpers\ArrayHelper;
//use api\modules\v1\models\CollectionUser;
use api\modules\v1\models\Follower;
use api\modules\v1\models\FileUpload;


class Story extends \yii\db\ActiveRecord
{
    const STATUS_ACTIVE=10;
    const STATUS_INACTIVE=9;
    const STATUS_DELETED=0;

    public $stories;

    
    
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'story';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status', 'id','user_id','created_at','type'], 'integer'],
            [['image','description','video','background_color'], 'string'],
            [['description'], 'string', 'max' => 200],
            [['type'], 'required','on'=>['create','update']],
            [['stories'], 'required','on'=>'createMain'],
            [['stories'], 'save'],

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
            $this->user_id       =   Yii::$app->user->identity->id;
          
        }
        return parent::beforeSave($insert);
    }

    
    public function fields()
    {
        
        $fields = parent::fields();
       // unset($fields['parent_id'],$fields['status'],$fields['priority'],$fields['leval']);
       $fields[] = "imageUrl"; 
       $fields[] = "videoUrl"; 
       
        return $fields;
    }

    

    public function extraFields()
    {
        return ['user'];
    }

   

    public function getImageUrl(){
        if($this->image){
            $modelFileUpload = new FileUpload();
            return $modelFileUpload->getFileUrl($modelFileUpload::TYPE_STORY,$this->image);

            //return Yii::$app->params['pathUploadStory'] ."/".$this->image;
        }
     }
     public function getVideoUrl(){
        if($this->video){
            $modelFileUpload = new FileUpload();
            return $modelFileUpload->getFileUrl($modelFileUpload::TYPE_STORY,$this->video);
            //return Yii::$app->params['pathUploadStory'] ."/".$this->video;
        }
     }

     
    /**
     * RELEATION START
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id'=>'user_id']);
        
    }

     public function getFollowers()
    {
        return $this->hasMany(Follower::className(), ['user_id'=>'user_id']);
        
    }

    

}
