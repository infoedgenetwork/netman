<?php

namespace app\modules\dashboard\controllers;

use yii\web\Controller;
use frontend\modules\dashboard\models\Membership;


/**
 * Default controller for the `dashboard` module
 */
class DefaultController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        $membership = new Membership();
        $orgchart = [
                        [['v' => '1001', 'f' => '<a href="/mills/frontend/web/index.php?r=site/index"><img src="images/person4.jpg" /></a><br  /> <strong>Mike</strong><br  />You'],'', 'The President'],
                        [['v' => 'Jim', 'f' => '<img src="https://placeholdit.imgix.net/~text?txtsize=20&txt=Jim&w=120&h=150" /><br  /><strong>Jim</strong><br  />The Test'],'1001', 'VP'],
                        [['v' => 'ทดสอบ', 'f' => '<img src="https://placeholdit.imgix.net/~text?txtsize=20&txt=ทดสอบ&w=120&h=150" /><br  /><strong>ทดสอบ</strong><br  />The Test'], '1001', ''],
                        [['v' => 'Bob', 'f' => '<img src="images/person4.jpg" /><br  /><strong>Bob</strong><br  />The Test'], 'Jim', 'Bob Sponge'],
                        [['v' => 'Carol', 'f' => '<img src="images/person5.jpg" /><br  /><strong>Carol</strong><br  />The Test'], '1001', 'Carol Title'],

                ];

        return $this->render('index',[
            'membership'=> $membership,
            //'orgchart' => $orgchart,
            'orgchart' => $membership->showArray,
            'parents' => $membership->parents,
        ]);
        
    }
}
