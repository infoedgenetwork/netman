<?php

$this->title="Dashboard";

use frontend\assets\LocalAssets;
use yii\helpers\Html;

$this->registerLinkTag([
//'title' => 'Live News for Yii',
'rel' => 'stylesheet',
//'type' => 'application/rss+xml',
'href' => 'https://unpkg.com/treeflex/dist/css/treeflex.css',
]);
LocalAssets::register($this);
?>
<div class="dashboard-default-index">
    <div class="page-header col-md-offset-1">
    <h1 ><?= $this->title ?></h1>
    </div>
    <div class="col-lg-2 summary" >
        <h2>Summary</h2>
        <strong>Member Name :</strong><br> <?=  $membership->memberName; ?>
        <br><strong>Status:</strong> <?=  $membership->status; ?>
        <br><strong>Member No:</strong> <?=  $membership->memberNo ?>
        <br><strong>Rank:</strong> <?=  $membership->memberRank ?>
        <br><strong>Level:</strong> <?=  $membership->level ?>
        
        <?php
        foreach($membership->levelCount as $i=>$arr) {
        echo "<br><strong>level ".$i." Count: </strong>".$arr ;
        }?>
        
        
        <hr>
        <h2>Sponsoring</h2>
        <strong>Sponsor's Name :</strong><br> <?=  $membership->sponsorName; ?>
        <p>Total Sponsored:</p>
        <hr>
        <h2>Links</h2>
        <p>Marketing</p>
        <hr
        <p>Payments</p>
        <hr>
        
    </div>
    
    <div class="col-lg-9 centerpiece " >
        <h2 style="align-content: center">Geneology</h2>
        <?= $this->render('_form', [
                'orgchart' => $orgchart,
        ]) ?>
        <strong>Note: </strong>Place mouse over picture to see the member's name
        <div class="tf-tree">
            
  <!--<ul>
    <li>
      <span class="tf-nc">1</span>
      <ul>
        <li>
            <span class="tf-nc" title="Andrew Kubo" ><img src="images/Person13.jpg" height="32px" width="32px" ></img><br>Kubo</span>
          <ul>
            <li><span class="tf-nc">4</span></li>
            <li>
              <span class="tf-nc">5</span>
              <ul>
                <li><span class="tf-nc">9</span></li>
                <li><span class="tf-nc">10</span></li>
              </ul>
            </li>
            <li><span class="tf-nc">6</span></li>
          </ul>
        </li>
        <li>
          <span class="tf-nc">3</span>
          <ul>
            <li><span class="tf-nc">7</span></li>
            <li><span class="tf-nc">8</span></li>
          </ul>
        </li>
      </ul>
    </li>
  </ul>-->
  <?= $mytree ?>
  </div>
        <div>
            
        </div>
        <!--<?= print_r($parents) ?>-->
    </div>
    
</div>
