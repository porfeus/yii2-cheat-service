<?php

namespace app\modules\admin\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Antifraud;
use app\models\Conf;

/**
 * AntifraudSearch represents the model behind the search form of `app\models\Antifraud`.
 */
class AntifraudSearch extends Antifraud
{
    public $suspicious = false;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'cost', 'balance'], 'integer'],
            [['userid', 'date', 'type', 'note'], 'safe'],
            [['suspicious'], 'boolean'],
        ];
    }

    public function attributeLabels()
    {
        $dynamicAttributes = [
          'suspicious' => 'Показать только подозрительные операции',
        ];
        return array_merge(parent::attributeLabels(), $dynamicAttributes);
    }

    /**
     * @inheritdoc
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
    public function search($params)
    {
        $query = Antifraud::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['id'=>SORT_DESC]]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'cost' => $this->cost,
            'balance' => $this->balance,
            'userid' => $this->userid,
        ]);

        $query
            ->andFilterWhere(['like', 'type', $this->type])
            ->andFilterWhere(['like', 'note', $this->note])
            ->andFilterWhere(['like', 'date', $this->date]);

        if( $this->suspicious ){
          $query->andFilterWhere(['>=', 'cost', Conf::getParams('suspicious_transaction_cost')]);
        }

        return $dataProvider;
    }
}
