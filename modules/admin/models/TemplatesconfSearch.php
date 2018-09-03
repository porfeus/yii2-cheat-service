<?php

namespace app\modules\admin\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Templatesconf;

/**
 * TemplatesconfSearch represents the model behind the search form of `app\models\Templatesconf`.
 */
class TemplatesconfSearch extends Templatesconf
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'url'], 'integer'],
            [['name', 'value'], 'safe'],
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
        $query = Templatesconf::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
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
            'name' => $this->name,
            'value' => $this->value,
            'url' => $this->url,
        ]);

        return $dataProvider;
    }
}
