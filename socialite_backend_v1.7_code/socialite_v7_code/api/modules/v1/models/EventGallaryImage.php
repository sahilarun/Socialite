<?php
namespace api\modules\v1\models;
use Yii;
use yii\helpers\ArrayHelper;
use api\modules\v1\models\FileUpload;


class EventGallaryImage extends \yii\db\ActiveRecord
{
    const STATUS_DELETED=0;
    const STATUS_ACTIVE=10;
    const STATUS_INACTIVE=9;
    
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'event_gallary_image';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status', 'id'], 'integer'],
            [['image',], 'string', 'max' => 100]

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
   
    public function fields()
    {
        $fields = parent::fields();
        //$fields[] = 'audio_url';
       // $fields[] = 'imageUrl';
       //$fields[cate] = 'getuserLocation';
        return $fields;
    }
   
    public function getImageUrl(){
        $modelFileUpload = new FileUpload();
        return $modelFileUpload->getFileUrl($modelFileUpload::TYPE_EVENT,$this->image);
        
        //return Yii::$app->params['pathUploadCompetition'] ."/".$this->image;
    }
    

}
