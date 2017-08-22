<?php
use yii\helpers\Html;
use yii\bootstrap\Modal;
use yii\bootstrap\ActiveForm;
use yii\jui\DatePicker;
use slavkovrn\logvisitor\LogVisitorAsset;
$assets = LogVisitorAsset::register($this); 
Modal::begin([
        'options'=>[
            'id'=>"ip-popup",
            'style'=>"display:none"
        ]]);
Modal::end();
?>
<div class="LogVisitorModule-default-index">
    <div class="content">


        <div class="row">
            <div class="col-sm-10">

                <div class="form-group">
                    <?php $form = ActiveForm::begin([
                        'id' => 'activeform',
                        'layout' => 'horizontal',
                        'action' => '/logvisitor',
                        'fieldConfig' => [
                            'horizontalCssClasses' => [
                                'label' => 'col-sm-4',
                                'offset' => 'col-sm-offset-2',
                                'wrapper' => 'col-sm-8',
                            ],
                        ],
                    ]); 
                    ?>
                    <?= $form->field($model, 'dateFrom')->widget(DatePicker::classname(),[
                        'language' => (isset(Yii::$app->language))?Yii::$app->language:'en',
                        'dateFormat' => 'yyyy-MM-dd'
                    ]) ?>
                    <?= $form->field($model, 'dateTo')->widget(DatePicker::classname(),[
                        'language' => (isset(Yii::$app->language))?Yii::$app->language:'en',
                        'dateFormat' => 'yyyy-MM-dd'
                    ]) ?>
                    <?= $form->field($model, 'filterIp') ?>
                    <div class="form-group">
                        <div class="col-sm-4"></div>
                        <div class="col-sm-8">
                            <?= Yii::t('logvisitor','Comma separated substrings of IP to be filtered, begining from first position preg_match(/^needle/,haystack), for example 127.0.0.1') ?>
                        </div>
                    </div>
                    <?= $form->field($model, 'filterUri') ?>
                    <div class="form-group">
                        <div class="col-sm-4"></div>
                        <div class="col-sm-8">
                            <?= Yii::t('logvisitor','Comma separated substrings of URI to be filtered') ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-4"></div>
                        <?= Html::submitButton(Yii::t('logvisitor', 'Show'), ['id'=>'order','class'=>'btn btn-success']) ?>
                    </div>
                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>

        <div class="row">
            <div id="widget" class="col-sm-12">
                <?= $this->render('_widget',compact('model')) ?>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="table-responsive"> 
                <table class="table table table-striped table-bordered table-hover table-condensed">
                    <caption><h3>UNIQUE IP,URI,count()</h3></caption>
                    <thead>
                        <tr class="success">
                            <th>IP</th>
                            <th>URI</th>
                            <th>count()</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($model->ip_uri as $key=>$val) : ?>
                            <tr>
                                <td><?= $val['ip'] ?></td>
                                <td><?= $val['uri'] ?></td>
                                <td><?= $val['count(ip)'] ?></td>
                                <td>
                                    <img id="loader<?= $val['id'] ?>" src="<?= $assets->baseUrl.'/images/loader.gif' ?>" style="display:none" />
                                    <a data-id="<?= $val['id'] ?>" data-ip="<?= $val['ip'] ?>" class="btn btn-success whois" style="margin:3px">Whois IP</button></div>
                                    <a data-id="<?= $val['id'] ?>" data-ip="<?= $val['ip'] ?>" data-uri="<?= $val['uri'] ?>" class="btn btn-success chart" style="margin:3px;"><?= Yii::t('logvisitor','Chart') ?></button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
$this->registerJs("
    $('.whois').click(function(){
        var ip=$(this).data('ip');
        var dateTo=$('#logvisitormodel-dateto').val();
        var id=$(this).data('id');
        $('#loader'+id).show();        
		$.ajax({
			url:'/logvisitor/default/whois',
			type: 'post',
            dataType:'json',
            data:{'ip':ip},
			success: function(data) {
                $('#loader'+id).hide();        
                $('#ip-popup .modal-body').html(data.info);
                $('#ip-popup').modal('toggle');
 			}
		});
    });
    $('.chart').click(function(){
        var ip=$(this).data('ip');
        var uri=$(this).data('uri');
        var dateFrom=$('#logvisitormodel-datefrom').val();
        var dateTo=$('#logvisitormodel-dateto').val();
        var id=$(this).data('id');
        $('#loader'+id).show();        
		$.ajax({
			url:'/logvisitor/default/chart',
			type: 'post',
            data:{
                'LogVisitorModel[ip]':ip,
                'LogVisitorModel[uri]':uri,
                'LogVisitorModel[dateFrom]':dateFrom,
                'LogVisitorModel[dateTo]':dateTo,
            },
			success: function(data) {
                $('#loader'+id).hide();        
                $('#widget').html(data);
                if ($('#graphic_table_".$model->graphic_id." > table').length)
    			    $('#graphic_table_".$model->graphic_id." > table')
                        .visualize({type:'line', width:'".$model->graphic_width."px',height:'".$model->graphic_height."px'})
    			        .appendTo('#".$model->graphic_id."').trigger('visualizeRefresh');
 			}
		});
    });
",$this::POS_READY);
?>