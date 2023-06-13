<?php
namespace common\models;
use Yii;
use yii\helpers\ArrayHelper;
use common\models\FileUpload;

class Organization extends \yii\db\ActiveRecord
{
    const STATUS_ACTIVE=10;
    const STATUS_INACTIVE=9;
    const STATUS_DELETED=0;

    const TYPE_NGO=1;
    const TYPE_SOCIETY=2;
   
    public $audioFile;
    public $imageFile;

    
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'organization';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [

            [['name','type','status'], 'required'],
            [['image'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg'],
            [['name','description','email','address'], 'string'],

            [['status','type'], 'integer'],
            [['email'], 'email'],
            [['phone'], 'string'],

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
            'type' => Yii::t('app', 'Type'),
            'image' => Yii::t('app', 'Image'),
            'description' => Yii::t('app', 'Description'),
            'phone' => Yii::t('app', 'Phone Number'),
            'email' => Yii::t('app', 'Email'),
            'address' => Yii::t('app', 'Address'),
           
        ];
    }
   

    public function getStatus()
    {
       if($this->status==$this::STATUS_INACTIVE){
           return 'Inactive';
       }else if($this->status==$this::STATUS_ACTIVE){
           return 'Active';    
       }
    }
  

    public function getStatusDropDownData()
    {
        return array(self::STATUS_ACTIVE => 'Active', self::STATUS_INACTIVE => 'Inactive');
    }



    //   Dropdown
    public function getTypeDropDownData()
    {
        return array(self::TYPE_NGO => 'NGO', self::TYPE_SOCIETY => 'SOCIETY');
    }

   

    
   

    public function getImageUrl(){
        
        $modelFileUpload = new FileUpload();
        return $modelFileUpload->getFileUrl($modelFileUpload::TYPE_ORGNIZATION,$this->image);
        
    }

 


    public function getCategory(){

        return $this->hasOne(Category::className(), ['id' => 'category_id']);

    }


    

}


