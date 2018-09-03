<?php

namespace app\modules\admin\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Users;
use app\models\Logs;

/**
 * LogsSearch represents the model behind the search form of `app\models\Logs`.
 */
class LogsSearch extends Logs
{

    public $search_value;
    public $search_type;
    public $search_from;
    public $search_to;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['text', 'date', 'category'], 'safe'],
            [['search_value', 'search_type', 'search_from', 'search_to'], 'trim'],
        ];
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
        $query = Logs::find();

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
            'date' => $this->date,
            'category' => $this->category,
        ]);

        if( !empty($this->search_type) && !empty($this->search_value) ){
          switch( $this->search_type ){
            case "id":
              $query->andFilterWhere(['user_id' => $this->search_value]);
            break;
            case "login":
              $userId = -1;
              $userModel = Users::findOne(['login' => $this->search_value]);
              if( !empty($userModel) ){
                $userId = $userModel->id;
              }
              $query->andFilterWhere(['user_id' => $userId]);
            break;
            case "email":
              $userId = -1;
              $userModel = Users::findOne(['email' => $this->search_value]);
              if( !empty($userModel) ){
                $userId = $userModel->id;
              }
              $query->andFilterWhere(['user_id' => $userId]);
            break;
            case "ip":
              $query->andFilterWhere(['ip' => $this->search_value]);
            break;
          }
        }

        $query->andFilterWhere(['or',
            ['like','text', $this->text],
            ['like','ip', $this->text],
            ['like','browser', $this->text],
            ['like','referer', $this->text],
        ]);
        $query->andFilterWhere(['like', 'category', $this->category]);

        if( !empty($this->search_from) && !empty($this->search_to) ){
          $query->andFilterWhere(['between', 'date', $this->search_from.' 00:00:00', $this->search_to.' 23:59:59']);
        }else
        if( !empty($this->search_from) ){
          $query->andFilterWhere(['between', 'date', $this->search_from.' 00:00:00', date('Y-m-d H:i:s')]);
        }else
        if( !empty($this->search_to) ){
          $query->andFilterWhere(['between', 'date', date('Y-m-d H:i:s', 0), $this->search_to.' 23:59:59']);
        }

        return $dataProvider;
    }
}
