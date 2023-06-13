<?php

namespace api\modules\v1\controllers;
use yii\rest\ActiveController;
use api\modules\v1\models\Package;

use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;




/**
 * package Controller API
 *
 
 */
class PackageController extends ActiveController
{
    public $modelClass = 'api\modules\v1\models\package';   
    
    public function actions()
	{
		$actions = parent::actions();

		// disable default actions
		unset($actions['create'], $actions['update'], $actions['index'], $actions['delete'], $actions['view']);                    

		return $actions;
	}    


    public function actionIndex(){
        $model =  new Package();
        $results = $model->getOrdinaryPackage();
       
        $response['message']='ok';
        $response['package']=$results;
        
        return $response;
    }


}


