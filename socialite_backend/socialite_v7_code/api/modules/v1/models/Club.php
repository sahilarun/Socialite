<?php
namespace api\modules\v1\models;
use Yii;
use yii\helpers\ArrayHelper;
use api\modules\v1\models\User;
use api\modules\v1\models\ClubUser;
use api\modules\v1\models\ClubInvitationRequest;


use api\modules\v1\models\FileUpload;

class Club extends \yii\db\ActiveRecord
{
    const STATUS_ACTIVE=10;
    const STATUS_DELETED = 0;


    const PRIVACY_TYPE_PUBLIC=1;
    const PRIVACY_TYPE_PRIVATE=2;
    
    const COMMON_YES = 1;
    const COMMON_NO = 0;

    
    public $club_user_id;
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'club';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        
        return [
            [['id','user_id','category_id','privacy_type','is_request_based','status','is_chat_room','chat_room_id','created_at','created_by','updated_at','updated_by'], 'integer'],
            [['name','description','image'], 'string'],
            [[ 'name','privacy_type' ], 'required','on'=>['create','update']],
            [['name'], 'checkUniqueName','on'=>['create','update']],
            [[ 'id' ], 'required','on'=>['join']],
            [[ 'id','club_user_id' ], 'required','on'=>['remove']],
            [['club_user_id'], 'safe'],
            
           
    
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
            $this->created_by   =   Yii::$app->user->identity->id;
            
        }

        
        return parent::beforeSave($insert);
    }

    public function extraFields()
    {
        return ['totalJoinedUser','createdByUser'];
    }

    
    public function fields()
    {
        $fields = parent::fields();
        $fields[] = 'imageUrl';
       // $fields[] = 'competitionImage';
       $fields['is_joined'] = (function($model){
         return (@$model->isJoined) ? 1: 0;
       });
       $fields['is_join_requested'] = (function($model){
        return (@$model->isJoinRequested) ? 1: 0;
      });
        return $fields;
    }

    
     /**START valication function custom  */
     public function checkUniqueName($attribute, $params, $validator)
     {
        
         if(!$this->hasErrors()){
             if($this->isNewRecord){
                 $count= Club::find()->where([$attribute=>$this->$attribute])->andWhere(['<>','status',self::STATUS_DELETED])->count();
             }else{
                
                 $count= Club::find()->where([$attribute=>$this->$attribute])->andWhere(['<>','id',$this->id])->andWhere(['<>','status',self::STATUS_DELETED])->count();
             }
             
             if($count){
                 $this->addError($attribute, 'Club name already exist');     
             }
             
         }
        
     }


    public function getClubUser()
    {
       return $this->hasMany(ClubUser::className(), ['club_id'=>'id'])->andOnCondition(['club_user.status'=>ClubUser::STATUS_ACTIVE])->orderBy(["club_user.is_admin" => SORT_DESC]);
        
    }

    public function getCreatedByUser()
    {
       return  $this->hasOne(User::className(), ['id'=>'user_id']);
       
        
    }
    

    public function getImageUrl()
    {
        if($this->image){
            $modelFileUpload = new FileUpload();
            return $modelFileUpload->getFileUrl($modelFileUpload::TYPE_CHAT,$this->image);

            //return Yii::$app->params['pathUploadChat'] . "/" . $this->image;
        }else{
            return '';
        }
        
    }
    public function getIsJoined()
    {
        return $this->hasOne(ClubUser::className(), ['club_id' => 'id'])->andOnCondition(['club_user.status'=>ClubUser::STATUS_ACTIVE,'club_user.user_id' => @Yii::$app->user->identity->id]);
        
        
    }

    public function getIsJoinRequested()
    {
        return $this->hasOne(ClubInvitationRequest::className(), ['club_id' => 'id'])->andOnCondition(['club_invitation_request.type'=>ClubInvitationRequest::TYPE_REQUEST,'club_invitation_request.status'=>ClubInvitationRequest::STATUS_PENDING,'club_invitation_request.user_id' => @Yii::$app->user->identity->id]);
        
        
    }
    public function getTotalJoinedUser()
    {
        return (int)$this->hasMany(ClubUser::className(), ['club_id'=>'id'])->andOnCondition(['club_user.status'=>ClubUser::STATUS_ACTIVE])->count();
        
    }

    

    

    

    
    

    

}
