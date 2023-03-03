<?php
namespace api\modules\v1\models;
use Yii;
use api\modules\v1\models\FileUpload;

class PostGallary extends \yii\db\ActiveRecord
{
    const STATUS_ACTIVE = 10;
    const STATUS_DELETED = 0;

    const IS_DEFAULT_YES = 1;
    const IS_DEFAULT_NO = 0;

    
    const MEDIA_TYPE_IMAGE = 1;
    const MEDIA_TYPE_VIDEO = 2;
    const MEDIA_TYPE_AUDIO = 3;

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
            [['filename'], 'file', 'skipOnEmpty' => false, 'extensions' => 'png, jpg,mp4', 'on' => 'uploadFile'],
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

    public function fields()
    {
        $fields = parent::fields();
        $fields[] = "filenameUrl";
        $fields[] = "videoThumbUrl";


        return $fields;
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

   

    public function updateGallary($postId, $images)
    {
        //print_r($locations);

        // $images = json_decode($images);
        $values = [];

        $isDefaultSet = false;

        $this->deleteAll( ['post_id' => $postId]);

        foreach ($images as $image) {
            //  print_r($location);
            $dataInner['post_id'] = $postId;
            $dataInner['type'] = $image['type'];
            $dataInner['media_type'] = $image['media_type'];
            $dataInner['filename'] = $image['filename'];
            $dataInner['video_thumb'] = $image['video_thumb'];
            
            if ($image['is_default'] && !$isDefaultSet) {
                $isDefaultSet = true;
                $dataInner['is_default'] = PostGallary::IS_DEFAULT_YES;
            } else {
                $dataInner['is_default'] = PostGallary::IS_DEFAULT_NO;

            }
            $dataInner['created_at'] = time();
            $values[] = $dataInner;
           // $isFirst = false;

        }

        if (count($values) > 0) {

            

            Yii::$app->db
                ->createCommand()
                ->batchInsert('post_gallary', ['post_id','type', 'media_type', 'filename','video_thumb','is_default', 'created_at'], $values)
                ->execute();
        }
    }

}
