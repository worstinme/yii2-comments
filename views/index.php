<?php

use yii\helpers\Html;
use yii\helpers\Url;
use worstinme\uikit\ActiveForm;
use yii\captcha\Captcha;

\worstinme\uikit\assets\Notify::register($this);

?>
<section id="comments">
	<ul class="uk-comment-list"><?php if (count($comments)): ?>
        <?php foreach ($comments as $comment): ?>
        <?= $this->render('_comment',['comment'=>$comment,'url'=>$url]);?>
        <?php endforeach ?>        
	<?php endif ?></ul>
	<hr>
	<?php if ($respond): ?>

	<div id="respond">	
		<?php $form = ActiveForm::begin(['action'=>['/comments/create']]); ?>

    	<?= Html::activeHiddenInput($respond, 'parent_id',['value'=>0]) ?>
    	<?= Html::activeHiddenInput($respond, 'item_id',['value'=>$model->id]) ?>
    	<?= Html::activeHiddenInput($respond, 'relation',['value'=>$this->context->relation]) ?>
    	<?= Html::activeHiddenInput($respond, 'url',['value'=>$url]) ?>
		    
    	<div class="uk-grid">
			<div class="uk-width-1-1 uk-form-row">
				<?= $form->field($respond, 'content')->textarea(['rows'=>5,'class'=>'uk-width-large','placeholder'=>'Комментарий'])->label(false) ?>
		    </div> 
		    <?php if ($respond->scenario == 'guest'): ?>
		    <?php if (Yii::$app->has('authClientCollection')): ?>
		    <div class="uk-margin-top">    
		        <?php $authAuthChoice = \worstinme\user\AuthChoice::begin([ 'baseAuthUrl' => ['/user/default/auth']]); ?>
		        <div class="services uk-display-inline-block uk-subnav">
		            <?php foreach ($authAuthChoice->getClients() as $client): ?>
		                <?php $authAuthChoice->clientLink($client) ?>
		            <?php endforeach; ?>
		        </div>
		        <?php \worstinme\user\AuthChoice::end(); ?>
		    </div>  
		    <?php endif ?>
			<div class="uk-width-medium-4-10 uk-form-row">
				<?= $form->field($respond, 'author')->textInput(['class'=>'uk-width-large','placeholder'=>'Имя'])->label(false) ?>
			    <?= $form->field($respond, 'email')->textInput(['class'=>'uk-width-large','placeholder'=>'E-mail'])->label(false) ?>
			</div>
			<div class="uk-width-medium-2-10 uk-form-row">
		    	<div id="captcha">
		    	<?= $form->field($respond, 'captcha')->widget(Captcha::className(),['captchaAction'=>'/comments/captcha'])->label(false)?>
		    	</div>  
		    </div>
			<?php endif ?>
		    <div class="uk-width-medium-4-10 uk-form-row">
		    	<?= Html::submitButton('Отправить комментарий',['class' => 'uk-width-1-1 uk-button uk-button-success']) ?>
		    </div>
		    <div class="uk-width-medium-4-10 uk-form-row">
		    	<a class="dfn uk-hidden" href="#respond" data-reply-main>в новой ветке</a>
		    </div>		    
		</div>

	    <?php ActiveForm::end(); ?>
	</div>

	<?php endif ?>
</section>

<?php

$text = Yii::$app->user->isGuest ? 'Комментарий добавлен и будет опубликован после проверки модератором.' : 'Комментарий размещен.';

$script = <<< JAVASCRIPT


JAVASCRIPT;

$script .= <<<JAVASCRIPT

$('#respond form').on('beforeSubmit', function(event, jqXHR, settings) {
    var form = $(this),
    formdata = form.serialize();
    form.trigger('reset');
   	if(form.find('.has-error').length) {
        return false;
	}
    $.post(form.attr('action'), formdata, function(data) {
    	if (data.code == 100) {	   	     
	        if ($("#respond").hasClass("reply")) {
	        	$("#respond").before('<ul>'+data.comment+'</ul>');
	         	$("#respond").removeClass('reply').detach().appendTo("#comments");
	        }
	        else {
	        	$("#comments .uk-comment-list").append(data.comment);
	        }
        }
        else {
        	UIkit.notify(data.message, {status:'warning'})
        }
		        console.log(data);
    });
   	return false;
});  

$("#comments")
	.on("click","a[data-reply]",function(e){
		e.preventDefault();
		var comment_id = Number($(this).data('reply'));
		$("#respond input[name='Comments[parent_id]']").val(comment_id);
		var respond = $("#respond").addClass('reply').detach();
		respond.appendTo("#comment-"+comment_id);
		$("#respond a[data-reply-main]").removeClass('uk-hidden');
		$("html,body").stop().animate({scrollTop: $("#respond").offset().top},300,'easeOutExpo');
	})
	.on("click","a[data-reply-main]",function(e){
		e.preventDefault();
		$("#respond input[name='Comments[parent_id]']").val(0);
		var respond = $("#respond").removeClass('reply').detach();
		respond.appendTo("#comments");
		$("#respond a[data-reply-main]").addClass('uk-hidden');
		$("html,body").stop().animate({scrollTop: $("#respond").offset().top},300,'easeOutExpo');
	});
JAVASCRIPT;

if (Yii::$app->user->can('moder') || Yii::$app->user->can('admin')) {

$remove_url = Url::toRoute(['/comments/remove']);
$hide_url = Url::toRoute(['/comments/hide']);
$show_url = Url::toRoute(['/comments/show']);
$edit_url = Url::toRoute(['/comments/edit']);

$script .= <<<JAVASCRIPT

$("#comments")
	.on("click","a[data-remove]",function(e){
		e.preventDefault();
		var comment = $(this).data('remove');
		UIkit.modal.confirm("Удалить?", function(){
			$.post('$remove_url',{'comment-id': comment}, function(data) {
				if (data.code == 100) {
		        	$("#comment-"+comment).hide(300);
		        	UIkit.notify(data.message);	 
		        } 
		        else {
		        	UIkit.notify(data.message, {status:'warning'})
		        } 	     
		        console.log(data);    
		    });
		}); 
	})
	.on("click","a[data-hide]",function(e){
		e.preventDefault();
		var comment = $(this).data('hide'),
		link = $(this);
	    $.post('$hide_url',{'comment-id': comment}, function(data) {
			if (data.code == 100) {
		    	$("#comment-"+comment).addClass("hide");
		    	link.removeAttr('data-hide');
		    	link.attr('data-show',comment); 	
		    	UIkit.notify(data.message);	 
		    } 
		    else {
		    	UIkit.notify(data.message, {status:'warning'});
		    } 	         
		        console.log(data);
		})
	})
	.on("click","a[data-show]",function(e){
		e.preventDefault();
		var comment = $(this).data('show'),
		link = $(this);
	    $.post('$show_url',{'comment-id': comment}, function(data) {
			if (data.code == 100) {
		    	$("#comment-"+comment).removeClass("hide");
	        	link.removeAttr('data-show');
	        	link.attr('data-hide',comment); 	  
		    	UIkit.notify(data.message);	 
		    } 
		    else {
		    	UIkit.notify(data.message, {status:'warning'});
		    } 	         
		        console.log(data);
		}); 
	});

JAVASCRIPT;

}

$this->registerJs($script, $this::POS_READY);