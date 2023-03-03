<?php
namespace backend\controllers;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\models\Setting;
use yii\helpers\ArrayHelper;
use app\models\User;

/**
 * 
 */
class SettingController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all  models.
     * @return mixed
     */
    public function actionIndex()
    {
        $id=1;
        $model = $this->findModel($id);
       
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
 
            $modelUser = new User();
            $modelUser->checkPageAccess();

            if($model->save()){
                Yii::$app->session->setFlash('success', "Setting updated successfully");
                return $this->redirect(['index']);
                
            }
                
        }
       
        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionGeneralInformation(){

        $id=1;
        $model = $this->findModel($id);
    
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {

            $modelUser = new User();
            $modelUser->checkPageAccess();

            if($model->save()){
                Yii::$app->session->setFlash('success', "Setting updated successfully");
                return $this->redirect(['general-information']);
                
            }
                
        }
        return $this->render('generalupdate', [
            'model' => $model,
        ]);
    }

    public function actionPayment(){
        $id=1;
        $model = $this->findModel($id);
       
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
    
            $modelUser = new User();
            $modelUser->checkPageAccess();
    
            if($model->save()){
                Yii::$app->session->setFlash('success', "Setting updated successfully");
                return $this->redirect(['payment']);
                
            }
                
        }
        return $this->render('paymentupdate', [
            'model' => $model,
        ]);
     }
     public function actionSocialLinks(){
      
        $id=1;
        $model = $this->findModel($id);
       
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
    
            $modelUser = new User();
            $modelUser->checkPageAccess();
    
            if($model->save()){
                Yii::$app->session->setFlash('success', "Setting updated successfully");
                return $this->redirect(['social-links']);
                
            }
                
        }
        return $this->render('sociallinksupdate', [
            'model' => $model,
        ]);
     }

     public function actionAppSetting(){
        $id=1;
        $model = $this->findModel($id);
       
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
    
            $modelUser = new User();
            $modelUser->checkPageAccess();
    
            if($model->save()){
                Yii::$app->session->setFlash('success', "Setting updated successfully");
                return $this->redirect(['app-setting']);
                
            }
                
        }
        return $this->render('app', [
            'model' => $model,
        ]);
     }

     public function actionFeature(){
        {
            $id=1;
            $model = $this->findModel($id);
           
            if ($model->load(Yii::$app->request->post()) && $model->validate()) {
     
                $modelUser = new User();
                $modelUser->checkPageAccess();
    
                if(($model->is_photo_post==0 && $model->is_video_post==0)) {
    
                    Yii::$app->session->setFlash('error', "At least 1 thing should be enabled from Photo Post and Video Post, both can’t be disabled");
                   return $this->goBack(Yii::$app->request->referrer);
                }
    
                if($model->is_stories==1)
                {
                  
                  $model->is_story_highlights=1;
    
                }
               
                if( $model->is_story_highlights==1  &&  $model->is_stories==0)
                {
                    Yii::$app->session->setFlash('error', "Enable only when  Story Highlights is Enable ");
                    return $this->goBack(Yii::$app->request->referrer);
                }
    
                if($model->is_chat==1)
                {
                   /* $model->is_photo_share=1;  $model->is_video_share=1;   $model->is_files_share=1; $model->is_gift_share=1; $model->is_audio_share=1; $model->is_drawing_share=1;
    
                    $model->is_user_profile_share=1;$model->is_club_share=1;  $model->is_photo_share=1; $model->is_reply=1; $model->is_forward=1; $model->is_star_message=1;  $model->is_events_share=1;   $model->is_location_sharing=1; $model->is_contact_sharing=1; 
                    */
                }
                if($model->is_chat==0){
    
                    $model->is_photo_share=0;  $model->is_video_share=0;   $model->is_files_share=0; $model->is_gift_share=0; $model->is_audio_share=0; $model->is_drawing_share=0;
    
                    $model->is_user_profile_share=0;$model->is_club_share=0;  $model->is_photo_share=0; $model->is_reply=0; $model->is_forward=0; $model->is_star_message=0;  $model->is_events_share=0; $model->is_location_sharing=0; $model->is_contact_sharing=0;
               
    
                }
    
    
                if($model->save()){
                    Yii::$app->session->setFlash('success', "Setting updated successfully");
                    return $this->redirect(['feature']);
                    
                }
                    
            }
           
            return $this->render('feature', [
                'model' => $model,
            ]);
        }
     }


    protected function findModel($id)
    {
        if (($model = Setting::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
