<?php
namespace api\modules\v1\models;
use Yii;
use api\modules\v1\models\Post;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Expression;
use api\modules\v1\models\BlockedUser;

class PostSearch extends Post
{
    
    public $is_popular_post;
    public $is_following_user_post;
    public $is_my_post;
    public $is_winning_post;
    public $is_recent;
    public $is_reel;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['hashtag','title'], 'string'],
            [['user_id','club_id','audio_id','is_popular_post','is_following_user_post','is_my_post','is_winning_post','is_recent','is_reel'], 'integer'],
          //  [['title'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
          return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function searchMyPost($params)
    {
        $userId   =  Yii::$app->user->identity->id;
        $this->load($params,'');
        $query = Post::find()
       
        ->select(['post.id','post.type','post.post_content_type','post.user_id','post.title','post.competition_id','post.club_id','post.image','post.total_view','post.total_like','post.total_comment','post.total_share','post.popular_point','post.status','post.created_at','post.latitude','post.longitude','post.address'])
        ->where(['post.user_id'=>$userId])
        ->andWhere(['<>','post.status',Post::STATUS_DELETED])
       // ->andWhere(['post.type'=>[Post::TYPE_NORMAL,Post::TYPE_COMPETITION,Post::TYPE_CLUB]])
        ->orderBy(['post.id'=>SORT_DESC]);

        if($this->is_reel){
            
            $query->andWhere(['post.type'=>Post::TYPE_REEL]);
        }else{
            
            $postArr =[];
            $postArr[]=Post::TYPE_NORMAL;
            $postArr[]=Post::TYPE_COMPETITION;
            $postArr[]=Post::TYPE_CLUB;
          
            $query->andWhere(['post.type'=>$postArr]);
        
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 20,
            ]
        ]);

      //  $this->load($params);

        $this->setAttributes($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
       
        // grid filtering conditions
         $query->andFilterWhere([
            //'ad.user_id' => $this->user_id,
           //  'hash_tag.hashtag' => $this->hashtag,
            
            
        ]);

      
        return $dataProvider;
    }

    public function searchMyPostMentionUser($params)
    {
        $userId   =  Yii::$app->user->identity->id;
        // $countryId   =  Yii::$app->user->identity->country_id;
         
         
         $this->load($params,'');

         $mentionUserId = $this->user_id;
         

         //searchMyPostMentionUser
 
        
         
         
         $query = Post::find()
         ->select(['post.id','post.type','post.post_content_type','post.user_id','post.title','post.competition_id','post.club_id','post.image','post.total_view','post.total_like','post.total_comment','post.total_share','post.popular_point','post.status','post.created_at','post.latitude','post.longitude','post.address'])
         //->select(['post.id','post.type','post.user_id','post.title','post.competition_id','post.is_winning','post.image','post.total_view','post.total_like','post.total_comment','post.total_share','post.popular_point','post.status','post.created_at'])
         ->joinWith(['user' => function($query) {
             $query->select(['name','username','email','image','id','is_chat_user_online','chat_last_time_online','location','latitude','longitude']);
         }])
         ->joinWith('mentionUsers')
         ->where(['<>','post.status',Post::STATUS_DELETED])
        // ->andWhere(['post.type'=>[Post::TYPE_NORMAL,Post::TYPE_COMPETITION,,Post::TYPE_CLUB]])
         ->andWhere(['mention_user.user_id'=>$mentionUserId]);

         
         
         $query->orderBy('id desc');
         
 
         $query->distinct();
 
 
         $dataProvider = new ActiveDataProvider([
             'query' => $query,
             'pagination' => [
                 'pageSize' => 20,
             ]
         ]);
         
       //  $this->setAttributes($params);
         if (!$this->validate()) {
             // uncomment the following line if you do not want to return any records when validation fails
             // $query->where('0=1');
             return $dataProvider;
         }
       
         
         return $dataProvider;
    }

    

    /**
     * search post
     */

    public function search($params)
    {
        $userId   =  Yii::$app->user->identity->id;
       // $countryId   =  Yii::$app->user->identity->country_id;
       $modleBlockedUser = new BlockedUser();
       $userIdsBlockedMe = $modleBlockedUser->getUserIdsWhomeBlockMe($userId);
       
      

     //  $conditionTime =time();
        
        $isFilter=false;
        $this->load($params,'');

        if($this->user_id || $this->hashtag || $this->is_following_user_post ){ /// for whether within country or overall
            $isFilter=true; /// overall
        }

        
        
        $query = Post::find()
        ->select(['post.id','post.type','post.post_content_type','post.user_id','post.title','post.competition_id','post.club_id','post.image','post.total_view','post.total_like','post.total_comment','post.total_share','post.popular_point','post.status','post.created_at','post.latitude','post.longitude','post.address','post.audio_id','post.audio_start_time','post.audio_end_time','post.is_add_to_post',])
        ->joinWith(['user' => function($query) use ($isFilter){
            
            $query->select(['id','username','name','email','bio','description','image','is_verified','country_code','phone','country','city','sex','dob','is_chat_user_online','chat_last_time_online','location','latitude','longitude']);
           
        }])
        ->joinWith('hashtags')
        ->where(['<>','post.status',Post::STATUS_DELETED])
       // ->andWhere(['post.type'=>[Post::TYPE_NORMAL,Post::TYPE_COMPETITION]])
       
        ->andWhere(['NOT',['post.user_id'=>$userIdsBlockedMe]]);
        
        //->orderBy(['post.id'=>SORT_DESC]);
        if($this->is_recent){
            $query->orderBy('id desc');
        }else{
            $query->orderBy(new Expression('rand()'));
        }

       
        

        if($this->is_popular_post){
            $popuplarPointCondition = Yii::$app->params['postPopularityPoint']['popuplarPointCondition'];
            $query->andWhere(['>','post.popular_point',$popuplarPointCondition]);

        }
        if($this->is_following_user_post){

            $query->joinWith(['followers' => function($query) use ($userId){
               //$query->where(['follower_id'=>$userId]);
              
            }]);
            //$query->andWhere(['follower.follower_id'=>$userId]);
            if($this->is_my_post){
                $query->andWhere(
                    ['or',
                        
                        ['follower.follower_id'=>$userId],
                        ['post.user_id'=>$userId]
                        
                    ]);
            
            }else{
                $query->andWhere(['follower.follower_id'=>$userId]);
            }
            


        }else{
            if($this->is_my_post){
                $query->andWhere(['post.user_id'=>$userId]);
            }

        }

        if($this->is_reel){
            
            $query->andWhere(['post.type'=>Post::TYPE_REEL]);
        }else{
            
            $postArr =[];
            $postArr[]=Post::TYPE_NORMAL;
            $postArr[]=Post::TYPE_COMPETITION;
            $postArr[]=Post::TYPE_CLUB;
          
            $query->andWhere(
                ['or',
                    ['post.type'=>$postArr],
                    ['post.type'=>Post::TYPE_REEL,'post.is_add_to_post'=>Post::COMMON_YES]
                    
                ]);
        
        }

        $query->distinct();


        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 20,
            ]
        ]);
        
      //  $this->setAttributes($params);
        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        // grid filtering conditions
        if($this->is_winning_post){
            $query->andFilterWhere([
                'post.is_winning' => $this->is_winning_post,
            ]);   
        }
         $query->andFilterWhere([
            'post.user_id' => $this->user_id,
            'post.club_id' => $this->club_id,
            'post.audio_id' => $this->audio_id,
            'hash_tag.hashtag' => $this->hashtag
        ]);
        
        $query->andFilterWhere(
            ['or',
                
                ['hash_tag.hashtag'=>$this->title],
                ['like', 'title', $this->title]
                
                
            ]);

            
    

        
        return $dataProvider;
    }



     /**
     * search story post
     */

    public function searchStory($params)
    {
        $userId   =  Yii::$app->user->identity->id;
       // $countryId   =  Yii::$app->user->identity->country_id;
        
        $isFilter=false;
        $this->load($params,'');

        
        $conditionTime = strtotime('-24 hours', time());

        
        $query = Post::find()
        ->select(['post.id','post.type','post.post_content_type','post.user_id','post.title','post.competition_id','post.club_id','post.image','post.total_view','post.total_like','post.total_comment','post.total_share','post.popular_point','post.status','post.created_at','post.latitude','post.longitude','post.address'])
        ->joinWith(['user' => function($query) use ($isFilter){
            $query->select(['name','username','email','image','id','is_chat_user_online','chat_last_time_online','location','latitude','longitude']);
        }])
        ->joinWith('hashtags')
        ->where(['<>','post.status',Post::STATUS_DELETED])
        ->andWhere(['<>','post.user_id',$userId])
        ->andWhere(['post.type'=>Post::TYPE_STORY])
        ->andWhere(['>','post.created_at',$conditionTime])
        ->orderBy(['post.id'=>SORT_DESC]);
        //->orderBy(new Expression('rand()'));

        $query->joinWith(['followers' => function($query) use ($userId){
            //$query->where(['follower_id'=>$userId]);
        }]);
        $query->andWhere(
            ['or',
                
                ['follower.follower_id'=>$userId],
                ['post.user_id'=>$userId]
                
            ]);
    
      //  $query->andWhere(['follower.follower_id'=>$userId]);

        $query->distinct();

        return $query->all();


        /*

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' =>false
        ]);
        
      //  $this->setAttributes($params);
        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
       
        return $dataProvider;*/
    }


    public function searchMyStory($params)
    {
        $userId   =  Yii::$app->user->identity->id;
       // $countryId   =  Yii::$app->user->identity->country_id;
        
        $isFilter=false;
        $this->load($params,'');

        
        $conditionTime = strtotime('-24 hours', time());

        
        $query = Post::find()
        ->select(['post.id','post.type','post.post_content_type','post.user_id','post.title','post.competition_id','post.club_id','post.image','post.total_view','post.total_like','post.total_comment','post.total_share','post.popular_point','post.status','post.created_at','post.latitude','post.longitude','post.address'])
        ->joinWith(['user' => function($query) use ($isFilter){
            $query->select(['name','username','email','image','id','is_chat_user_online','chat_last_time_online','location','latitude','longitude']);
        }])
        ->joinWith('hashtags')
        ->where(['<>','post.status',Post::STATUS_DELETED])
        ->andWhere(['post.user_id'=>$userId])
        ->andWhere(['post.type'=>Post::TYPE_STORY])
        ->orderBy(['post.id'=>SORT_DESC]);
        

        $query->distinct();


        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' =>  [
                'pageSize' => 20
            ]
        ]);
        
      //  $this->setAttributes($params);
        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
       
        return $dataProvider;
    }
    
    
}
