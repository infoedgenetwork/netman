<?php


namespace common\components;

use Yii;
use yii\base\Component;
use\yii\helpers\ArrayHelper;

use \common\models\People;
//use backend\modules\geneology\models\Sponsorship;
/**
 * Description of MemberDetails
 *
 * @author Apache1
 */
class MemberDetails extends Component{
    
    public $nextlft;
    public $nextParent;
    public $nextLvl;
    /**
     * 
     * @return type array with people.id of members
     */
    public function getMembersList(){
        $arr = (new \yii\db\Query())
                ->select(['member'])
                ->from('sponsorship')
                ->all();
        return ArrayHelper::getColumn($arr, 'member');
    }
    public function getProspectiveMembers()
    {
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
    public Function getNextMemberNo()
    {
         //$val = Yii::$app->db->createCommand('select MAX(membershipNo) From sponsorship')->queryScalar();
        $val=1000000;
        $unique = false;
        do{
            $val = random_int(1000001, 9999999);
            if($this->isUnique($val))$unique=true;
        }while($unique == false);
         return  $val;
    }
    /**
     * confirms  $anInt is unique in sponsorship table
     * 
     * @param type $anInt
     * @return boolean
     */
    Private function isUnique($anInt)
    {
        $aVal=(new \yii\db\Query())
                ->select(['count(*)'])
                ->from('sponsorship')
                ->where(['membershipNo'=>$anInt])
                ->count();
        if($aVal>0)return false;
        return true;
    }
    
    public function getCurrentMemberDetails($optn){
         $myqry = (new \yii\db\Query())
                ->select('*')
                ->from('people p')
                ->leftJoin('user u','u.peopleId=p.id')
                 ->leftJoin('sponsorship s','s.member=p.id')
                 ->leftJoin('statuses t','t.id=s.status')
                ->where(['u.id'=>Yii::$app->user->id])
                ->one();
         switch($optn){
             case 1:// Member Name
                return $myqry['firstName'].' '.$myqry['surname'];
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
    public function getSponsorNo()
    {
        $myqry = (new \yii\db\Query())
                ->select('*')
                ->from('tempsponsor')
                ->where(['member'=>Yii::$app->user->id])
                ->one();
        return $myqry['sponsor'];
    }
    /**
     * creates a new member in sponsorship table from details in tempSponsor
     * @param type $peopleId
     */
    public function addMember($peopleId)
    {
        $session = Yii::$app->session;
        try{
        //confirm the parent
        //$parent = $this->confirmParent($this->getMemberPartsUsingMemberNo($sponsorMemberNo));
        //add 1 to the parent level
        //$level = $this->getMemberPartsUsingPeopleId($parent,4)+1;
        $tempSponsorNo = $this->getTempSponsorDetails($peopleId);//sponsor's membershipNo
        $tempSponsor = $this->getMemberPartsUsingMemberNo($tempSponsorNo);
        $parent = $this->getNextParent($tempSponsor);
        $db = Yii::$app->db;
        $db->createCommand()->insert('sponsorship',[
            'member'=> $peopleId,
            'membershipNo' => $this->getNextMemberNo(),
            'sponsor' => $tempSponsor ,
            'parent' =>$parent ,
            'lft'=> $this->insertChild($parent),
            'rgt'=> $this->toggle($this->nextlft),
            'status' => 1,//Inactive
            'Rank' => 1,
            'level' => $this->nextLvl,
            'RecordDate' => date('Y-m-d H:i:s'),
            'RecordBy' => Yii::$app->user->id, 
        ])
                
                ->execute();
        } catch (\yii\db\Exception $e){
            $session->setFlash('error', 'Unable to save sponsor details: '.$e->getMessage());
        }
    }
    
    private function getTempSponsorDetails($peopleId,$optn=1)
    {
        $myqry = (new \yii\db\Query())
                ->select('*')
                ->from('tempsponsor')
                ->where(['member'=>$peopleId])
                ->one();
        switch ($optn){
            case 1://sponsor
                $retval = $myqry['sponsor'];
                break;
            case 2:
                $retval = $myqry['lft'];
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
    public function getMemberPartsUsingPeopleId($peopleId,$optn=1)
    {
        $myqry = (new \yii\db\Query())
                ->select('*')
                ->from('sponsorship s')
                ->leftJoin('people p','p.id= member')
                ->leftJoin('ranks r', 'r.id=s.rank')
                ->where(['member'=> $peopleId])
                ->one();
        switch($optn){
            case 1:
                return $myqry['membershipNo'];
                
            case 2://parent peopleId
                return $myqry['parent'];
                
            case 3://sponsor peopleId
                return $myqry['sponsor'];
                
            case 4://member level
                return $myqry['level'];
                
            case 5://member rank
                return $myqry['rankName'];
            case 6:
                return $myqry['firstName'].' '.$myqry['surname'];
            case 7://rgt
                return $myqry['rgt'];
            case 8://lft
                return $myqry['lft'];
            case 9:// get position
                return $myqry['position'];
            default:
                return 0;
                
        }
    }
    public function getMemberPartsUsingMemberNo($memberNo,$optn=1)
    {
        $myqry = (new \yii\db\Query())
                ->select('*')
                ->from('sponsorship s')
                ->leftJoin('ranks r', 'r.id=s.rank')
                ->where(['membershipNo'=> $memberNo])
                ->one();
        switch($optn){
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
    
    public function confirmParent($sponsor/**typeOf people.id*/)
    {
        //select level+1 get no of children parented by sponsor
        
        //if children are more than 6 [Allowed cildren No Per Level Per Parent] then go to next lower level
        
        //else return next child sponsored at this level
        return $sponsor;
    }
    private function toggle($mybool)
    {
        return $mybool==0?1:0;
    }
    
    public function isRegistered($memberId,$optn=1) {
       $mycount = (new \yii\db\Query())
                ->select('*')
                ->from('inpayments i')
                
                ->where(['member'=>$memberId, 'ptype'=>1]);
       switch($optn){
           case 1:
                return $mycount->count();
           case 2: //if approved 1
                return $this->toggle($mycount->andWhere(['confirmDate'=>Null])->count());
           default:
               return -1;
       }
    }
    
    public function isAMember($memberId){
        $myqry = (new \yii\db\Query())
                ->select('*')
                ->from('sponsorship ')
                
                ->where(['member'=>$memberId])
                ->one(); 
        return $myqry['membershipNo'];
    }
    
    private function getNextParent($sponsor)
    {
        //check if sopsor has left and right
        //get all parents
        $myqry = (new \yii\db\Query())->select('*')->from('sponsorship ');
        
        $my1stQry = $myqry ->where(['member'=>$sponsor])->one();
        $lftVal = $my1stQry['lft'];
        $rgtVal = $my1stQry['rgt'];
        $parentlvl = $my1stQry['level'];
        //$myqry2 = (new \yii\db\Query())->select('parent ,level - $parentlvl AS lvl, COUNT(level) AS lvlCount')->from('sponsorship ');     
        $myqry3 = (new \yii\db\Query())->select('Min(level)')->from('sponsorship ');     
        $my2ndQry= $myqry3->Where('lft>:lft',[':lft'=>$lftVal])
                   ->andWhere('rgt<:rgt',[':rgt'=>$rgtVal])
                    ->andwhere('rgt=lft+1');
        $qryParent= $my2ndQry->orderBy('recordDate')->all();
        //get the parent of thestse that does not have 2 siblings 
        $parentFound=false;
        foreach($qryParent as $leaf){
            if($myqry->where(['member'=>$leaf['parent']])->count()<2){
                $this->nextParent=$leaf['parent'];
                $parentFound=true;
                break;
            } 
        }
        if(!$parentFound){// all parents have two
            $childAsParent = $my2ndQry->orderBy('recordDate')->one();
            $this->nextParent=$childAsParent['member'];//need to requery
                $parentFound=true;
        }           
        return ;//parent is in $this->mextParent if $parentFound is true  
    }
    
    private function checkChildrenNo($parent)
    {
        $myqry = (new \yii\db\Query())
                ->select('*')
                ->from('sponsorship ')
               
                ->where(['parent'=>$parent]);
        if($myqry->count()==0){
            // select left or right randomly
            $this->nextLft= random_int(0, 1);
            
            $this->nextParent = $parent;
            $isAllocated = 1;
        }elseif($myqry->count()==1){//count is 1
            $mychildren=$myqry->one();
            $this->nextlft= $this->toggle($mychildren['lft']);
            $this->nextParent = $parent;
            $isAllocated = 1;
        }else{//count is 2
            $isAllocated = 0;
        }
        return $isAllocated ;       
    }
    private function getParentParticulars($parent)
    {
        //add 4to the left and anything greter than that in lft and rgt columns
        $myqry = (new \yii\db\Query())
                ->from('sponsorship ')
                ->where(['parent'=>$parent])
                ->andWhere('rgt = (lft+1)')
                ->one();
        $mysubqry = $myqry->select('MIN(level)');
        $myfullqry = $myqry->andWhere(['level'=>$mysubqry]);//we hve the parent
        
        
    }
    public function addChild($myId,$parent,$side=1/*0=root;1=letf*; 2=right*/)
    {
        //if first
        if($this->confirmEmptySponsor()==0){
            $this->insertChild($myId,$myId,1,0);
        }else{
        //confirm that parent is not full
        $childrenNo = $this->getChildren($parent);
        if($childrenNo==1){
            //confirm child is left
            if($this->getChildren($parent, 2)==1){//exitting child is left
                //get rgt of parent
                $childLft = $this->getMemberPartsUsingPeopleId($parent,8);
                //update sponsors lft and rgt >= childlft
                $this->adjustSponsorship($childLft);
                // insert the child
                $this->insertChild($myId,$parent,$childLft,2);
            }elseif($this->getChildren($parent, 2)==2){ //existing child is right
                //get right child's left value
                $childLft = $this->getChildren($parent, 3);
                $this->adjustSponsorship($childLft);
                // insert the child
                $this->insertChild($myId,$parent,$childLft,1);
            }
        }elseif(!$childrenNo){// No Existing  children 
            $childLft = $this->getMemberPartsUsingPeopleId($parent,8);
                //update sponsors lft and rgt >= childlft
                $this->adjustSponsorship($childLft);
                // insert the child
                $this->insertChild($myId,$parent,$childLft,$side);
        }
        }
    }
    private function getChildren($parent,$item=1) {
        $myqry =  (new \yii\db\Query())
                ->select('*')
                ->from('sponsorship ')
                ->where(['parent'=>$parent]);
        switch($item){
            case 1:// count of children
                return  $myqry->count();
            case 2:// position of 1st child
                $childparts = $myqry->one();
                return  $childparts['position'];
            case 3: // left of existing child
                $childparts = $myqry->one();
                return  $childparts['lft'];
            default:
                return -1;
        }
                
    }
    /*private function confirmSide($member){
        $myqry = (new \yii\db\Query())
                ->select('*')
                ->from('sponsorship ')
                ->where(['member'=>$member])
                ->one();
        return $myqry['position'];
                
    }*/
    private function adjustSponsorship($lft){
        $db=Yii::$app->db;
        $transaction = $db->beginTransaction();
        try{
        //add  2 to values in lft and rgt >= $lft
            $db->createCommand()->update('sponsorship',[
               'lft'=> 'lft+2',
             ] , 'lft>=:lft',[':lft'=>$lft])->execute();
            $db->createCommand()->update('sponsorship',[
               'rgt'=> 'rgt+2',
             ] , 'rgt>=:rgt',[':rgt'=>$lft])->execute();
        }
        catch(\Exception $e) {
        $transaction->rollBack();
        throw $e;
        } catch(\Throwable $e) {
        $transaction->rollBack();
        throw $e;
        }
    }
    private function insertChild($peopleId,$parent,$lft,$position=1 /*root=0;left=1;right=2*/){
        $tempSponsorNo = $this->getTempSponsorDetails($peopleId);//sponsor's membershipNo
        $tempSponsor = $this->getMemberPartsUsingMemberNo($tempSponsorNo);
        //$parent = $this->getNextParent($tempSponsor);
        $db = Yii::$app->db;
        $db->createCommand()->insert('sponsorship',[
            'member'=> $peopleId,
            'membershipNo' => $this->getNextMemberNo(),
            'sponsor' => $tempSponsor ,
            'parent' =>$parent ,
            'lft'=> $lft,
            'rgt'=> $lft+1,
            'position'=> $position,
            'status' => 2,//Active
            'Rank' => 1,
            'level' => $this->getMemberPartsUsingPeopleId($parent,4)+1,
            'RecordDate' => date('Y-m-d H:i:s'),
            'RecordBy' => Yii::$app->user->id, 
        ])
                
                ->execute();
    }
    
    
    private function confirmEmptySponsor(){
        return  (new \yii\db\Query())
                ->select('*')
                ->from('sponsorship ')
                ->where('rgt>lft')//ensures admin has no and is not counted
                ->count();
    }
}
