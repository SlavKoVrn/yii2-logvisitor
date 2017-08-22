<?php
use slavkovrn\visualize\VisualizeWidget;
$this->registerCss("
.visualize-labels-x li span.label, .visualize-labels-y li span.label
{
    font-size: 0.9em;
}
");
?>
<?= VisualizeWidget::widget([
    'class' => 'graphic',   // Class to define stile
    'style' => 'light',     // Style of widget (only 'dark' or 'light' option)
    'id' => $model->graphic_id,      // Id of visualize widget should be unique at page
    'name' => $model->graphic_name,  // Name of visualize widget
    'width' => $model->graphic_width, // Width of widget in pixels
    'height' => $model->graphic_height, // Height of widget in pixels
    'graphic' => $model->graphic_chart, // Data of chart
]) ?>
