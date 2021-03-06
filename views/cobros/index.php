<?php
error_reporting(E_ALL ^ E_NOTICE);
?>
<?php use kartik\depdrop\DepDrop;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

error_reporting(E_ALL ^ E_NOTICE);
$this->title = 'Registrar Cobro/Pagos';
$this->params['breadcrumbs'][] = $this->title;
$chart_account=ArrayHelper::map(\app\models\ChartAccounts::find()
    ->Select(["id,concat(code,' ',slug) as name"])
    ->alias('t')
    ->where(['(select count(*) from chart_accounts t2 where t2.parent_id=t.id)'=>0])->andWhere(['parent_id'=>13123])->asArray()->all(),'id', 'name');

$form=ActiveForm::begin()

?>
<div class="card">
    <div class="card-header bg-primary">
        Información de Transacción

    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-lg-8 col-md-8 col-sm-10 col-12">
<?php
if($header->tipo_de_documento=="Cliente"){
    $val="Cobro";
}
else{
    $val="Pago";
}
?>

<?= $form->field($chargem, 'type_charge')->textInput(["readonly"=>True,"value"=>$val])->label("Tipo de Transacción"); ?>
                <?= $form->field($charguesd, 'type_transaccion')->dropDownList(
                    ['Caja' => 'Caja', 'Transferencia' => 'Transferencia','Cheque' => 'Cheque'], ["id" => "tipodocu",'onchange'=>'
            $.post( "'.urldecode(Yii::$app->urlManager->createUrl('cobros/subcat?data=')).'"+$(this).val(), function( data ) {
              $( "select#chart" ).html( data );
              console.log(data)
            });
        '])->label("Forma de Cobro")  ?>


  <?=
$form->field($chargem,'date')->textInput(["readonly"=>True,"value"=> $header->f_timestamp])->label("Fecha");
?>
<?=
$form->field($Person,'name')->textInput(["readonly"=>True,"value"=>$Person->name])->label("Persona");
?>
                <?php echo '<label class="control-label">Cuenta Contable</label>'?>;
                <?=
                $form->field($charguesd, 'chart_account')->dropDownList($chart_account,['prompt'=>'Select...','id'=>"chart"])->label(False);

               /* Select2::widget([
                    'model'=>$chargem,
                    'id' => 'chairbank',
                    'attribute' => 'chart_account',
                    'name' => 'accountbank',
                    'data' => $chart_account,
                    'options' => [
                        'placeholder' => 'Seleccione la cuenta bancaria',
                    ],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ]);
               */


                ?>

<div class="form-group">
<?= $form->field($charguesd,'comprobante',[
    'template' => 'N de Comprobante <div class="input-group">{input}
           <label class="ml-5 efectivo " for="">Efectivo</label><input type="checkbox" class="ml-5 efectivo" id="efectivo"></div> {error}{hint}'
])->label("N de Comprobante")->textInput(['maxlength' => true, 'id' => 'compro']); ?>
<?=

$form->field($chargem,'Description')->label("Descripción")->textarea(['rows' => '6']);
?>


        </div>
    </div>
</div>
    <br/>
    <br/>
    <br/>

<div class="card">
    <div class="card-header">
        <table class="table">
            <thead>
            <tr class="thead-dark">
            <td>Documento</td>
            <td>Fecha de emisión</td>
            <td>Tipo de Documentos</td>
            <td>Valor</td>
            <td>Saldo</td>
                <td>Valor a pagar</td>
            </tr>
            </thead>
        <tbody>
       <tr>
           <td>
               <div class="input-group">
               <?= $form->field($header,'n_documentos')->textInput(["readonly"=>False,"value"=>$header->n_documentos])->label(false);?>

               </div>
           </td>
           <td><?=\Yii::$app->formatter->asDate($header->f_timestamp, 'dd/MM/yyyy') ?></td>
           <td>Factura</td>
           <td><?= $body->total ?></td>
           <?php if($upt==False){ ?>
           <td><?=$body->total?></td>
           <?php } else {?>
               <?php $d=$chargem::findOne(["n_document"=>$header->n_documentos]);
               $ac=$charguesd::find(["id_charge"=>$d->id])->orderBy([
                   'date' => SORT_DESC
               ])->one();

               ?>
           <td><?=$ac->saldo?></td>
           <?php } ?>
           <td><?= $form->field($charguesd,'amount',[
    'template' => '<div class="input-group">{input}
          <a class="btn btn-primary" id="copiar">Copiar</a></div> {error}{hint}'
])->label(false)->textInput(["id"=>"labec"]); ?></td>
       </tr>
        </tbody>
        </table>
    </div>
    <div class="card-body">

    </div>
</div>
    <br>
    <br>
    <br>
<?= HTML::tag("button","Guardar",["class"=>"btn btn-success"]) ?>
<?php ActiveForm::end()?>
        <?php
        $script = <<< JS
$('#efectivo').click(function() {
  if ($(this).is(':checked')) {
    $('#compro').val("efectivo")
  }
  else{
     $('#compro').val("")   
  }
});
$('#copiar').click(function(){
  
    
})
       $('#tipodocu').change(function(){
           actual=$(this).val();
           if(actual=="Caja"){
              $(".efectivo").show(); 
              c=$("#efectivo").is(":checked");
              console.log($('#compro').val());
             
              if ($("#efectivo").is(":checked")){
                  $('#compro').val("efectivo")
              }
              else{
                $('#compro').val("")  
              }
           }
           else{
               $(".efectivo").hide();
               $('#compro').val("")
           }
       })
JS;
        $this->registerJs($script);

        ?>
