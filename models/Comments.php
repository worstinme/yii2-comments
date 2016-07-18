<?php

namespace worstinme\comments\models;

use Yii;

class Comments extends \yii\db\ActiveRecord
{

    public $captcha;
    public $url;

    /**
    * @inheritdoc
    */   
    
    public static function tableName()
    {
        return 'comments';
    }

    public function behaviors()
    {
        return [
            \yii\behaviors\TimestampBehavior::className(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['item_id','relation', 'parent_id'], 'required'],
            [['item_id', 'parent_id'], 'integer'],
            [['content'], 'string'],
            [['content'], 'required','message'=>'Напишите комментарий'],
            [['captcha'], 'required','on'=>'guest','message'=>'Введите код'],
            [['author'], 'required','on'=>'guest','message'=>'Представьтесь'],
            [['email'], 'required','on'=>'guest','message'=>'Укажите ваш e-mail'],
            [['email'],'email','message'=>'Укажите правильный e-mail'],
            [['captcha'], 'captcha','on'=>'guest','captchaAction'=>'/comments/captcha'],
            [['relation', 'author', 'email'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'relation' => 'Relation',
            'item_id' => 'Item ID',
            'parent_id' => 'Parent ID',
            'user_id' => 'User ID',
            'author' => 'Author',
            'email' => 'Email',
            'content' => 'Content',
            'state' => 'State',
            'created' => 'Created At',
            'vote_up' => 'Vote Up',
            'vote_down' => 'Vote Down',
            'params' => 'Params',
        ];
    }

    public function getRelated()
    {
        return $this->hasMany(Comments::className(), ['parent_id' => 'id']);
    }

    public function getDate() 
    {
        return Yii::$app->formatter->asRelativeTime($this->created_at);
    } 

    public function beforeSave($insert) {
        if (parent::beforeSave($insert)) {

            if ($this->isNewrecord) {

                if (!Yii::$app->user->isGuest) {
                    $this->user_id = Yii::$app->user->identity->id;  
                    $this->author = Yii::$app->user->identity->username;  
                    $this->email = Yii::$app->user->identity->email;  
                    $this->state = 1;
                }
            }

            return true;
        }
        else return false;
    }
    
}
