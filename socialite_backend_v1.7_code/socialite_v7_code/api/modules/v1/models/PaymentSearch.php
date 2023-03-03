<?php
namespace api\modules\v1\models;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use api\modules\v1\models\Payment;
use yii\db\Expression;

class PaymentSearch extends Payment
{
    public $month;
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['month'], 'string'],
            [['transaction_type','type'], 'integer'],
            
            
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
    public function searchMyPayment($params)
    {
        $userId = Yii::$app->user->identity->id;
        $this->setAttributes($params);
    
        $query = Payment::find()
        ->where(['payment.user_id'=>$userId])
        ->orderBy(['payment.created_at'=>SORT_DESC]);

        
        if($this->type==Payment::TYPE_PRICE){
            $query->andWhere(['payment.type'=>Payment::TYPE_PRICE]);
        }else if($this->type==Payment::TYPE_COIN){
            $query->andWhere(['payment.type'=>Payment::TYPE_COIN]);
        }


        //->where(['payment.user_id'=>$userId,'payment.type'=>Payment::TYPE_PRICE])

        $monthArr = explode(',',$this->month);
        $query->andWhere(['IN',"(date_format(FROM_UNIXTIME(created_at), '%Y-%m' ))", $monthArr]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false
        ]);
        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
       
        // grid filtering conditions
        $query->andFilterWhere([
            'payment.transaction_type' => $this->transaction_type
        ]);
       
      // $query->andFilterWhere(['like', 'order_number', $this->order_number]);
       // $result = $dataProvider->getModels();
       // print_r($result);
        
        return $dataProvider;
    }

    

    
}
