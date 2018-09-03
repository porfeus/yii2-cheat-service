<?php

namespace app\modules\admin\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Users;
use app\models\Jetid;

/**
 * UsersSearch represents the model behind the search form of `app\models\Users`.
 */
class UsersSearch extends Users
{

    public $search_value;
    public $search_type;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'trafbalans', 'pay', 's1i2', 'notify_send', 'reseller_show', 'lastdate', 'time', 'z'], 'integer'],
            [['login', 'pass', 'fio', 'v', 'email', 'r', 'type', 'info', 'ses', 'ip', 'unicses', 'news', 'notify', 'reseller', 'reseller_data', 'percent', 'pv', 'ppip', 'date', 'ppv', 'kap', 'nip', 'nkap', 'ppr', 'prich'], 'safe'],
            [['coef1', 'balans', 'koef', 'procent', 'coef2', 'coef3', 'unikjet', 'krbalans'], 'number'],
            [['search_value', 'search_type'], 'trim'],
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
        $query = Users::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
             $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'coef1' => $this->coef1,
            'balans' => $this->balans,
            'trafbalans' => $this->trafbalans,
            'koef' => $this->koef,
            'procent' => $this->procent,
            'pay' => $this->pay,
            's1i2' => $this->s1i2,
            'notify_send' => $this->notify_send,
            'reseller_show' => $this->reseller_show,
            'lastdate' => $this->lastdate,
            'coef2' => $this->coef2,
            'coef3' => $this->coef3,
            'unikjet' => $this->unikjet,
            'krbalans' => $this->krbalans,
            'time' => $this->time,
            'z' => $this->z,
        ]);

        if( !empty($this->search_type) && !empty($this->search_value) ){
          switch( $this->search_type ){
            case "email":
              $query->andFilterWhere(['email' => $this->search_value]);
            break;
            case "login":
              $query->andFilterWhere(['login' => $this->search_value]);
            break;
            case "openset":
              $userId = -1;
              $userModel = Users::searchUserBySettingsId($this->search_value);
              if( $userModel ){
                \Yii::$app->response->redirect(['/admin/users/updateset', 'id' => $this->search_value])->send();
                exit;
              }else{
                $query->andFilterWhere(['id' => $userId]);
              }
            break;
            case "open_schedule":
              $userId = -1;
              $userModel = Users::searchUserBySettingsId($this->search_value);
              if( $userModel ){
                \Yii::$app->response->redirect(['/admin/users/schedule', 'id' => $this->search_value])->send();
                exit;
              }else{
                $query->andFilterWhere(['id' => $userId]);
              }
            break;
            case "open_template":
              $userId = -1;
              $userModel = Users::searchUserBySettingsId($this->search_value);
              if( $userModel ){
                \Yii::$app->response->redirect(['/admin/users/robot-edit', 'id' => $this->search_value])->send();
                exit;
              }else{
                $query->andFilterWhere(['id' => $userId]);
              }
            break;
            case "set":
              $userId = -1;
              $userModel = Users::searchUserBySettingsId($this->search_value);
              if( $userModel ){
                $query->andFilterWhere(['id' => $userModel->id]);
              }else{
                $query->andFilterWhere(['id' => $userId]);
              }
            break;
            case "user":
              $query->andFilterWhere(['id' => $this->search_value]);
            break;
          }
        }

        $query->andFilterWhere(['like', 'login', $this->login])
            ->andFilterWhere(['like', 'pass', $this->pass])
            ->andFilterWhere(['like', 'fio', $this->fio])
            ->andFilterWhere(['like', 'v', $this->v])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'r', $this->r])
            ->andFilterWhere(['like', 'coef1', $this->coef1])
            ->andFilterWhere(['like', 'type', $this->type])
            ->andFilterWhere(['like', 'info', $this->info])
            ->andFilterWhere(['like', 'ses', $this->ses])
            ->andFilterWhere(['like', 'ip', $this->ip])
            ->andFilterWhere(['like', 'unicses', $this->unicses])
            ->andFilterWhere(['like', 'news', $this->news])
            ->andFilterWhere(['like', 'notify', $this->notify])
            ->andFilterWhere(['like', 'reseller', $this->reseller])
            ->andFilterWhere(['like', 'reseller_data', $this->reseller_data])
            ->andFilterWhere(['like', 'percent', $this->percent])
            ->andFilterWhere(['like', 'pv', $this->pv])
            ->andFilterWhere(['like', 'ppip', $this->ppip])
            ->andFilterWhere(['like', 'date', $this->date])
            ->andFilterWhere(['like', 'ppv', $this->ppv])
            ->andFilterWhere(['like', 'kap', $this->kap])
            ->andFilterWhere(['like', 'nip', $this->nip])
            ->andFilterWhere(['like', 'nkap', $this->nkap])
            ->andFilterWhere(['like', 'ppr', $this->ppr])
            ->andFilterWhere(['like', 'prich', $this->prich]);

        return $dataProvider;
    }
}
