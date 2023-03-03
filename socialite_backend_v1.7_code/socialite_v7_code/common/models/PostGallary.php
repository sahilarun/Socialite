<?php
namespace common\models;
use Yii;
use yii\helpers\ArrayHelper;
use common\models\User;
use common\models\Post;
use common\models\FileUpload;


class PostGallary extends \yii\db\ActiveRecord
{
    const STATUS_ACTIVE = 10;
    const STATUS_DELETED = 0;

    const IS_DEFAULT_YES = 1;
    const IS_DEFAULT_NO = 0;

    
    const MEDIA_TYPE_IMAGE = 1;
    const MEDIA_TYPE_VIDEO = 2;

    public $filenameFile;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'post_gallary';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [

            [['post_id', 'status', 'id', 'type', 'media_type','is_default'], 'integer'],
            [['filename','video_thumb'], 'string', 'max' => 256],
          //  [['filename'], 'file', 'skipOnEmpty' => false, 'extensions' => 'png, jpg,mp4', 'on' => 'uploadFile'],
            //[['filename'], 'file', 'skipOnEmpty' => false, 'extensions' => 'mp4', 'maxSize' => '2048000', 'on' => 'uploadVideo'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'filename' => Yii::t('app', 'filename'),
            'status' => Yii::t('app', 'Status'),

        ];
    }

   


     public function getFilenameUrl(){
        if($this->filename){
            $modelFileUpload = new FileUpload();
            return $modelFileUpload->getFileUrl($modelFileUpload::TYPE_POST,$this->filename);
        }
     }

     public function getVideoThumbUrl(){
        if($this->video_thumb){
            
            $modelFileUpload = new FileUpload();
            return $modelFileUpload->getFileUrl($modelFileUpload::TYPE_POST,$this->video_thumb);

            //return Yii::$app->params['pathUploadImage'] ."/".$this->video_thumb;
        }
     }


   
}
