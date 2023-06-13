<?php
namespace api\modules\v1\models;
use Yii;
use api\modules\v1\models\GiftHistory;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Expression;

class giftHistorySearch extends GiftHistory
{
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            
            [['send_on_type','live_call_id','post_id'], 'integer'],
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
    
    /**
     * search post
     */

    public function search($params)
    {
        $userId     =     Yii::$app->user->identity->id;
        
        $this->load($params,'');

        
        $query = GiftHistory::find()
        ->where(['gift_history.reciever_id'=>$userId])
        ->joinWith(['senderDetail' => function($query) {
            $query->select(['name','username','email','image','id','is_chat_user_online','chat_last_time_online','location','latitude','longitude']);
        }])
        ->orderBy(['gift_history.id'=>SORT_DESC]);
        

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
         $query->andFilterWhere([
            'send_on_type' => $this->send_on_type,
            'live_call_id' => $this->live_call_id,
            'post_id' => $this->post_id
            
        ]);
      
        return $dataProvider;
    }
    
    
}
