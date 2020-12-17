<?php
namespace frontend\modules\basic\components;

use Yii;
use yii\base\Component;

use common\models\User;

class UserDetails extends Component
{
    public function getPersonId($id){
        $mymodel = User::findOne($id);
        return $mymodel->peopleId;
    }
    
}