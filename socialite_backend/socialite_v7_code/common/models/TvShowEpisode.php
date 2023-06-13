<?php
namespace common\models;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use common\models\FileUpload;
use common\models\Category;
use api\modules\v1\models\User;


/**
 * This is the model class 
 *
 */
class TvShowEpisode extends \yii\db\ActiveRecord
{
    const STATUS_ACTIVE=10;
    const STATUS_INACTIVE=9;
    const STATUS_DELETED=0;

    const COMMON_NO=0;
    const COMMON_YES=1;

    
    public $imageFile;

    
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tv_show_episode';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'status','tv_show_id'], 'required'],
            
            [['status', 'id','tv_show_id','created_by'], 'integer'],
            [['name','episode_period'], 'string'],
            
            [['name','tv_show_id','created_at'], 'required','on'=>['create','update']],
            
            [['image','video'], 'safe'],
            [['imageFile'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg'],
            // [['video'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, mp4','maxFiles' => 2],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => Yii::t('app', 'Name'),
            'status' => Yii::t('app', 'Status'),
            'tv_show_id' => Yii::t('app', 'Tv Show'),
            'created_at' => Yii::t('app', 'Show Episode Date'),
            'created_by' => Yii::t('app', 'Created by'),
            'video' => Yii::t('app', 'Video'),
            
            
        ];
    }



    public function getStatusDropDownData()
    {
        return array(self::STATUS_ACTIVE => 'Active', self::STATUS_INACTIVE => 'Inactive');
    }

    public function getStatus()
    {
       if($this->status==$this::STATUS_INACTIVE){
           return 'Inactive';
       }else if($this->status==$this::STATUS_ACTIVE){
           return 'Active';    
       }
    }
  
    
    
    public function getImageUrl()
    {
        $modelFileUpload = new FileUpload();
        return $modelFileUpload->getFileUrl($modelFileUpload::TYPE_TV_SHOW_EPISODE,$this->image);

        
    }

    public function getVideoUrl()
    {
        $modelFileUpload = new FileUpload();
        return $modelFileUpload->getFileUrl($modelFileUpload::TYPE_TV_SHOW_EPISODE,$this->video);

        
    }

    
    public function getCategory()
    {
        return $this->hasOne(Category::className(), ['id' => 'category_id']);

    }

 

    public function beforeSave($insert)
    {
        if ($insert) {
            // $this->created_at = time();
            $this->created_by   =   Yii::$app->user->identity->id;
            
        }

        
        return parent::beforeSave($insert);
    }

    public function getTvShowList(){
        return $this->find()->select(['id','name','image'])->all();
    }

}
