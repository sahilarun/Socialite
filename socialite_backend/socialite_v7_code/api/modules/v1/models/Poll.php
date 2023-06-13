<?php
namespace api\modules\v1\models;
use Yii;
use yii\helpers\ArrayHelper;
use api\modules\v1\models\LiveTvSubscriber;
use api\modules\v1\models\LiveTvViewer;
use api\modules\v1\models\LiveTvFavorite;
use api\modules\v1\models\LiveTvCategory;
use api\modules\v1\models\TvShow;
use common\models\TvShowEpisode;

class Poll extends \yii\db\ActiveRecord
{
    const STATUS_ACTIVE=10;
    const STATUS_INACTIVE=9;
    const STATUS_DELETED=0;

   
    public $imageFile;
    public $transaction_id;
   

    
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'poll';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'status','category_id','start_time','end_time'], 'required'],
            
            [['status', 'id','category_id','campaigner_id','created_at','created_by','updated_at','updated_by'], 'integer'],
            [['title','description'], 'string'],
            
            
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
        unset($fields['parent_id'],$fields['status'],$fields['priority'],$fields['leval']);
        //$fields[] = 'categoryName';

        $fields['categoryName'] = (function($model){
            return @$model->category->name;
           // return (@$model->isReported) ? 1: 0;
        });
        $fields['campaignerName'] = (function($model){
            return @$model->campaigner->name;
           // return (@$model->isReported) ? 1: 0;
        });

        return $fields;
    }


    public function extraFields()
    {
        return ['pollQuestion'];
    }
   
 

    public function getCategory(){

        return $this->hasOne(Category::className(), ['id' => 'category_id']);

    }

    public function getCampaigner(){

        return $this->hasOne(Organization::className(), ['id' => 'campaigner_id']);

    }
    
    public function getPollQuestion()
    {
        return $this->hasMany(PollQuestion::className(), ['poll_id' => 'id'])->andOnCondition(['poll_question.status' => PollQuestion::STATUS_ACTIVE])->limit(10);

    }

    public function getPollQuestions()
    {
        return $this->hasMany(PollQuestion::className(), ['poll_id' => 'id'])->andOnCondition(['poll_question.status' => PollQuestion::STATUS_ACTIVE])->limit(10);

    }




}
