<?php

namespace common\components;

use Yii;
use yii\base\Component;
use\yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use \common\models\People;

//use backend\modules\geneology\models\Sponsorship;
/**
 * Description of MemberDetails
 *
 * @author Apache1
 */
class MemberDetails extends Component {

    public $nextlft;
    public $nextParent;
    public $nextLvl;
    public $nextPosition;
    public $msg;

    /**
     * 
     * @return type array with people.id of members
     */
    public function getMembersList() {
        $arr = (new \yii\db\Query())
                ->select(['member'])
                ->from('sponsorship')
                ->all();
        return ArrayHelper::getColumn($arr, 'member');
    }

    public function getProspectiveMembers() {
        $arr = (new \yii\db\Query())
                ->select(['id'])
                ->from('people')
                ->where(['NOT IN', 'id', $this->getMembersList()])
                ->all();
        return ArrayHelper::getColumn($arr, 'id');
    }

    /**
     * 
     * Create a random memberNo
     * @return type
     */
    public Function getNextMemberNo() {
        //$val = Yii::$app->db->createCommand('select MAX(membershipNo) From sponsorship')->queryScalar();
        $val = 1000000;
        $unique = false;
        do {
            $val = random_int(1000001, 9999999);
            if ($this->isUnique($val))
                $unique = true;
        }while ($unique == false);
        return $val;
    }

    /**
     * confirms  $anInt is unique in sponsorship table
     * 
     * @param type $anInt
     * @return boolean
     */
    Private function isUnique($anInt) {
        $aVal = (new \yii\db\Query())
                ->select(['count(*)'])
                ->from('sponsorship')
                ->where(['membershipNo' => $anInt])
                ->count();
        if ($aVal > 0)
            return false;
        return true;
    }

    public function getCurrentMemberDetails($optn) {
        $myqry = (new \yii\db\Query())
                ->select('*')
                ->from('people p')
                ->leftJoin('user u', 'u.peopleId=p.id')
                ->leftJoin('sponsorship s', 's.member=p.id')
                ->leftJoin('statuses t', 't.id=s.status')
                ->where(['u.id' => Yii::$app->user->id])
                ->one();
        switch ($optn) {
            case 1:// Member Name
                return $myqry['firstName'] . ' ' . $myqry['surname'];
                break;
            case 2://Member No
                return $myqry['membershipNo'];
            case 3:// Status Name
                return $myqry['Status'];
            case 4:
                return $myqry['sponsor'];
            case 5:
                return $myqry['parent'];
            default:
                return 'try another option';
        }
    }

    /**
     * Gets sponsorMemberNo saved upon registration
     * @return type int  = sponsorNo
     */
    public function getSponsorNo() {
        $myqry = (new \yii\db\Query())
                ->select('*')
                ->from('tempsponsor')
                ->where(['member' => Yii::$app->user->id])
                ->one();
        return $myqry['sponsor'];
    }

    /**
     * creates a new member in sponsorship table from details in tempSponsor
     * @param type $peopleId
     */
    public function addMember($peopleId) {
        $session = Yii::$app->session;
        try {
            //confirm the parent
            //$parent = $this->confirmParent($this->getMemberPartsUsingMemberNo($sponsorMemberNo));
            //add 1 to the parent level
            //$level = $this->getMemberPartsUsingPeopleId($parent,4)+1;
            $tempSponsorNo = $this->getTempSponsorDetails($peopleId); //sponsor's membershipNo
            $tempSponsor = $this->getMemberPartsUsingMemberNo($tempSponsorNo);
            $parent = $this->getNextParent($tempSponsor);
            $db = Yii::$app->db;
            $db->createCommand()->insert('sponsorship', [
                        'member' => $peopleId,
                        'membershipNo' => $this->getNextMemberNo(),
                        'sponsor' => $tempSponsor,
                        'parent' => $parent,
                        'lft' => $this->insertChild($parent),
                        'rgt' => $this->toggle($this->nextlft),
                        'status' => 1, //Inactive
                        'Rank' => 1,
                        'level' => $this->nextLvl,
                        'RecordDate' => date('Y-m-d H:i:s'),
                        'RecordBy' => Yii::$app->user->id,
                    ])
                    ->execute();
        } catch (\yii\db\Exception $e) {
            $session->setFlash('error', 'Unable to save sponsor details: ' . $e->getMessage());
        }
    }

    public function getTempSponsorDetails($userid, $optn = 1) {
        $myqry = (new \yii\db\Query())
                ->select('*')
                ->from('tempsponsor')
                ->where(['member' => $userid])
                ->one();
        switch ($optn) {
            case 1://sponsor
                $retval = $myqry['sponsor'];
                break;
            case 2:
                $retval = $myqry['lft'];
                break;
            case 3:
                $retval = $myqry['parent'];
                break;
            default :
                $retval = 0;
        }
        return $retval;
    }
    public function getTempSponsorDetails2($memberid, $optn = 1) {
        $myqry = (new \yii\db\Query())
                ->select('*')
                ->from('tempsponsor t')
                ->leftJoin('user u','t.member=u.id')
                //->leftJoin(people p)
                ->where(['u.peopleId' => $memberid])
                ->one();
        switch ($optn) {
            case 1://sponsor
                $retval = $myqry['sponsor'];
                break;
            case 2:
                $retval = $myqry['lft'];
                break;
            case 3:
                $retval = $myqry['parent'];
                break;
            default :
                $retval = 0;
        }
        return $retval;
    }

    /**
     * 
     * @param type $peopleId
     * @param type $optn
     * @return int
     */
    public function getMemberPartsUsingPeopleId($peopleId, $optn = 1) {
        $msg = '<strong>getMemberPartsUsingPeopleId </strong><br>';
        $myqry = (new \yii\db\Query())
                ->select('*')
                ->from('sponsorship s')
                ->leftJoin('people p', 'p.id = s.member')
                ->leftJoin('ranks r', 'r.id = s.rank')
                ->where(['s.member' => $peopleId])
                ->one();
        switch ($optn) {
            case 1:
                $msg .= 'membershipNo';
                return $myqry['membershipNo'];

            case 2://parent peopleId
                $msg .= 'parent: ' . $myqry['parent'];
                return $myqry['parent'];

            case 3://sponsor peopleId
                $msg .= 'sponsor: ' . $myqry['sponsor'];
                return $myqry['sponsor'];

            case 4://member level
                $msg .= 'level: ' . $myqry['level'];
                return $myqry['level'];

            case 5://member rank
                $msg .= 'rankName: ' . $myqry['rankName'];
                return $myqry['rankName'];
            case 6:
                $msg .= 'firstName: ' . $myqry['firstName'];
                return $myqry['firstName'] . ' ' . $myqry['surname'];
            case 7://rgt
                $msg .= 'rgt: ' . $myqry['rgt'];
                return $myqry['rgt'];
            case 8://lft
                $msg .= 'lft: ' . $myqry['lft'];
                return $myqry['lft'];
            case 9:// get position
                $msg .= 'position: ' . $myqry['position'];
                return $myqry['position'];
            default:
                return 0;
        }
    }

    public function getMemberPartsUsingMemberNo($memberNo, $optn = 1) {
        $myqry = (new \yii\db\Query())
                ->select('*')
                ->from('sponsorship s')
                ->leftJoin('ranks r', 'r.id=s.rank')
                ->where(['membershipNo' => $memberNo])
                ->one();
        switch ($optn) {
            case 1:// people.id 
                return $myqry['member'];

            case 2://parent peopleId
                return $myqry['parent'];

            case 3://sponsor peopleId
                return $myqry['sponsor'];

            case 4://member level
                return $myqry['level'];

            case 5://member rank
                return $myqry['rankName'];

            default:
                return 0;
        }
    }

    public function confirmParent($sponsor/*             * typeOf people.id */) {
        //select level+1 get no of children parented by sponsor
        //if children are more than 6 [Allowed cildren No Per Level Per Parent] then go to next lower level
        //else return next child sponsored at this level
        return $sponsor;
    }

    private function toggle($mybool) {
        return $mybool == 0 ? 1 : 0;
    }

    public function isRegistered($memberId, $optn = 1) {
        $mycount = (new \yii\db\Query())
                ->select('*')
                ->from('inpayments i')
                ->where(['member' => $memberId, 'ptype' => 1, 'confirmed' => 1]);
        switch ($optn) {
            case 1:
                return $mycount->count();
            case 2: //if approved 1
                return $this->toggle($mycount->andWhere(['confirmDate' =>  Null])->count());
            default:
                return -1;
        }
    }

    public function isAMember($memberId) {
        $myqry = (new \yii\db\Query())
                ->select('*')
                ->from('sponsorship ')
                ->where(['member' => $memberId])
                ->one();
        return $myqry['membershipNo'];
    }

    public function getNextParent($sponsor/* from peopleId */) {
        //check if sponsor has left and right
        //get all parents
        $msg = '<strong>GetNextParent</strong><br>';
        if(($lft= $this->getMemberPartsUsingPeopleId($sponsor,8)+1)==($rgt= $this->getMemberPartsUsingPeopleId($sponsor,7))){
            $this->nextParent= $sponsor;
            $this->nextPosition = $this->getPosition();
            
            return $msg;
        }else{
            $allLeafIds= $this->getAllLeaves($sponsor);
            $allParentIds= $this->getAllLeaves($sponsor,2);
            $found = false;
            for($i=0;$i<count($allParentIds);$i++){
                if($this->getChildren($allParentIds[$i])<2){//count of children is less than 2
                    $this->nextParent= $allParentIds[$i];
                    $this->nextPosition = $this->getPosition();
                    $found=true;
                    break;
                }
            }
            if(!$found){
                $this->nextParent= /*is_array($allLeafIds)? $allLeafIds[0]:****/$allLeafIds;
                $this->nextPosition = $this->getPosition();
            }   
        }
        
        return $msg;
    }
    
   /**
    * 
    * @param type $sponsor
    * @param type $parent
    * @return type
    */ 
   public function getParent($sponsor, $parent=0/* >0 if parent specified*/) {
       $sponsorChildCnt= $this->getChildren($sponsor);
       
       if($parent==0 && $sponsorChildCnt==0 ){
           $retval=$this->nextParent=$sponsor;
           //$this->nextLvl = $this->getMemberPartsUsingPeopleId($sponsor,4)+1;
           //$this->nextlft=$this->getMemberPartsUsingPeopleId($sponsor,7);//get rgt od sponsor
           return;
       }elseif($parent==0/*Parent not specified*/&& $sponsorChildCnt>0 ){
           //look for suitable parent using sponsor
           $retval=$this->nextParent=$this->searchParent($sponsor);
       }elseif($parent>0){
           //confirm suitabilty of parent
           //parent must be in sponsors tree and have less than two children
           if(!$this->confirmSuitableParent($sponsor,$parent)){
           //if not suitable thenlook for suitable parent using parent
                $retval=$this->nextParent=$this->searchParent($parent);
           }else{
                $retval=$this->nextParent=$parent;
           }
       }
       return $retval;
   }
   
   /**
    * 
    * @param type $parent
    * @return type
    */
   protected function searchParent($parent){
       //get all leaves + parents in date order
       $arr1= $this->getAllLeaves($parent);
       $arr2 = $this->getAllLeaves($parent,2);
       $arr3 = ArrayHelper::merge($arr2, $arr1);
       $arr3 = array_unique($arr3);
      
       //get first that has  < two children
       foreach($arr3 as $myparent){
           if($this->getChildren($parent)<2){
               
               return $parent;
           }
       }
       return -1;//if parent not found.
   }
   
   /**
    * 
    * @param type $sponsor
    * @param type $parent
    * @return boolean if parent is suitable return true; else false
    */
   public function confirmSuitableParent($sponsor,$parent){
       $retval=true;
       if($this->getChildren($parent)>1){//parent has less than two children
           $retval==false;
       }elseif(!ArrayHelper::isIn ($parent,$this->getAllLeaves($sponsor,3)/*array of all members unser sponsor*/)){
           $retval==false;
       }
       return $retval;
   }
   
   public function getNextLft($parent){
       //count children for parent
       $childrenCnt =$this->getChildren($parent);
       //if childCount=0 or child is on left take right take $side
       if($childrenCnt==0 ||$this->getChildren($parent, 2)==1){
           $retval = $this->nextlft = $this->getMemberPartsUsingPeopleId($parent,7);
       }else{//existing child is on right 
           $retval = $this->nextlft =  $this->getChildren($parent, 3);
       }
       return $retval;
   }
   
    public function checkChildrenNo($parent) {
        $myqry = (new \yii\db\Query())
                ->select('*')
                ->from('sponsorship ')
                ->where(['parent' => $parent]);
        if ($myqry->count() == 0) {
            // select left or right randomly
            $this->nextLft = random_int(0, 1);

            $this->nextParent = $parent;
            $isAllocated = 1;
        } elseif ($myqry->count() == 1) {//count is 1
            $mychildren = $myqry->one();
            $this->nextlft = $this->toggle($mychildren['lft']);
            $this->nextParent = $parent;
            $isAllocated = 1;
        } else {//count is 2
            $isAllocated = 0;
        }
        return $isAllocated;
    }

    public function getParentParticulars($parent, $optn = 1) {
        //add 4to the left and anything greter than that in lft and rgt columns
        $myqry = (new \yii\db\Query())
                ->select('*')
                ->from('sponsorship ')
                ->where(['parent' => $parent]);
        switch ($optn) {
            case 1://count of children
                return $myqry->count();
            case 2://this is parent member no
                $mymember = $myqry->one();
                return $mymember['member'];
            case 3://get rgt
                $mymember = $myqry->one();
                return $mymember['rgt'];
            case 4://get lft
                $mymember = $myqry->one();
                return $mymember['lft'];
            case 5://get rgt
                $mymember = $myqry->one();
                return $mymember['level'];
            default:
                return -1;
        }
    }

    public function addChild($myId, $parent, $sponsor, $side = 1/* 0=root;1=letf*; 2=right */) {
        try {
            $msg = '<strong>AddChild</strong><br>';
            //$msg .= 'MemberId: ' . $myId . '; Parent: ' . $parent . '; Sponsor: ' . $sponsor . '; Side: ' . $side . '<br>';
            //if first
            if ($this->confirmEmptySponsor() == 0) {
                $this->insertChild($myId, $myId, 1, 0);
            } else {
                $this->adjustAndInsertChild2($myId, $parent, $sponsor,$side );
                //confirm that parent is not full
                //$childrenNo = $this->getChildren($parent);
               /* if ($childrenNo == 0 && $side == 1) {// No Existing  children 
                    $msg .= 'No of children: 0; Child to be put on Left' . '<br>';

                    $msg .= $this->adjustAndInsertChild($myId, $parent, $sponsor,$side , 1/* 1=yes 0=no );
                } elseif ($childrenNo == 0 && $side == 2) {
                    $msg .= 'No of children: 0; Child to be put on Right'.'<br>';
                    $msg .= $this->adjustAndInsertChild($myId, $parent, $sponsor,$side , 0/* 1=yes 0=no );
                } elseif ($childrenNo == 1 && $this->getChildren($parent, 2) == 1) {
                    //confirm child is left
                    $msg .= 'No of children: 1; Existing child is on left' . '<br>';
                    $msg .= $this->adjustAndInsertChild($myId, $parent, $sponsor,2 , 1/* 1=yes 0=no );
                } elseif ($childrenNo == 1 && $this->getChildren($parent, 2) == 2) { //existing child is right
                    //get right child's left value
                    $msg .= 'No of children: 1; Existing child is on Right' . '<br>';
                    $msg .= $this->adjustAndInsertChild($myId, $parent, $sponsor,1 , 0/* 1=yes 0=no );
                } else {//already has two children
                    $msg .='Two children aready existing';
                }*/
                
            }
            return $msg;
        } catch (\Exception $e) {
            $msg .= 'Unable to save to sponsorship (3 addChild): ' . $e->getMessage();
            throw $e;
            return $msg;
        } catch (\Throwable $e) {
            $msg .= 'Unable to save to sponsorship (4 addChild): ' . $e->getMessage();
            throw $e;
            return $msg;
        }
        return $msg;
    }

    public function getChildren($parent, $item = 1) {
        $myqry = (new \yii\db\Query())
                ->select('*')
                ->from('sponsorship ')
                ->where(['parent' => $parent])
                ->andWhere(['member' => !$parent]);
        switch ($item) {
            case 1:// count of children
                return $myqry->count();
            case 2:// position of 1st child
                $childparts = $myqry->one();
                return $childparts['position'];
            case 3: // left of existing child
                $childparts = $myqry->one();
                return $childparts['lft'];
            case 4: // rgt of existing child
                $childparts = $myqry->one();
                return $childparts['rgt'];
            default:
                return -1;
        }
    }

    /* private function confirmSide($member){
      $myqry = (new \yii\db\Query())
      ->select('*')
      ->from('sponsorship ')
      ->where(['member'=>$member])
      ->one();
      return $myqry['position'];

      } */

    public function adjustSponsorship($lft) {
        $msg = '<strong>AdjustSponsorship</strong><br>';
        $db = Yii::$app->db;
        $transaction = $db->beginTransaction();
        try {
            //add  2 to values in lft and rgt >= $lft
            $db->createCommand('UPDATE sponsorship SET lft = lft+2 where lft>=:lft')->bindParam(':lft', $lft)->execute();
            //$transaction->commit();
            $db->createCommand('UPDATE sponsorship SET rgt = rgt+2 where rgt>=:rgt')->bindParam(':rgt', $lft)->execute();
            /*$db->createCommand()->update('sponsorship', [
                'rgt' => 'rgt+ 2',
                    ], 'rgt>=:rgt', [':rgt' => $lft])->execute();*/
            $transaction->commit();
            $msg .= 'Lft and Rht successfully adjusted <br>';
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
            return $e->message;
        } catch (\Throwable $e) {
            $transaction->rollBack();
            throw $e;
            return $e->message;
        }
        return $msg;
    }

    public function insertChild($peopleId, $parent, $sponsor, $lft, $position = 1 /* root=0;left=1;right=2 */) {
        $msg = '<strong>insertChild</strong><br>';
        $msg .= 'MemberId: ' . $peopleId . '; ';
        //$msg .= 'Parent: ' . $parent . '; ';
        $msg .= 'Sponsor: ' . $sponsor . '; ';
        $msg .= 'Lft: ' . $lft . '; ';
        $msg .= 'Position: ' . $position . '<br>';
        $session = Yii::$app->session;

        //$parent = $this->getNextParent($tempSponsor);
        $db = Yii::$app->db;
        try {
            $db->createCommand()->insert('sponsorship', [
                        'member' => $peopleId,
                        'membershipNo' => $this->getNextMemberNo(),
                        'sponsor' => $sponsor,
                        'parent' => $parent,
                        'lft' => $lft,
                        'rgt' => $lft + 1,
                        'position' => $position,
                        'status' => 2, //Active
                        'Rank' => 1,
                        'level' => $this->getMemberPartsUsingPeopleId($parent, 4) + 1,
                        'RecordDate' => date('Y-m-d H:i:s'),
                        'RecordBy' => Yii::$app->user->id,
                    ])
                    ->execute();
            //$session . setFlash('success', 'Sponsorship successfully saved');
            $msg .= 'Sponsorship successfully saved';
            return $msg;
        } catch (Exception $e) {
            $msg .= 'Unable to save to sponsorship (1): ' . $e->getMessage();
            throw $e;
            return $msg;
        } catch (\Throwable $e) {
            $msg .= 'Unable to save to sponsorship (2): ' . $e->getMessage();
            throw $e;
            return $msg;
        }
    }

    public function confirmEmptySponsor() {
        return (new \yii\db\Query())
                        ->select('*')
                        ->from('sponsorship ')
                        ->where('rgt>lft')//ensures admin has no and is not counted
                        ->count();
    }

    public function getPosition() {
        $msg = '<strong>GetPosition</strong><br>';
        if ($this->getChildren($this->nextParent) == 1) {
            $this->nextPosition = $this->getChildren($this->nextParent, 2) == 1 ? 2 : 1;
            $msg .= 'Side : ' . $this->nextPosition . '<br>';
        } elseif ($this->getChildren($this->nextParent) == 0) {
            $this->nextPosition = 1;
            $msg .= 'Side : ' . $this->nextPosition . '<br>';
        } else {
            $this->nextPosition = -1;
            $msg .= 'Side : ' . $this->nextPosition . ' Parent is full<br>';
        }
        return $msg;
    }
    
    public function getNextPosition($parent,$side) {
        $this->msg = '<strong>GetNextPosition</strong><br>';
        if ($this->getChildren($this->nextParent) == 1) {
            $retval = $this->nextPosition = $this->getChildren($parent, 2) == 1 ? 2 : 1;
            $this->msg .= 'Side : ' . $this->nextPosition . '<br>';
        } elseif ($this->getChildren($parent) == 0) {//count of children=0
            $retval = $this->nextPosition = $side==1 ? 1 : 2;
            $this->msg .= 'Side : ' . $this->nextPosition . '<br>';
        } else {
            $retval =$this->nextPosition = -1;
            $this->msg .= 'Side : ' . $this->nextPosition . ' Parent is full<br>';
        }
        return $retval;
    }

    public function adjustAndInsertChild($myId, $parent, $sponsor,$position ,$fromParent/*1=yes; 0=no*/) {
        $msg= '<strong>AdjustAndInsertChild</strong><br>';
        
        switch ($fromParent) {
            case 1:
                $childLft = $this->getMemberPartsUsingPeopleId($parent, 7);
                $this->nextlft = $childLft;
                $msg .= 'Lft : ' . $childLft . '<br>';
                break;
            case 0:
                $msg .= 'Side: Right' . '<br>';
                //get right child's left value
                $childLft = $this->getChildren($parent, 3);
                $this->nextlft = $childLft;
                $msg .= 'Lft : ' . $childLft . '<br>';
                break;
        }
        //update sponsors lft and rgt >= childlft
        $msg .= $this->adjustSponsorship($this->nextlft);
        // insert the child
        $msg .= $this->insertChild($myId, $parent, $sponsor, $this->nextlft, $position);
        return $msg;
    }
    public function adjustAndInsertChild2($myId, $parent, $sponsor,$position ) {
        $msg= '<strong>AdjustAndInsertChild2</strong><br>';
        
        $nextLeft = $this->getNextLft($parent);
        //update sponsors lft and rgt >= childlft
        $msg .= $this->adjustSponsorship($nextLeft);
        // insert the child
        $msg .= $this->insertChild($myId, $parent, $sponsor, $nextLeft, $position);
        return $msg;
    }
    public function getAllLeaves($memberId,$optn=1)
    {
        $lftVal = $this->getMemberPartsUsingPeopleId($memberId, 8);
        $rgtVal = $this->getMemberPartsUsingPeopleId($memberId, 7);
        $myqry = (new \yii\db\Query())->select('*')->from('sponsorship ')
                ->where('lft>=:lft', [':lft' => $lftVal])
                ->andWhere('rgt<=:rgt', [':rgt' => $rgtVal])
                
                ->orderBy('recordDate');
        switch($optn){
            case 1:// Array of all leaf memberIds
                $allLeaves = $myqry->andwhere('rgt=lft+1')->all();
                return ArrayHelper::getColumn($allLeaves, 'member');
            case 2: // Array of all parent memberIds
                $allparents = $myqry->andwhere('rgt=lft+1')->andWhere('member!=:member',[':member'=>$memberId])->all();
                return ArrayHelper::getColumn($allparents, 'parent');
            case 3:// all nodes in sponsors realm
                $wholerealm = $myqry->all();
                return ArrayHelper::getColumn($wholerealm, 'member');
        }
    }
    public function getTree($memberId){
        
        $lftVal = $this->getMemberPartsUsingPeopleId($memberId, 8);
        $rgtVal = $this->getMemberPartsUsingPeopleId($memberId, 7);
        $myqry = (new \yii\db\Query())->select('*')->from('sponsorship s')
                ->leftJoin('people p','s.member=p.id')
                ->where('lft>=:lft', [':lft' => $lftVal])
                ->andWhere('rgt<=:rgt', [':rgt' => $rgtVal])
                ->orderBy('lft')
                ->all();
        $stack=array();
        $level=$mylvl=0;//mylevel keeps track of number of levels
        $treeVal='';
            foreach($myqry as $memberVals){
                $side = $this->getMemberPartsUsingPeopleId($memberVals['member'],9);
                $childCnt = $this->getParentParticulars($memberVals['member']);
                if($mylvl>=Yii::$app->params['maxLevels']){
                    continue;//next loop
                }elseif($memberVals['level']>$level){
                    $treeVal .= "<ul>";
                    array_push($stack,"</ul>");
                    $this->addTreeVals($treeVal,$stack,$memberVals,$memberId);
                    //$this->addOpen($treeVal,$stack,$memberVals,$memberId);
                    
                    $level=$memberVals['level'];
                    $mylvl++;
                }elseif($memberVals['level']==$level){
                    $treeVal.=array_pop($stack);//close i.e </li>
                    
                    $this->addTreeVals($treeVal,$stack,$memberVals,$memberId);
                }elseif($memberVals['level']<$level){
                    $treeVal.=array_pop($stack);//close i.e </li>
                    $treeVal.=array_pop($stack);//close i.e </ul>
                    $this->addTreeVals($treeVal,$stack,$memberVals,$memberId);
                    $level=$memberVals['level'];
                    $mylvl--;
                }
                
            }
            while(count($stack)>0){
                $treeVal.=array_pop($stack);
            }
        return $treeVal;  
    }
    public function addTreeVals(&$treeVal,&$stack,&$memberVals,$memberId) {
        
        $treeVal .= "<li>";
                    array_push($stack,"</li>");
        $treeVal .= "<span class='tf-nc treenode'>";
        //$treeVal .= "<a href='". htmlspecialchars( Url::toRoute('/site/join',['parent'=>$memberVals['membershipNo'],'sponsor'=>$this->getMemberPartsUsingPeopleId($memberId),'lft'=>1]))."'>";
        $treeVal .= $memberVals['status']==2? "<img src='images/Person14.jpg'":"<img src='images/Person5.jpg'";
        $treeVal .= " alt = '".$memberVals["surname"]." logo' width ='32px'  height ='32px'"
                ."  title='".
                //$memberVals['firstName']." ".$memberVals['surname']. "'><br>".$memberVals['membershipNo']."<br>Children: ".$this->getParentParticulars($memberVals['member'])."<br>side: ".$this->getMemberPartsUsingPeopleId($memberVals['member'],9)."</span>\n";
                $memberVals['firstName']." ".$memberVals['surname']. "'><br>".$memberVals['membershipNo']."</span>\n";
        //$treeVal .=array_pop($stack);
    }
    public function addOpen(&$treeVal,&$stack,&$memberVals,$memberId,$side) {
        $treeVal .= "<li>";
                    array_push($stack,"</li>");
        $treeVal .= "<span class='tf-nc treenode'>";
        $treeVal .= "<a href='".  Url::toRoute('/site/join')."/".$memberVals['membershipNo']."/".$this->getMemberPartsUsingPeopleId($memberId)."/".$side."'>";
        $treeVal .= "<img src='images/Person4.jpg'";
        $treeVal .= " alt = 'open logo' width ='32px'  height ='32px'"
                ."  title='open'><br>--Open--</span></li>";
    }
    public function confirmOpenChild(&$treeVal,&$stack,&$memberVals,$memberId){
        $side = $this->getMemberPartsUsingPeopleId($memberVals['member'],9);
        $childCnt = $this->getParentParticulars($memberVals['member']);
        if($childCnt==1 && $side=1){
           
                $this->addTreeVals($treeVal,$stack,$memberVals,$memberId);
                $this->addOpen($treeVal,$stack,$memberVals,$memberId);
                //$treeVal.=array_pop($stack);
        }elseif($childCnt==1 && $side==2){
                $this->addOpen($treeVal,$stack,$memberVals,$memberId);
                //$treeVal.=array_pop($stack);
                $this->addTreeVals($treeVal,$stack,$memberVals,$memberId);
                       
        } elseif($childCnt==2 ){
            $this->addTreeVals($treeVal,$stack,$memberVals,$memberId);
        } elseif($childCnt==0){
            $this->addTreeVals($treeVal,$stack,$memberVals,$memberId);
            $treeVal .= "<ul>";
            array_push($stack,"</ul>");
            $this->addOpen($treeVal,$stack,$memberVals,$memberId);
                //$treeVal.=array_pop($stack);
            $this->addOpen($treeVal,$stack,$memberVals,$memberId);
                //$treeVal.=array_pop($stack);
            array_pop($stack);
        }

    }
    
    public function getTree2($memberId){
        
        $lftVal = $this->getMemberPartsUsingPeopleId($memberId, 8);
        $rgtVal = $this->getMemberPartsUsingPeopleId($memberId, 7);
        $myqry = (new \yii\db\Query())->select('*')->from('sponsorship s')
                ->leftJoin('people p','s.member=p.id')
                ->where('lft>=:lft', [':lft' => $lftVal])
                ->andWhere('rgt<=:rgt', [':rgt' => $rgtVal])
                ->orderBy('lft')
                ->all();
        $stack=array();
        $level=$mylvl=0;//mylevel keeps track of number of levels
        $treeVal='';
            foreach($myqry as $memberVals){
                $side = $this->getMemberPartsUsingPeopleId($memberVals['member'],9);
                $childCnt = $this->getParentParticulars($memberVals['member']);
                if($mylvl>=Yii::$app->params['maxLevels']){
                    continue;//next loop
                }elseif($memberVals['level']>$level){
                    $treeVal .= "<ul>";
                    array_push($stack,"</ul>");
                    if($mylvl>0 && $childCnt==1 && $side==1 ){
                        $this->addTreeVals($treeVal,$stack,$memberVals,$memberId);
                        $this->addOpen($treeVal,$stack,$memberVals,$memberId,2);
                    }elseif($mylvl>0 && $childCnt==1 && $side==2 ){
                        $this->addOpen($treeVal,$stack,$memberVals,$memberId,1);
                        $this->addTreeVals($treeVal,$stack,$memberVals,$memberId);
 
                    }else{
                    $this->addTreeVals($treeVal,$stack,$memberVals,$memberId);
                    }
                    //$this->addOpen($treeVal,$stack,$memberVals,$memberId);
                    
                    $level=$memberVals['level'];
                    $mylvl++;
                }elseif($memberVals['level']==$level){
                    $treeVal.=array_pop($stack);//close i.e </li>
                    
                    $this->addTreeVals($treeVal,$stack,$memberVals,$memberId);
                }elseif($memberVals['level']<$level){
                    $treeVal.=array_pop($stack);//close i.e </li>
                    $treeVal.=array_pop($stack);//close i.e </ul>
                    $this->addTreeVals($treeVal,$stack,$memberVals,$memberId);
                    $level=$memberVals['level'];
                    $mylvl--;
                }
                
            }
            while(count($stack)>0){
                $treeVal.=array_pop($stack);
            }
        return $treeVal;  
    }
    
    public function showArray($memberId)
    {
        $lftVal = $this->getMemberPartsUsingPeopleId($memberId, 8);
        $rgtVal = $this->getMemberPartsUsingPeopleId($memberId, 7);
        $myqry = (new \yii\db\Query())->select('*')->from('sponsorship s')
                ->leftJoin('people p','s.member=p.id')
                ->where('lft>=:lft', [':lft' => $lftVal])
                ->andWhere('rgt<=:rgt', [':rgt' => $rgtVal])
                ->orderBy('lft')
                ->all();
        $outArr=array(array());
        $level=$mylvl=0;//mylevel keeps track of number of levels
        $treeVal='';
        foreach($myqry as $i=>$arr){
            $side = $this->getMemberPartsUsingPeopleId($arr['member'],9);
            $childCnt = $this->getParentParticulars($arr['member']);
            if($i==0){
               $this->transferArr($outArr,$arr,$i); 
            }elseif($childCnt==1 && $side==1 ){
               $this->transferArr($outArr,$arr,$i); 
               $this->addOpenArr($outArr,$arr,$i);
            }elseif($childCnt==1 && $side==2 ){
               $this->addOpenArr($outArr,$arr,$i);
               $this->transferArr($outArr,$arr,$i); 
               
            }
        }
        return $treeVal;
    }
    public function transferArr(&$outArr,&$arr,$i){
            $outArr[$i]['member'] = $arr['member'];
            $outArr[$i]['memberNo'] = $arr['membershipNo'];
            $outArr[$i]['sponsor'] = $arr['sponsor'];
            $outArr[$i]['parent'] = $arr['parent'];
            $outArr[$i]['position'] = $arr['position'];
            $outArr[$i]['level'] = $i;
            $outArr[$i]['fullName'] = $arr['firstName']." ".$arr['surname'];
            $outArr[$i]['shortName'] = $arr['firstName']." ".$arr['surname'];
            $outArr[$i]['status'] = $arr['status'];
            $outArr[$i]['lft'] = $arr['lft'];
            $outArr[$i]['rgt'] = $arr['rgt'];

    }
    public function addOpenArr(&$outArr,&$arr,$i){
            $outArr[$i]['member'] = -1;
            $outArr[$i]['memberNo'] = 0;
            $outArr[$i]['sponsor'] = $arr['sponsor'];
            $outArr[$i]['parent'] = $arr['parent'];
            $outArr[$i]['position'] = $arr['position']==1?2:1;
            $outArr[$i]['level'] = $i;
            $outArr[$i]['fullName'] = "Position Open";
            $outArr[$i]['status'] = 3;
            $outArr[$i]['lft'] = 0;
            $outArr[$i]['rgt'] = 0;

    }
   public function updPointsTable($memberId/*id of sponsored members */){
        
        $sponsor = $this->getMemberPartsUsingPeopleId($memberId, 3);
       
        $db->createCommand()->insert('tblpoints', [
                        
                        'sponsor' => $sponsor,
                        'memberFrom' => $memberId,
                        'trxType'=>$this->getPackageDetails($memberId),
                        'RecordDate' => date('Y-m-d H:i:s'),
                        'RecordBy' => Yii::$app->user->id,
                    ])
                    ->execute();
   }
   
   public function getPackageDetails($memberId,$optn=1){
       $myqry = (new \yii\db\Query())
               ->select('*')
               ->from('membershiphistory m')
               ->where(['memberId'=>$memberId, 'dateEnd'=>null])
               ->one();
       switch($optn){
           case 1://Package
               return $myqry['packId'];
           case 2://status
               return $myqry['status'];
           case 3://status
               return $myqry['status'];
               
       }
   }
}
