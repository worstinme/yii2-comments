<?php 

namespace worstinme\comments;

use Yii;
use yii\helpers\Html;
use worstinme\comments\models\Comments;

class Widget extends \yii\base\Widget {

	
	public $relation;
	public $model;
	public $respond = true;
	public $url;

   	public function run()
    {
    	$tablename = $this->model->tablename();

		$comments = Comments::find()
			->where(['parent_id'=>0,'relation'=>$this->relation,'item_id'=>!empty($this->model) ? $this->model->id : null])->all();

	    if ($this->respond) {

	    	$respond = new Comments;

	    	if (Yii::$app->user->isGuest) {
	    		$respond->scenario = 'guest';
	    	}

	    }
	    else {
	    	$respond = false;
	    }

	    return $this->render('index',[
	    	'comments'=>$comments,
	    	'respond'=>$respond,
	    	'model'=>$this->model,
	    	'url'=>$this->url,
	    ]);
        
    }

}