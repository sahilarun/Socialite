<?php
namespace app\models;
use common\models\Country;
use common\models\ReportedUser;
use common\models\FileUpload;
use yii\web\ForbiddenHttpException;


use Yii;

/**
 * This is the model class for table "user".
 *
 * @property int $id
 * @property string $username
 * @property string $auth_key
 * @property string $password_hash
 * @property string|null $password_reset_token
 * @property string $email
 * @property int $status
 * @property int $created_at
 * @property int $updated_at
 * @property string|null $verification_token
 */
class User extends \yii\db\ActiveRecord
{
    
    const ROLE_ADMIN = 1;
    const ROLE_SUBADMIN=2;
    const ROLE_CUSTOMER=3;


    const STATUS_DELETED = 0;
    const STATUS_INACTIVE = 9;
    const STATUS_ACTIVE = 10;

    const IS_VERIFIED_NO = 0;
    const IS_VERIFIED_YES = 1;
    

    public $password;
    public $confirmPassword;
    public $imageFile;
   
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            //[['username', 'auth_key', 'password_hash', 'email', 'created_at', 'updated_at'], 'required'],
            [[ 'email','username'], 'required'],
            [['status', 'created_at', 'updated_at','country_id','is_verified','country_id'], 'integer'],
            [['username','name', 'password_hash', 'password_reset_token', 'email', 'verification_token','address','phone','city','postcode','website'], 'string', 'max' => 255],
            [['auth_key'], 'string', 'max' => 32],
            [['username'], 'checkUniqueUsername'],
            [['email'], 'checkUniqueEmail'],
            [['password_reset_token'], 'unique'],
            [['password','confirmPassword'], 'required','on'=>'create'],
            [['password'], 'string', 'min' => 6],
            [['confirmPassword'], 'compare', 'compareAttribute' => 'password'],
            [['imageFile'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg'],
           // [['name'], 'save'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Username',
            'auth_key' => 'Auth Key',
            'password_hash' => 'Password Hash',
            'password_reset_token' => 'Password Reset Token',
            'email' => 'Email',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'verification_token' => 'Verification Token',
            'country_id' => 'Country',
            'is_verified' => 'Is verified?',
            
        ];
    }

    public function beforeSave($insert)
    {
        if ($insert) {

            $this->created_at = time();
            $this->password_hash = Yii::$app->security->generatePasswordHash($this->password);
        }

        $this->updated_at = time();

        return parent::beforeSave($insert);
    }
    
    public function checkUniqueUsername($attribute, $params, $validator)
    {
        if(!$this->hasErrors()){
            if($this->isNewRecord){
                $count= User::find()->where([$attribute=>$this->$attribute])->andWhere(['<>','status',self::STATUS_DELETED])->count();
            }else{
                $count= User::find()->where([$attribute=>$this->$attribute])->andWhere(['<>','id',$this->id])->andWhere(['<>','status',self::STATUS_DELETED])->count();
            }
            
            if($count){
                $this->addError($attribute, 'Username already exist');     
            }
            
        }
       
    }

    

    public function checkUniqueEmail($attribute, $params, $validator)
    {
        if(!$this->hasErrors()){
            if($this->isNewRecord){
                $count= User::find()->where([$attribute=>$this->$attribute])->andWhere(['<>','status',self::STATUS_DELETED])->count();
            }else{
                $count= User::find()->where([$attribute=>$this->$attribute])->andWhere(['<>','id',$this->id])->andWhere(['<>','status',self::STATUS_DELETED])->count();
            }
            
            if($count){
                $this->addError($attribute, 'Email already exist');     
            }
            
        }
       
    }
    public function getLastTweleveMonth()
    {
        $month =  strtotime("+1 month");
        for ($i = 1; $i <= 12; $i++) {
            $months[(int)date("m", $month)] = date("M", $month);
            $month = strtotime('+1 month', $month);
        }
        return $months;
        
    }

    
    public function getIsVerifiedString()
    {
        if($this->is_verified==$this::IS_VERIFIED_YES){
           return 'Yes';    
        }else {
            return 'No';    
        }
       
    }


    public function getLastTweleveMonthUser()
    {
        
        $totalAds = [];
        $monthArr =[];
        $months = $this->getLastTweleveMonth();
        
        $res= Yii::$app->db->createCommand("SELECT month(from_unixtime(created_at)) as month, count(id) as total_ad FROM user where role=3 and status!=0 and from_unixtime(created_at) >= DATE_SUB(CURDATE(), INTERVAL 1 YEAR) group by month")->queryAll();

        foreach($months as $key => $month){
            $found_key = array_search($key, array_column($res, 'month'));  
            //echo gettype($found_key), "\n";
            if(is_int($found_key)){
                $totalAd =  $res[$found_key]['total_ad'];
            }else{
                $totalAd = 0;
            }
            //echo $totalAds;
            /*echo '=====================';
            echo '<br>';
            echo $key.'#'.$month;
            echo '<br>';*/

            //print_r($found_key);
            
            $totalAds[]=$totalAd;
           
            $monthArr[]=$month;

        }
        $output=[];

        $output['data'] = $totalAds;
        $output['dataCaption'] = $monthArr;
        return $output;

        
    }
    



    public function getSex()
    {
       if($this->sex==1){
           return 'Male';
       }else if($this->sex==2){
           return 'Female';    
       }else{
           return 'Other';
       }
    }
    
    public function checkPageAccess()
    {
        if(Yii::$app->params['siteMode']==3){
        
            throw new ForbiddenHttpException('You are not allowed to take this action in demo');
        }
    }
    
    public function getStatus()
    {
       if($this->status==$this::STATUS_INACTIVE){
           return 'Inactive';
       }else if($this->status==$this::STATUS_ACTIVE){
           return 'Active';    
       }
    }

    public function getStatusDropDownData()
    {
        return array(self::STATUS_ACTIVE => 'Active', self::STATUS_INACTIVE => 'Inactive');
    }

    public function getVerifiedStatusDropDownData()
    {
        return array(self::IS_VERIFIED_NO => 'No', self::IS_VERIFIED_YES => 'Yes');
    }

    public function getVerifiedStatus()
    {
       if($this->is_verified==$this::IS_VERIFIED_NO){
           return 'No';
       }else if($this->is_verified==$this::IS_VERIFIED_YES){
           return 'Yes';    
       }
    }

    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
    }


    public function getCountry()
    {
        return $this->hasOne(Country::className(), ['id'=>'country_id']);
        
    }

    public function getCountryDetail()
    {
        return $this->hasOne(Country::className(), ['id'=>'country_id']);
        
        
    }


    public function getUserCount()
    {
        return $this->find()->where(['role' => self::ROLE_CUSTOMER])->andWhere(['<>','status', self::STATUS_DELETED])->count();
    }

    public function getImageUrl(){
        
        $image = $this->image;
        if(empty($this->image)){
            $image  ='default.png';
        }
        $modelFileUpload = new FileUpload();
        return $modelFileUpload->getFileUrl($modelFileUpload::TYPE_USER,$image);

        //return Yii::$app->params['pathUploadUser'].'/'.$image;
        
        
    }
    
    public function getReportedUser()
    {
        return $this->hasMany(ReportedUser::className(), ['report_to_user_id'=>'id'])->orderBy(['id' => SORT_DESC]);
        
    }
    
    public function getReportedUserActive()
    {
        return $this->hasMany(ReportedUser::className(), ['report_to_user_id'=>'id'])->andOnCondition(['reported_user.status' => ReportedUser::STATUS_PENDING]);
        
    }






    
   

}
