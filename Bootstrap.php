<?php

namespace worstinme\comments;

use yii\base\BootstrapInterface;
use yii\base\Application;

class Bootstrap implements BootstrapInterface
{
    public function bootstrap($app)
    {
       	$this->logTarget = Yii::$app->getLog()->targets['debug'] = new LogTarget($this);

        // delay attaching event handler to the view component after it is fully configured
        $app->on(Application::EVENT_BEFORE_REQUEST, function () use ($app) {
            $app->getView()->on(View::EVENT_END_BODY, [$this, 'renderToolbar']);
        });       
    }
}