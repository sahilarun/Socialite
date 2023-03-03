<?php
namespace common\models;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use common\models\FileUpload;


/**
 * This is the model class for table "countryy".
 *
 */
class Category extends \yii\db\ActiveRecord
{
    const STATUS_ACTIVE=10;
    const STATUS_INACTIVE=9;
    const STATUS_DELETED=0;

    const LEVEL_MAIN = 1;
    const LEVEL_SUB = 2;

    const TYPE_EVENT = 1;
    const TYPE_SHOW_CATEGORY = 3;
    const TYPE_REEL_AUDIO = 4;
    const TYPE_FUNDRASING=5;
    const TYPE_PODCAST_SHOW=6;
    const TYPE_POLL=7;
    public $imageFile;

    
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'category';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'status'], 'required'],
            //[['parent_id'], 'required','on'=>['createSubCategory','updateSubCategory']],
            //[['imageFile'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg'],
            [['status', 'id','parent_id','priority','type'], 'integer'],
            [['name'], 'string', 'max' => 100],
            [['name'], 'required','on'=>'createMainCategory'],
            [['name'], 'required','on'=>'updateMainCategory'],
            //[['imageFile'], 'required','on'=>'createMainCategory'],
            [['parent_id','image','priority','parent_id'], 'safe'],

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
            'parent_id' => Yii::t('app', 'Main Category'),
            
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
   /*public function upload()
    {
        
        if ($this->validate()) {
            if($this->imageFile){

            $filename=$this->imageFile->baseName.'_'.time(). '.' . $this->imageFile->extension;
            $this->imageFile->saveAs('@frontend/web/uploads/category/' .$filename ,false);
            return $filename;
            }
        } else {
            return false;
        }
    }*/
    
    public function getMainCategory(){
        return $this->find()->select(['id','name','image'])->where(['status'=>$this::STATUS_ACTIVE,'level'=>$this::LEVEL_MAIN])->all();
        
    }
    /*
    public function getSubCategory($parentId){
        return $this->find()->select(['id','name'])->where(['status'=>$this::STATUS_ACTIVE,'level'=>$this::LEVEL_SUB,'parent_id'=>$parentId])->all();
        
    }
    public function getParent(){

        return $this->hasOne(Category::className(), ['id' => 'parent_id']);

    }

    
    public function getChildCategory(){

        return $this->hasMany(Category::className(), ['parent_id' => 'id']);

    }

    

    */
    
    public function getImageUrl()
    {
        $modelFileUpload = new FileUpload();
        return $modelFileUpload->getFileUrl($modelFileUpload::TYPE_CATEGORY,$this->image);

        
    }

   



    

}
