<?php
namespace worstinme\comments;

use Yii;
use yii\web\NotFoundHttpException;
use worstinme\comments\models\Comments;


class Controller extends \yii\web\Controller
{

    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => \yii\filters\VerbFilter::className(),
                'actions' => [
                    'create' => ['post'],
                    'remove' => ['post'],
                    'hide' => ['post'],
                    'show' => ['post'],
                    'edit' => ['post'],
                ],
            ],
        ];
    }

    public function beforeAction($action)
    {
        if (!parent::beforeAction($action)) {
            return false;
        }

        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        return true; 
    }

    public function actions()
    {
        return [
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function actionCreate()
    {

        $model = Yii::$app->user->isGuest ? new Comments(['scenario'=>'guest']) : new Comments;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {

            return [
                'code'=>100,
                'comment'=> $this->renderPartial("@worstinme/comments/views/_comment",['comment'=>$model,'url'=>$model->url,'new'=>true,'created'=>true]),
            ];

        }

        return [
            'code'=>10,
            'message'=> 'Комментарий не сохранён.',
            'errors'=>$model->errors,
        ];
    }

    public function actionRemove()
    {
        if (($model = Comments::findOne(Yii::$app->request->post('comment-id'))) !== null) {

            if (count($model->related)) {

                return [
                    'code'=>21,
                    'message'=> 'Нельзя удалить комментарий с ответами',
                ];

            }

            if ($model->delete()) {

                return [
                    'code'=>100,
                    'message'=> 'Комментарий удалён.',
                ];

            }
            
        }

        return [
            'code'=>20,
            'message'=> 'Комментарий не найден',
            'request'=>Yii::$app->request->post(),
        ];
    }

    public function actionHide()
    {
        if (($model = Comments::findOne(Yii::$app->request->post('comment-id'))) !== null) {

            $model->state = 0;

            if ($model->save()) {
                return [
                    'code'=>100,
                    'message'=> 'Комментарий скрыт.',
                ];
            }

            return [
                'code'=>31,
                'message'=> 'Не удалось сохранить комментарий.',
            ];

        }

        return [
            'code'=>30,
            'message'=> 'Комментарий не найден',
        ];
    }

    public function actionShow()
    {
        if (($model = Comments::findOne(Yii::$app->request->post('comment-id'))) !== null) {
            
            $model->state = 1;

            if ($model->save()) {
                return [
                    'code'=>100,
                    'message'=> 'Комментарий одобрен.',
                ];
            }
        }

        return [
            'code'=>30,
            'message'=> 'Комментарий не найден',
        ];
    }

    public function actionEdit()
    {
        if (($model = Comments::findOne(Yii::$app->request->post('comment-id'))) !== null) {

            $reason = Yii::$app->request->post('reason'); 
            $reason = !empty($reason) ? '<p class="edited">'.$reason.'</p>' : '';

            $model->state = 1;
            $model->content = Yii::$app->request->post('content') . $reason;

            if ($model->save()) {

                return [
                    'code'=>100,
                    'message'=> 'Комментарий изменён и одобрен.',
                ];
            }

            return [
                'code'=>41,
                'message'=> 'Не удалось изменить комментарий.',
            ];

        }

        return [
            'code'=>40,
            'message'=> 'Не удалось изменить комментарий.',
        ];
    }

}