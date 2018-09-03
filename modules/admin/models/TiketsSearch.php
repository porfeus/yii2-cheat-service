<?php

namespace app\modules\admin\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Tikets;

/**
 * TiketsSearch represents the model behind the search form of `app\models\Tikets`.
 */
class TiketsSearch extends Tikets
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'parent_id', 'user', 'is_support', 'readed', 'answered', 'archived'], 'integer'],
            [['date', 'title', 'message', 'login'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributes()
    {
      return array_merge(parent::attributes(),
        [
          'login',
        ]
      );
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

        $query = Tikets::find()
          ->joinWith('userInfo')
          ->where(['parent_id'=>0])
          ->andFilterWhere(['!=', 'login', 'NULL']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['id'=>SORT_DESC]]
        ]);

        $dataProvider->setSort([
            'defaultOrder' => ['id'=>SORT_DESC],
            'attributes' => [
                'id',
                'login' => [
                    'asc' => ['users.login' => SORT_ASC],
                    'desc' => ['users.login' => SORT_DESC],
                    'label' => 'Логин'
                ],
                'title',
                'date',
            ]
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
            'parent_id' => $this->parent_id,
            'user' => $this->user,
            'is_support' => $this->is_support,
            'readed' => $this->readed,
            'answered' => $this->answered,
            'archived' => $this->archived,
        ]);

        switch( $params['status'] ?? '' ){
          case static::STATUS_READED:
            $query->andFilterWhere([
                'answered' => 1,
                'archived' => 0,
            ]);
          break;
          case static::STATUS_ARCHIVED;
            $query->andFilterWhere([
                'archived' => 1,
            ]);
          break;
          default:
            $query->andFilterWhere([
                'answered' => 0,
                'archived' => 0,
            ]);
          break;
        }

        $query->andFilterWhere(['like', 'login',
        $params['TiketsSearch']['login'] ?? '']);

        $query->andFilterWhere(['like', 'date', $this->date])
            ->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'message', $this->message]);

        return $dataProvider;
    }
}
