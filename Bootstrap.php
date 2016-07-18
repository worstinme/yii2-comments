<?php

namespace worstinme\comments;

use yii\base\BootstrapInterface;
use yii\base\Application;

class Bootstrap implements BootstrapInterface
{
    public function bootstrap($app)
    {
       	
       	if (YII_DEBUG) {
       	//	$app->controllerMap['comments'] = ['class'=>'worstinme\comments\Controller'];
       	}

       	/*

	    "extra": {
	        "bootstrap": "worstinme\\comments\\Bootstrap"
	    }
    */
       
    }
}