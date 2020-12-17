<?php
use yii\helpers\Url;
/* @var $this yii\web\View */

$this->title = 'Home';

?>
<div class="site-index">

    <div class="jumbotron">
        <h1>Knowledge to Earn!</h1>

        <p class="lead">
        <div class="col-sm-12"><img src="../web/images/knowledge_to_earn_Logo1.jpg"></div>
        </p>

        
    </div>

    <div class="body-content">

        <div class="row">
            <div class="col-lg-4">
                <h2>About Us</h2>

    <p>Knowledgetoearn.com is an online Educational platform, Created by Optimum Performance Solutions LTD. dealing with Trainings and Mentorship for growth across the globe helping individuals maximize their Personal and Economic potentials.</p>

                <p><a class="btn btn-default" href="<?= Url::to(['site/about']) ?>">...More &raquo;</a></p>
            </div>
            <div class="col-lg-4">
                <h2>Products</h2>

                <p>Knowledgetoearn.com is a training and mentorship platform for the knowledge which is necessary but not learnt in schools.
To ensure that a person succeeds in this ever changing world, we believe that it is necessary to be equipped with practical knowledge which an individual can use to better his/her life in addition to the skills they have acquired in their schooling years.
The trainings are categorized as follows.</p>

                <p><a class="btn btn-default" href="<?= Url::to(['site/products'])?>">...more on products &raquo;</a></p>
            </div>
            <div class="col-lg-4">
                <h2>Affiliate Program</h2>

                <p>.</p>

                <p><a class="btn btn-default" href="#">...more  &raquo;</a></p>
            </div>
        </div>

    </div>
</div>
