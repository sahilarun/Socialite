<?php

namespace backend\controllers;

use backend\models\Ad;
use backend\models\AdSearch;
use common\models\Category;
use common\models\City;
use common\models\Country;
use common\models\State;
use common\models\ReportedAd;
use Yii;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 *
 */
class AdController extends Controller
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
    public function actionIndex($type = 'active')
    {
        $adType['type'] = $type;
        if ($type == 'active') {
            $adType['title'] = 'Active Ads';
        } elseif ($type == 'pending') {
            $adType['title'] = 'Pending Ads';
        } elseif ($type == 'all') {
            $adType['title'] = 'All Ads';
        } elseif ($type == 'expire') {
            $adType['title'] = 'Expired Ads';
        } else {

            $adType['title'] = 'Ads';
        }

        $searchModel = new AdSearch();
        
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $type);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'adType' => $adType,
        ]);
    }

    /**
     * Displays a single Countryy model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id, $type = 'active')
    {
        $model = $this->findModel($id);
        $adType['type'] = $type;

        $adType['title'] = 'Ad Detail';

        return $this->render('view', [
            'model' => $model,
            'adType' => $adType,
        ]);
    }

    /**
     * Creates
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {

        $model = new Package();
        $modelPromotionalBanner = new PromotionalBanner();

        $promotionalBannerData = ArrayHelper::map($modelPromotionalBanner->getAllPromotionalBanner(), 'id', 'name');

        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                Yii::$app->session->setFlash('success', "Package created successfully");
                return $this->redirect(['index']);

            }

        } else {

            $model->type = $model::TYPE_ORDINARY;
        }
        return $this->render('create', [
            'model' => $model,
            'promotionalBannerData' => $promotionalBannerData,

        ]);
    }

    public function actionUpdate($id, $type = 'active')
    {

        $adType['type'] = $type;
        $adType['title'] = 'Update Ads';

        $model = $this->findModel($id);
        $model->scenario = 'update';
        $modelCategory = new Category();
        $modelCountry = new Country();
        $modelState = new State();
        $modelCity = new City();

        $mainCategoryDataList = ArrayHelper::map($modelCategory->getMainCategory(), 'id', 'name');
        $subCategoryDataList = ArrayHelper::map($modelCategory->getSubCategory($model->category_id), 'id', 'name');

       /* $countryDataList = $modelCountry->getCountryDropdown();

        $stateDataList = ArrayHelper::map($modelState->getStateList($model->country_id), 'id', 'name');
        $cityDataList = ArrayHelper::map($modelCity->getCityList($model->state_id), 'id', 'name');
        */
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', "Ad updated successfully");
            return $this->redirect(['index', 'type' => $type]);

        }

        return $this->render('update', [
            'model' => $model,
            'mainCategoryDataList' => $mainCategoryDataList,
            'subCategoryDataList' => $subCategoryDataList,
         //   'countryDataList' => $countryDataList,
         //   'stateDataList' => $stateDataList,
          //  'cityDataList' => $cityDataList,
            'adType' => $adType,

        ]);

    }

    public function actionApprove($id, $type = 'active')
    {

        $model = $this->findModel($id);
        $model->status = $model::STATUS_ACTIVE;
        if ($model->save(false)) {

            Yii::$app->session->setFlash('success', "Ad Approved successfully");
            return $this->redirect(['index', 'type' => $type]);
        }

    }

    public function actionReject($id, $type = 'active')
    {

        $model = $this->findModel($id);
        $model->status = $model::STATUS_REJECTED;
        if ($model->save(false)) {

            Yii::$app->session->setFlash('success', "Ad Rejected successfully");
            return $this->redirect(['index', 'type' => $type]);
        }

    }

    public function actionDelete($id, $type = 'active')
    {
        $model = $this->findModel($id);
        $model->status = $model::STATUS_DELETED;
        if ($model->save(false)) {

            Yii::$app->session->setFlash('success', "Ad deleted successfully");

            return $this->redirect(['index', 'type' => $type]);
        }

    }









    public function actionSubCategoryLists($id)
    {

        $modelCategory = new Category();
        $results = $modelCategory->getSubCategory($id);

        if (!empty($results)) {
            echo "<option value=''>Select Sub Category </option>";
            foreach ($results as $result) {
                echo "<option value='" . $result->id . "'>" . $result->name . "</option>";
            }
        } else {
            echo "<option value=''> No Result </option>";
        }

    }

    public function actionStateLists($id)
    {

        $model = new State();
        $results = $model->getStateList($id);

        if (!empty($results)) {
            echo "<option value=''>Select State </option>";
            foreach ($results as $result) {
                echo "<option value='" . $result->id . "'>" . $result->name . "</option>";
            }
        } else {
            echo "<option value=' ' > No Result </option>";
        }

    }
    public function actionCityLists($id)
    {

        $model = new City();
        $results = $model->getCityList($id);

        if (!empty($results)) {
            echo "<option value=''>Select City </option>";
            foreach ($results as $result) {
                echo "<option value='" . $result->id . "'>" . $result->name . "</option>";
            }
        } else {
            echo "<option value=''> No Result </option>";
        }

    }

    public function actionReportedAds()
    {

        $type = 'all';
        $searchModel = new AdSearch();
        $dataProvider = $searchModel->searchReportedAd(Yii::$app->request->queryParams, $type);

        return $this->render('reported-ads', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,

        ]);
    }

    public function actionViewReportedAd($id)
    {
        $model = $this->findModel($id);

        return $this->render('view-reported-ad', [
            'model' => $model,

        ]);
    }


    
    public function actionReportedAdAction($id, $type)
    {
        $modelReportedAd = new ReportedAd();
        $model = $this->findModel($id);
        if($type=='cancel'){
           
            $currentTime = time();
            $modelReportedAd->updateAll(['status' => ReportedAd::STATUS_REJECTED,'resolved_at'=>$currentTime], [ 'ad_id' => $id,'status'=> ReportedAd::STATUS_PENDING]);
            Yii::$app->session->setFlash('success', "Reported request cancelled successfully");
                return $this->redirect(['reported-ads']);
        }else if($type=='block'){
            
            $currentTime = time();
            $modelReportedAd->updateAll(['status' => ReportedAd::STATUS_ACEPTED,'resolved_at'=>$currentTime], [ 'ad_id' => $id,'status'=> ReportedAd::STATUS_PENDING]);
            
            $model->status = $model::STATUS_REJECTED;
            if ($model->save(false)) {
                Yii::$app->session->setFlash('success', "Ad Rejected successfully");
                return $this->redirect(['reported-ads']);
            }
        }
       
        
        
    }

    protected function findModel($id)
    {
        if (($model = Ad::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
