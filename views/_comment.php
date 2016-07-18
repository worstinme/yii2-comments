<?php

use yii\helpers\Html;

$new = !empty($new) && $new ?' uk-comment-primary' : '';
$created = !empty($created) ? $created : false;

?>
<?php if ($comment->state == 0 && count($comment->related) && !$created && (!Yii::$app->user->can('admin') || Yii::$app->user->isGuest)): ?>
    <?php foreach ($comment->related as $related): ?>
        <?= $this->render('_comment',['comment'=>$related,'parent'=>$comment,'url'=>$url]);?>
    <?php endforeach ?>    
<?php elseif($comment->state == 1 || Yii::$app->user->can('admin') || (!Yii::$app->user->isGuest && Yii::$app->user->identity->id == $comment->user_id) || $created): ?> 
<li id="comment-<?=$comment->id?>" class="<?=$comment->state == 1 ? 'show' : 'hide'?>">
    <article class="uk-comment<?=$new?>">
        <header class="uk-comment-header">
            <? //=Html::img('/images/placeholder_avatar.svg',['class'=>'uk-comment-avatar','width'=>20,'height'=>20])?>
            <i class="uk-icon-user"></i>
            <span class="comment-author-name"><?=$comment->author?></span>
            <span class="comment-date"><?=$comment->date?></span>
            <span class="comment-link"><a href="<?=$url?>#comment-<?=$comment->id?>">#</a></span>
            <span class="comment-respond"><a href="#respond" data-reply="<?=$comment->id?>">ответить</a></span>
            <?php if (Yii::$app->user->can('admin')): ?>
            <div class="uk-float-right">    
                <a class="uk-icon-trash" data-remove="<?=$comment->id?>"></a>
                <?php if ($comment->state==1): ?>
                <a class="uk-icon-eye-slash" href="#" data-hide="<?=$comment->id?>"></a>
                <?php else: ?>
                <a class="uk-icon-eye" href="#" data-show="<?=$comment->id?>"></a>    
                <?php endif ?>
            </div>
            <?php endif ?>

        </header>
        <div class="uk-comment-body">
            <?=$comment->content?>
        </div>
    </article>
    <?php if (count($comment->related)): ?>
    <ul>
        <?php foreach ($comment->related as $related): ?>
           <?= $this->render('_comment',['comment'=>$related,'parent'=>$comment,'url'=>$url]);?>
        <?php endforeach ?>
    </ul>
    <?php endif ?>
</li>
<?php endif ?>