<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\ChartAccounts */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="chart-accounts-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'code')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'slug')->textInput(['maxlength' => true]) ?>

    <?php if (!Yii::$app->request->isAjax){ ?>
	  	<div class="form-group">
	        <?= Html::submitButton('Guardar', ['class' => 'btn btn-success' ]) ?>
	    </div>
	<?php } ?>

    <?php ActiveForm::end(); ?>
    
</div>
