<?php
namespace common\models;
use Yii;
use yii\helpers\ArrayHelper;
use common\models\User;
use common\models\Comment;
use common\models\Competition;
use common\models\ReportedPost;
use common\models\PostGallary;
use common\models\FileUpload;


class Post extends \yii\db\ActiveRecord
{
    const STATUS_ACTIVE=10;
    const STATUS_DELETED = 0;
    const STATUS_BLOCKED=9;

    const IS_SHARE_POST_YES=1;
    const IS_SHARE_POST_NO=0;

    const IS_WINNING_NO=0;
    const IS_WINNING_YES=1;


    const TYPE_NORMAL=1;
    const TYPE_COMPETITION=2;
    const TYPE_CLUB         =3;
    const TYPE_REEL         =4;


    public  $imageFile;
    public  $videoFile;
    public  $hashtag;
        
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'post';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [[ 'title','video','image'], 'string'],
            [['status','user_id','audio_id','total_view','total_like','total_share','total_comment','is_share_post','share_level','origin_post_id', 'created_at','created_by', 'updated_by'], 'integer'],
            [['updated_by', 'updated_at','hashtag','audio_id','is_share_post','share_level','origin_post_id'], 'safe'],
            [['title'], 'string', 'max' => 256],
            ['status', 'in', 'range' => [0,9,10]],
            //[[ 'title','imageFile' ], 'required','on'=>'create'],
            
            //[[ 'title','category_id','currency' ], 'required','on'=>'update'],
            //[['imageFile'], 'file', 'skipOnEmpty' => false, 'extensions' => 'png, jpg','on'=>'create'],
            //[['videoFile'], 'file', 'skipOnEmpty' => false,'extensions' => 'mp4','maxSize' => '6048000','on'=>'create'],
            //[[ 'id' ], 'required','on'=>'share'],
            
            
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => Yii::t('app', 'Title'),
            'status' => Yii::t('app', 'Status'),
            'video' => Yii::t('app', 'video'),
            'image' => Yii::t('app', 'Image'),
            'user_id' => Yii::t('app', 'User'),
            'total_view' => Yii::t('app', 'Total views'),
            'total_share' => Yii::t('app', 'Total share'),
            'total_comment' => Yii::t('app', 'Total comment'),
            'is_share_post' => Yii::t('app', 'Share Post'),
            'audio_id' => Yii::t('app', 'Audio')
        ];
    }
   
    public function beforeSave($insert)
    {
        if ($insert) {
            $this->created_at = time();
            $this->created_by =   Yii::$app->user->identity->id;
          
        }else{
            $this->updated_at = time();
            $this->updated_by =   Yii::$app->user->identity->id;

        }

        
        return parent::beforeSave($insert);
    }
    

    public function getStatus()
    {
       if($this->status==$this::STATUS_BLOCKED){
           return 'Blocked';
       }else if($this->status==$this::STATUS_ACTIVE){
           return 'Active';    
       }
    }
    public function getStatusDropDownData()
    {
        return array(self::STATUS_ACTIVE => 'Active', self::STATUS_BLOCKED => 'Blocked');
    }
   
    public function getTotalPostCount()
    {
        return Post::find()->where(['<>','status',self::STATUS_DELETED])->count();
    }
   
    public function getLastTweleveMonth()
    {
        $month =  strtotime("+1 month");
        for ($i = 1; $i <= 12; $i++) {
            $months[(int)date("m", $month)] = date("M", $month);
            $month = strtotime('+1 month', $month);
        }
        return $months;
        
    }


    public function getLastTweleveMonthPost()
    {
        
        $totalAds = [];
        $monthArr =[];
        $months = $this->getLastTweleveMonth();
        
        $res= Yii::$app->db->createCommand("SELECT month(from_unixtime(created_at)) as month, count(id) as total_ad FROM post where status!=0 and from_unixtime(created_at) >= DATE_SUB(CURDATE(), INTERVAL 1 YEAR) group by month")->queryAll();

        foreach($months as $key => $month){
            $found_key = array_search($key, array_column($res, 'month'));  
            //echo gettype($found_key), "\n";
            if(is_int($found_key)){
                $totalAd =  $res[$found_key]['total_ad'];
            }else{
                $totalAd = 0;
            }
            //echo $totalAds;
            /*echo '=====================';
            echo '<br>';
            echo $key.'#'.$month;
            echo '<br>';*/

            //print_r($found_key);
            
            $totalAds[]=$totalAd;
           
            $monthArr[]=$month;

        }
        $output=[];

        $output['data'] = $totalAds;
        $output['dataCaption'] = $monthArr;
        return $output;

        
    }
    


    
    public function getAudioUrl(){


       
        
        $audio = $this->audio;
        //return Yii::$app->params['pathUploadAudio'] ."/".$audio;
        
        $modelFileUpload = new FileUpload();
        return $modelFileUpload->getFileUrl($modelFileUpload::TYPE_POST,$audio);
        
        //return Yii::$app->urlManagerFrontend->baseUrl.'/uploads/banner/thumb/'.$audio;
        
    }
/*
    public function getVideoUrl(){
        
        $video = $this->video;
        return Yii::$app->params['pathUploadVideo'] ."/".$video;
        //return Yii::$app->urlManagerFrontend->baseUrl.'/uploads/banner/thumb/'.$audio;
        
    }*/
    public function getImageUrl(){
        
        
        $modelFileUpload = new FileUpload();
        return $modelFileUpload->getFileUrl($modelFileUpload::TYPE_POST,$this->image);
        
        
        
        
    }
    
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id'=>'user_id']);
        
    }


    public function getPostComment()
    {
        return $this->hasMany(PostComment::className(), ['post_id'=>'id']);
        
    }

    public function getPostGallary()
    {
        return $this->hasMany(PostGallary::className(), ['post_id'=>'id']);
        
    }
    
    public function getCompetition()
    {
        return $this->hasOne(Competition::className(), ['id'=>'competition_id']);
        
    }
    
    public function getReportedPost()
    {
        return $this->hasMany(ReportedPost::className(), ['post_id'=>'id']);
        
    }
    
    public function getReportedPostActive()
    {
        return $this->hasMany(ReportedPost::className(), ['post_id'=>'id'])->andOnCondition(['reported_post.status' => ReportedPost::STATUS_PENDING]);
        
    }


   /* public function getImageUrlBig(){
        
        $image = $this->image;
        if(empty($this->image)){
            $image  ='default.png';
        }
        return Yii::$app->urlManagerFrontend->baseUrl.'/uploads/banner/original/'.$image;
        
    }*/

    

}
