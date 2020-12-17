<?php
namespace frontend\models;

use Yii;
use yii\base\Model;
use common\models\User;
use common\mail;
//use frontend\models\Tempsponsor;
use frontend\modules\dashboard\models\Sponsorship;

/**
 * Signup form
 */
class SignupForm extends Model
{
    //public $username;
    public $email;
    public $email_repeat;
    public $password;
    public $password_repeat;
    public $sponsor;


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            /*['username', 'trim'],
            ['username', 'required'],
            ['username', 'unique', 'targetClass' => '\common\models\User', 'message' => 'This username has already been taken.'],
            ['username', 'string', 'min' => 2, 'max' => 255],
            */
            [['email' ,'email_repeat'], 'trim'],
            [['email','email_repeat' ], 'required'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            ['email', 'unique', 'targetClass' => '\common\models\User', 'message' => 'This email address has already been taken.'],

            ['email_repeat', 'compare', 'compareAttribute' => 'email'],

            [['password', 'password_repeat'], 'required'],
            //['password','compare'],
            ['password', 'string', 'min' => 6],
            
            ['password_repeat', 'compare', 'compareAttribute' => 'password'],
            
            ['sponsor', 'required'],
            ['sponsor', 'integer'],
            ['sponsor','exist','targetClass'=>Sponsorship::class,'targetAttribute' =>['sponsor'=>'membershipNo']],
        ];
    }

    /**
     * Signs user up.
     *
     * @return bool whether the creating new account was successful and email was sent
     */
    public function signup()
    {
        if (!$this->validate()) {
            return null;
        }
        
        $user = new User();
        $user->username = $this->email;
        $user->email = $this->email;
        $user->setPassword($this->password);
        $user->generateAuthKey();
        $user->generateEmailVerificationToken();
        $user->save() && $this->sendEmail($user);
        $this->saveSponsor(  $this->sponsor);
        return true;
    }
    public function join($parent,$sponsor,$left)
    {
        if (!$this->validate()) {
            return null;
        }
        
        $user = new User();
        $user->username = $this->username;
        $user->email = $this->email;
        $user->setPassword($this->password);
        $user->generateAuthKey();
        $user->generateEmailVerificationToken();
        $user->save() /*&& $this->sendEmail($user)*/;
        $this->saveSponsored( $parent, $sponsor,$left);
        return true;
    }
    public function saveSponsor(/*$userId,*/ $sponsorNo)
    {
        $session = Yii::$app->session;
        $db = Yii::$app->db;
        try{
            $id=$db->lastInsertId;//get user.id
            $db->createCommand()->insert('tempsponsor',[
                'sponsor'=> $sponsorNo,
                'member' => $id,
                'RecordDate' => date('Y-m-d H:i:s'),
                'RecordBy' => $id
                ])->execute();
            
            $session->addFlash('success','User ID: '.$id);
            return true;
        } catch (Exception $e){
            $session->addFlash('error','An error has occured: '.$e->getMessage());
            return false; 
        }
    }

    public function saveSponsored($parent, $sponsor,$lft)
    {
        $session = Yii::$app->session;
        $db = Yii::$app->db;
        try{
            $id=$db->lastInsertId;//get user.id
            $db->createCommand()->insert('tempsponsor',[
                'sponsor'=> $sponsor,
                'member' => $id,
                'parent' => $parent,//?? need to confirm if parent is a child of sponsor??
                'lft' => $lft=='l'?1:0,
                'RecordDate' => date('Y-m-d H:i:s'),
                'RecordBy' => $id
                ])->execute();
            
            $session->addFlash('success','User ID: '.$id);
            return true;
        } catch (Exception $e){
            $session->addFlash('error','An error has occured: '.$e->getMessage());
            return false; 
        }
    }
    public function attributeLabels()
    {
        return [
            'sponsor' => 'Sponsor\'s ID',
            'email_repeat' => 'Confirm Email',
            'password_repeat' => 'Confirm Password',
            'username' => 'E-mail'
        ];
    }

    /**
     * Sends confirmation email to user
     * @param User $user user model to with email should be send
     * @return bool whether the email was sent
     */
    protected function sendEmail($user)
    {
        return Yii::$app
            ->mailer
            ->compose(
                ['html' => 'emailVerify-html', 'text' => 'emailVerify-text'],
                ['user' => $user]
            )
            ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name . ' robot'])
            ->setTo($this->email)
            ->setSubject('Account registration at ' . Yii::$app->name)
            ->send();
    }
}
