<?php
namespace api\modules\v1\models;

use Yii;
use api\modules\v1\models\FileUpload;
use api\modules\v1\models\EventGallaryImage;
use api\modules\v1\models\EventTicket;
use api\modules\v1\models\EventTicketBooking;
use api\modules\v1\models\Organization;



class Event extends \yii\db\ActiveRecord
{
    const COMMON_NO = 0;
    const COMMON_YES = 1;
    
    
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_DEACTIVE = 9;


    const CURRENT_STATUS_UPCOMING = 1;
    const CURRENT_STATUS_ACTIVE = 2;
    const CURRENT_STATUS_COMPLETED = 3;
    const CURRENT_STATUS_CANCELLED = 4; // ticket booking cancelled
    
    


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'event';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status', 'id','category_id','organisor_id','is_paid','created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['name','description','image','start_date', 'end_date','place_name','address','latitude','longitude','disclaimer','description'], 'string'],
     //       [['name','category_id','start_date', 'end_date'], 'required','on'=>['create','update']],
      //      [['imageFile'], 'required','on'=>'create'],

            [['imageFile'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg'],

            

        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [

        ];
    }

    public function beforeSave($insert)
    {
        if ($insert) {
            $this->created_at = time();
            $this->created_by = Yii::$app->user->identity->id;

        } else {
            $this->updated_at = time();
            $this->updated_by = Yii::$app->user->identity->id;

        }

        return parent::beforeSave($insert);
    }

    public function fields()
    {
        $fields = parent::fields();
        $fields[] = 'imageUrl';
        $fields[] = 'eventCurrentStatus';
        $fields['eventGallaryImages'] = (function ($model) {
            $imageArr = [];
            foreach ($model->eventGallaryImages as $img) {
                $imageArr[] = $img->imageUrl;
            }
            return $imageArr;
        });
        $fields['is_ticket_booked'] = (function($model){
            return (@$model->isTicketBooked) ? 1: 0;
        });


       
       // $fields[] = 'competitionImage';
        return $fields;
    }

    public function extraFields()
    {
        return ['eventTicket','eventOrganisor','myEventTicketBooking'];
    }

    public function getImageUrl()
    {
        $modelFileUpload = new FileUpload();
        return $modelFileUpload->getFileUrl($modelFileUpload::TYPE_EVENT,$this->image);
    }

    public function getEventCurrentStatus()
    {
        
        $eventCurrentStatus=1;
        $currentTime = time();
        if($this->start_date > $currentTime){
            $eventCurrentStatus = Event::CURRENT_STATUS_UPCOMING;
        }else if($this->start_date < $currentTime && $this->end_date > $currentTime){
            $eventCurrentStatus = Event::CURRENT_STATUS_ACTIVE;
        }else if($this->end_date < $currentTime){
            $eventCurrentStatus = Event::CURRENT_STATUS_COMPLETED;
        }
         
        return $eventCurrentStatus;
    }

    

    

    /**
     * RELEATION START
     */
    
    public function getEventGallaryImages()
    {
        return $this->hasMany(EventGallaryImage::className(), ['event_id' => 'id']);

    }
    public function getEventTicket()
    {
        return $this->hasMany(EventTicket::className(), ['event_id' => 'id']);

    }
    public function getEventOrganisor()
    {
        
        return $this->hasOne(Organization::className(), ['id' => 'organisor_id']);

    }

    



    public function getIsTicketBooked()
    {
        
        return $this->hasOne(EventTicketBooking::className(), ['event_id' => 'id'])->andOnCondition(['event_ticket_booking.user_id' => @Yii::$app->user->identity->id,'event_ticket_booking.status' => [EventTicketBooking::STATUS_PURCHASED,EventTicketBooking::STATUS_COMPLETED]]);
        
        
    }

    public function getEventTicketBooking()
    {
        return $this->hasMany(EventTicketBooking::className(), ['event_id' => 'id']);
        
        
    }
    public function getMyEventTicketBooking()
    {
        return $this->hasMany(EventTicketBooking::className(), ['event_id' => 'id'])->andOnCondition(['event_ticket_booking.user_id' => @Yii::$app->user->identity->id]);
        
        
    }
    

    





}
