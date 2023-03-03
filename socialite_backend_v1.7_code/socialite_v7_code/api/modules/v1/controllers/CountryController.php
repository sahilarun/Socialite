<?php

namespace api\modules\v1\controllers;

use yii\rest\ActiveController;
use api\modules\v1\models\Country;

class CountryController extends ActiveController
{
    public $modelClass = 'api\modules\v1\models\country';   
    
    public function actions()
	{
		$actions = parent::actions();

		// disable default actions
		unset($actions['create'], $actions['update'], $actions['index'], $actions['delete'], $actions['view']);                    

		return $actions;
	}    


    public function actionIndex(){
        
        $model =  new Country();
        $modelResult  =$model->find()->where(['status'=>Country::STATUS_ACTIVE])->orderBy(['name'=>SORT_ASC])->all(); 
         $response['message']='Ok';
        $response['country']=$modelResult;
        
        return $response;
    }


}


