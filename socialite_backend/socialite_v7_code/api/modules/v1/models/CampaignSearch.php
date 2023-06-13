<?php
namespace api\modules\v1\models;
use Yii;
use api\modules\v1\models\Campaign;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Expression;

class CampaignSearch extends Campaign
{
    
    /**
     * {@inheritdoc}
     */
    public $my_joined_club;
    public function rules()
    {
        return [
            [['title'], 'string'],
            [['category_id','campaigner_id','campaign_for_id'], 'integer'],
          //  [['title'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
       
          return Model::scenarios();
    }


     /**
     * search story post
     */

    public function search($params)
    {
        $currentdate = time();
        $this->load($params,'');

    
        $query = Campaign::find()
        ->where(['campaign.status'=>Campaign::STATUS_ACTIVE])->andWhere(['<=', 'start_date',$currentdate])->andWhere(['>=', 'end_date',$currentdate])
        ->orderBy(['campaign.id'=>SORT_DESC]);

     
        // ->where(['campaign.status'=>Campaign::STATUS_ACTIVE])->andWhere(['>=','target_value','raised_value']);
        // ->orderBy(['campaign.id'=>SORT_DESC]);




        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 20,
            ]
        ]);
       
        if (!$this->validate()) {
          
            return $dataProvider;
        }
       
         $query->andFilterWhere([
            'campaign.category_id' => $this->category_id

           
            
        ]);

       
        $query->andFilterWhere([
            'campaign.campaigner_id' => $this->campaigner_id
          
            
        ]);

        
        $query->andFilterWhere([
            'campaign.campaign_for_id' => $this->campaigner_id
           
            
        ]);

        
      
        $query->andFilterWhere(
            [
                'or',
                    ['like', 'campaign.title', $this->title],
                    ['like', 'description', $this->description]
            ]
        );

        return $dataProvider;

    }

    // search

    public function CampaignMyFavorite($params)
    {
        $userId   =  Yii::$app->user->identity->id;
        $this->load($params,'');

        $query = Campaign::find()
        ->where(['campaign.status'=>Campaign::STATUS_ACTIVE])

        ->joinWith('campaignMyFavorite')
        ->andWhere(['campaign_favorite.user_id'=>$userId])
        ->orderBy(['campaign.title'=>SORT_ASC]);

       

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 20,
            ]
        ]);
        
        if (!$this->validate()) {
           
            return $dataProvider;
        }
      
         $query->andFilterWhere([
            'campaign.category_id' => $this->category_id
            
        ]);
      

        $query->andFilterWhere(
            [
                'or',
                    ['like', 'campaign.title', $this->title],
                    ['like', 'description', $this->description]
            ]
        );

        return $dataProvider;




    }

   


    


    
    
    

    
}
