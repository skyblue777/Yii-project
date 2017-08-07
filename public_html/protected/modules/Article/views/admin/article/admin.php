<?php
$this->breadcrumbs=array(
	Language::t(Yii::app()->language,'Backend.Common.Menu','Static Pages')=>array('index'),
	Language::t(Yii::app()->language,'Backend.Common.Menu','Manage'),
);

$this->menu=array(
	array('label'=>Language::t(Yii::app()->language,'Backend.Article.Admin','Create Article'), 'url'=>array('create')),
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('article-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1><?php echo Language::t(Yii::app()->language,'Backend.Article.Admin','Manage Static Pages')?></h1>
<!--
<p>
You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>
or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.
</p>
-->

<?php //echo CHtml::link('Advanced Search','#',array('class'=>'search-button')); ?>
<div class="search-form" style="display:none">
<?php $this->renderPartial('_search',array(
	'model'=>$model,
)); ?>
</div>
<!-- search-form -->
<p><strong><em><?php echo Language::t(Yii::app()->language,'Backend.Common.Common','Notes:')?></em></strong><br />
<?php echo Language::t(Yii::app()->language,'Backend.Article.Admin','Notes')?>
</p>
<?php 
Yii::import('Language.models.LanguageForm');
$criteria=new CDbCriteria;
$criteria->compare('parent_id', Settings::STATIC_PAGE_ROOT_CATEGORY);
$criteria->order = 'ordering';
$list = Category::model()->findAll($criteria);
$list_cat=array();
foreach ($list as $cat){
	$list_cat[$cat->id]=Language::t(Yii::app()->language,'Backend.Article.Admin',$cat->title);
}
?>
<?php $grid = $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'article-grid',
	'dataProvider'=>$dataSource,
	'filter'=>$model,
    'selectableRows'=>2,
    'selectionChanged'=>"updateSelectors",
	'columns'=>array(
        array(
            'class'=>'CCheckBoxColumn',
            'value'=>'$data->id',
            'htmlOptions'=>array('width'=>'3%'),
        ),
		array(
            'name'=>'id',
            'htmlOptions'=>array('width' => '5%'),
        ),
        array(
            'name'=>'lang',
        	'value'=>'Language::getLabel_language($data->lang)',
        	'filter'=>CHtml::activeDropDownList($model, 'lang', LanguageForm::getList_languages_exist(),array('prompt' => '--- '.Language::t(Yii::app()->language,'Backend.Common.Common','All').' ---')),
            'htmlOptions'=>array('width' => '15%'),
        ),
		array(
            'name'=>'category_id',
            'value'=>'$data->category->title',
            'filter'=>CHtml::activeDropDownList($model, 'category_id', $list_cat,array('prompt' => '--- '.Language::t(Yii::app()->language,'Backend.Common.Common','All').' ---')),
            'htmlOptions'=>array('width' => '15%'),
        ),
		array(
            'name'=>'title',
            'value'=>'CHtml::link($data->title, url("update",array("id" => $data->id))) . "<br />" . $data->alias',
            'type'=>'raw',
            'htmlOptions'=>array(),
        ),
		array(
            'name'=>'author_name',
            'value'=>'$data->author->username',
        ),
        array(
            'name'=>'status',
            'value'=>'"<a href=\"#\" class=\"set-status\" id=\"".$data->id."\">".
                       ($data->status == Article::STATUS_ACTIVE ? CHtml::image(themeUrl()."/images/tick.png","active") : CHtml::image(themeUrl()."/images/error.png","inactive")).
                       "</a>"',
            'type'=>'raw',
            'filter'=>CHtml::activeDropDownList($model, 'status', Lookup::items('status'),array('prompt' => '--- '.Language::t(Yii::app()->language,'Backend.Common.Common','All').' ---')),
            'htmlOptions' => array('align' => 'center')
        ),
		/*
        'leading_text',
        'content',
		'photo',
		'tags',
		'create_time',
		'update_time',
		'author_id',
		*/
		array(
			'class'=>'CButtonColumn',
            'template' => '{update} {delete}',
		),
	),
)); 

if ($grid->dataProvider->ItemCount) {
    $this->menu[] = array('label' => Language::t(Yii::app()->language,'Backend.Common.Common','Delete selected items'), 'url'=>$this->createUrl('delete'), 'linkOptions' => array('onclick'=>'return multipleDelete("article-grid",this.href)'));
}
Yii::app()->clientScript->registerScriptFile(Yii::app()->core->AssetUrl.'/scripts/gridview.js', CClientScript::POS_BEGIN);
?>

<?php
$script = '
    $("a.set-status").live("click", function(){
        status = $(this).find("img").attr("alt") == "active" ? "inactive" : "active";
        $.ajax({
            url:"'.serviceUrl('Article.ArticleAPI.setStatus', 'ajax').'",
            type: "get",
            data: {"status": status, "id": $(this).attr("id")},
            success: function(){
                $.fn.yiiGridView.update("article-grid");
            }
        });
        return false;
    })
';
cs()->registerScript('set-status', $script, CClientScript::POS_READY);
?>