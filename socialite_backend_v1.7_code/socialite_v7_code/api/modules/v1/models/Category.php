<?php
namespace api\modules\v1\models;
use Yii;
use yii\helpers\ArrayHelper;
use api\modules\v1\models\LiveTv;
use api\modules\v1\models\Event;
use api\modules\v1\models\FileUpload;


class Category extends \yii\db\ActiveRecord
{
    const STATUS_ACTIVE=10;
    const STATUS_INACTIVE=9;
    const STATUS_DELETED=0;

    const LEVEL_MAIN = 1;
    const LEVEL_SUB = 2;
    
    const TYPE_EVENT = 1;

    const TYPE_REEL_AUDIO = 4;
    const TYPE_FUNDRASING=5;

    const TYPE_POLL= 7;
    

    

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
            [['imageFile'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg'],
            [['status', 'id','parent_id','priority','type'], 'integer'],
            [['name'], 'string', 'max' => 100],
           // [['name', 'status'], 'save'],

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
    
    public function fields()
    {
        
        $fields = parent::fields();
        unset($fields['parent_id'],$fields['status'],$fields['priority'],$fields['leval']);
        $fields[] = 'imageUrl';
        return $fields;
    }


    public function extraFields()
    {
        return ['subCategory','liveTv','event','campaignList'];
        return ['subCategory','liveTv','event','pollList'];
    }
   
    public function getMainCategory(){
        return $this->find()->select(['id','name','image'])->where(['status'=>$this::STATUS_ACTIVE,'level'=>$this::LEVEL_MAIN])->all();
        
    }
    /*public function getSubCategory($parentId){
        return $this->find()->select(['id','name'])->where(['status'=>$this::STATUS_ACTIVE,'level'=>$this::LEVEL_SUB,'parent_id'=>$parentId])->all();
        
    }*/
    public function getParent(){

        return $this->hasOne(Category::className(), ['id' => 'parent_id']);

    }

    public function getSubCategory(){

        return $this->hasMany(Category::className(), ['parent_id' => 'id'])->from(['subCategory' => Category::tableName()])->select(['id','name','parent_id']);

    }

    public function getImageUrl()
    {
        if($this->image){
            $modelFileUpload = new FileUpload();
            return $modelFileUpload->getFileUrl($modelFileUpload::TYPE_CATEGORY,$this->image);

           
        }else{
            return '';
        }
        
    }

    public function getLiveTv()
    {
        return $this->hasMany(liveTv::className(), ['category_id' => 'id'])->andOnCondition(['live_tv.status' => liveTv::STATUS_ACTIVE])->limit(10);

    }
    public function getEvent()
    {
        return $this->hasMany(Event::className(), ['category_id' => 'id'])->andOnCondition(['event.status' => Event::STATUS_ACTIVE])->limit(10);

    }

    public function getCampaignList()
    {
        return $this->hasMany(Campaign::className(), ['category_id' => 'id'])->andOnCondition(['campaign.status' => Campaign::STATUS_ACTIVE])->limit(10);

    }
    public function getPollList()
    {
        return $this->hasMany(Poll::className(), ['category_id' => 'id'])->andOnCondition(['poll.status' => Poll::STATUS_ACTIVE])->orderBy(['id'=> SORT_DESC])->limit(10);

    }
    

    

}
