<?php
use yii\helpers\Html;
use yii\bootstrap\Modal;
use yii\bootstrap\ActiveForm;
use yii\jui\DatePicker;
?>
<?php
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
                <?= $this->render('_widget',compact('graphic_id','graphic_width','graphic_height','graphic_chart')) ?>
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
                        <?php foreach ($ip_uri as $key=>$val) : ?>
                            <tr>
                                <td><?= $val['ip'] ?></td>
                                <td><?= $val['uri'] ?></td>
                                <td><?= $val['count(ip)'] ?></td>
                                <td>
                                    <a data-ip="<?= $val['ip'] ?>" class="btn btn-success whois" style="margin:3px">Whois IP</button></div>
                                    <a data-ip="<?= $val['ip'] ?>" data-uri="<?= $val['uri'] ?>" class="btn btn-success chart" style="margin:3px;"><?= Yii::t('logvisitor','Chart') ?></button>
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
		$.ajax({
			url:'/logvisitor/default/whois',
			type: 'post',
            dataType:'json',
            data:{'ip':ip},
			success: function(data) {
                $('#ip-popup .modal-body').html(data.info);
                $('#ip-popup').modal('toggle');
 			}
		});
    });
    $('.chart').click(function(){
        var ip=$(this).data('ip');
        var uri=$(this).data('uri');
        var dateFrom=$('#logvisitorform-datefrom').val();
        var dateTo=$('#logvisitorform-dateto').val();
		$.ajax({
			url:'/logvisitor/default/chart',
			type: 'post',
            data:{
                'ip':ip,
                'uri':uri,
                'dateFrom':dateFrom,
                'dateTo':dateTo,
            },
			success: function(data) {
                $('#widget').html(data);
                if ($('#graphic_table_".$graphic_id." > table').length)
    			    $('#graphic_table_".$graphic_id." > table')
                        .visualize({type:'line', width:'".$graphic_width."px',height:'".$graphic_height."px'})
    			        .appendTo('#".$graphic_id."').trigger('visualizeRefresh');
 			}
		});
    });
",$this::POS_READY);
?>