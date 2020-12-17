<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace frontend\modules\dashboard\models;

use Yii;
use yii\base\Model;
use\yii\helpers\ArrayHelper;
use yii\helpers\Html;


/**
 * Description of Membership
 *
 * @author Apache1
 */
class Membership extends Model {
    public $memberName;
    public $memberNo;
    public $memberRank;
    public $status;
    public $level;
    public $peopleId;
    public $sponsorName;
    public $sponsorNo;
    public $sponsorRank;
    public $sponsorId;
    public $parentName;
    public $parentNo;
    public $parentRank;
    public $parentId;
    public $parents = array(array());
    public $levelCount = array();
    public $showArray = array(array());
    public $bTreeList = array(array());
    public $tempList = array(array());
    public $bTreeMembers ;

    
    
    public function init()
    {
        $myqry = (new \yii\db\Query())
                ->select('*')
                ->from('people p')
                ->leftJoin('user u','u.peopleId=p.id')
                 ->leftJoin('sponsorship s','s.member=p.id')
                 ->leftJoin('statuses t','t.id=s.status')
                ->leftJoin('ranks r', 'r.id=s.rank')
                ->where(['u.id'=>Yii::$app->user->id])
                ->one();
              
        $this->setMemberValues($myqry);
        $this->parents[1][]=$this->peopleId;
        //$this->getChildParticulars();
        $this->getLevelParts();
    }
    
    
    private function setMemberValues(&$myqry)
    {
        $this->memberName = $myqry['firstName'].' '.$myqry['surname'];
        $this->memberNo =  $myqry['membershipNo'];
        $this->memberRank = $myqry['rankName'];
        $this->level = $myqry['level'];
        $this->status = $myqry['Status'];
        $this->peopleId = $myqry['member'];
        $this->sponsorId =  $myqry['sponsor'];
        $this->sponsorName = Yii::$app->memberdetails->getMemberPartsUsingPeopleId($this->sponsorId,6);
        $this->sponsorNo = Yii::$app->memberdetails->getMemberPartsUsingPeopleId($this->sponsorId);
        $this->parentId = $myqry['parent'];
        $this->parentName = Yii::$app->memberdetails->getMemberPartsUsingPeopleId($this->parentId,6);
        $this->parentNo = Yii::$app->memberdetails->getMemberPartsUsingPeopleId($this->parentId);
    }
    
    
    public function getLevelParts(){
        
        $extractIdx=0;// keeps track of total no of members
        $myqry = (new \yii\db\Query())
                ->from('sponsorship s')
                ->leftJoin('people p','p.id= member')
                ->leftJoin('ranks r', 'r.id=s.rank')
                ->orderBy(['membershipNo'=>SORT_ASC]);
        for( $i=0;$i<Yii::$app->params['maxLevels'];$i++ ){// $i keeps track of levels
            $relLevel=$i+1;
            $parentArr = $this->parents[$relLevel];
//            
            
            if(count($parentArr)==0)break;//Stop if no more children
            $this->levelCount[$relLevel] = count($parentArr);
            $levelItems = $myqry->select('*')
                            ->where(['level'=> ($this->level)+$relLevel])
                            ->andWhere(['IN','parent',$parentArr])
                            ->all();
            // extract required items
            $this->extractItems($levelItems,$i,$extractIdx);
            //get member No
            if($i==0){
                $this->parents[0]= $this->peopleId;
            }
            //get parents for next level
            $this->parents[$relLevel+1]=ArrayHelper::getColumn($levelItems,'member'); 
             //prepare next level 
            
        }        
    }
    
    public function extractItems($arr,$itemcnt,&$membercount)
    {
        
        foreach($arr as $i=>$member){
            if($membercount==0){
                $memberArr = array('v'=>$this->memberNo, 'f'=>'<img src="images/person15.jpg" /><br  /><strong>You</strong><br  />');
                $this->showArray[$membercount]= array($memberArr,' ',$this->memberName);
                $membercount++;
                //continue;
            }
            $firstArr=array('v'=>$member['membershipNo'], 'f'=>'<img src="images/person15.jpg" /><br  /><strong>'.$member['surname'].'</strong><br  />');
            $membername = $member['firstName'].' ' .$member['surname'];
            
            $this->showArray[$membercount]=array($firstArr,Yii::$app->memberdetails->getMemberPartsUsingPeopleId($member['parent']),$membername);
            $membercount ++; 
            
        }
        
    }
    
    /**
     * @
     * @param type $node
     */
    private function buildList($node/*MemberId*/) {
        $stack = new stack();
        $this->bTreeMembers=0;
        $curNode=$node;
        $otherNode=0;
        
        // select members from sponsorship whose parent is node
        $myqry = (new \yii\db\Query())
                ->select('*')
                ->from('sponsorship s')
                ->leftJoin('people','p.id = s.member')
                ->leftJoin('ranks r','r.id = s.Rank')
                ->where(['parent'=>$curNode]);
        $noOfChildren =$this->getChildCount($myqry);
       
        do{
            if($noOfChildren>1 ){//there is a right child
                
                $curNode=$this->extractMemberDetails($myqry,'L');
                //put right leg in a temporary Array and put the pointer on the stack
                $stack->push($this->extractTempDetails($myqry));
            }elseif($this->checkLeftChild($myqry)){
                $curNode=$this->extractMemberDetails($myqry,'L');
            }elseif($this->checkRightChild($myqry)){
                $curNode=$this->extractMemberDetails($myqry,'R');
            }else{// no children
                //copy the temporary list
                $curNode=$stack->pop();
                $this->bTreeList[]=$this->tempList[$curNode];
            }
        }while(!$stack->empty());
        
    }
    
    private function getChildCount($myqry) {
        
        return $myqry->count();
    }
    
    private function checkLeftChild($myqry){
        if($myqry->andWhere(['lft'=>1])->count()>0)
        {return true;}
        else 
        {return false;}
            
       
    }
    private function checkRightChild($myqry){
        if($myqry->andWhere(['rgt'=>1])->count()>0)
        {return true;}
        else 
        {return false;}
            
       
    }
    private function getRightChildNode($myqry){
        $theQry=$myqry->andWhere(['rgt'=>1])->one();
        return $theQry['member'] ;
    }
    /*
     *   items required
     *   person.Id, membershipNo,name,whether Left or Right, parentNode, Rank
     */
    private function extractMemberDetails($myqry,$side){
        if($side=='L'){
            $mySelection= $myqry->andWhere(['lft'=>1])->one();
        }elseif($side=='R'){
            $mySelection= $myqry->andWhere(['rgt'=>1])->one();
        }
        $this->bTreeList[]=array('pid'=>$mySelection['member'],
                                    'memberNo'=>$mySelection['membershipNo'],
                                    'memberName'=>$mySelection['surname'],
                                    'parentNo'=>$mySelection['parent'],
                                    'side'=> $side,
                                    'rank'=> $mySelection['rankName']
                                    );
        return $mySelection['member'];
    }
    private function extractTempDetails($myqry,$side='R'){
        if($side=='L'){
            $mySelection= $myqry->andWhere(['lft'=>1])->one();
        }elseif($side=='R'){
            $mySelection= $myqry->andWhere(['rgt'=>1])->one();
        }
        $this->tempList[$mySelection['member']]=array('pid'=>$mySelection['member'],
                                    'memberNo'=>$mySelection['membershipNo'],
                                    'memberName'=>$mySelection['surname'],
                                    'parentNo'=>$mySelection['parent'],
                                    'side'=> $side,
                                    'rank'=> $mySelection['rankName']
                                    );
        return $mySelection['member'];
    }
}
