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

class PollQuestionOption extends \yii\db\ActiveRecord
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
        return 'poll_qustion_options';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title','question_id','status'], 'required'],
            
            [['question_id','id'], 'integer'],
            [['title'], 'string'],
            
            [['title','question_id'], 'required','on'=>['create','update']],
            
            
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

        $fields['total_option_vote_count'] = (function($model){
            return (int)@$model->totalOptionVoteCount;
        });

        $fields['is_option_vote'] = (function($model){
            return (@$model->userOptionVote) ? 1: 0;
        });

        return $fields;
    }


    public function extraFields()
    {
        // return ['options'];
    }
   
 

    public function getTotalOptionVoteCount(){
        return $this->hasMany(PollQuestionAnswer::className(), ['poll_question_id' => 'question_id','question_option_id' => 'id'])->andOnCondition(['poll_question_answer.status' => PollQuestionAnswer::STATUS_ACTIVE])->count();
    }

    public function getUserOptionVote(){
        return $this->hasOne(PollQuestionAnswer::className(), ['poll_question_id' => 'question_id','question_option_id' => 'id'])->andOnCondition(['poll_question_answer.user_id' => @Yii::$app->user->identity->id,'poll_question_answer.status' => PollQuestionAnswer::STATUS_ACTIVE]);
    }

}
