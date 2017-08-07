<?php
Yii::import('zii.widgets.grid.CCheckBoxColumn');
class FCheckBoxColumn extends CCheckBoxColumn
{  
	public function init()
	{
		$name="{$this->id}\\[\\]";
		if($this->grid->selectableRows==1)
			$one="\n\tjQuery(\"input:not(#\"+$(this).attr('id')+\")[name='$name']\").attr('checked',false);";
		else
			$one='';
		$js=<<<EOD
jQuery('#{$this->id}_all').live('click',function() {
	var checked=this.checked;
	jQuery("input[name='$name']:enabled").each(function() {
		this.checked=checked;
	});
});
jQuery("input[name='$name']").live('click', function() {
	jQuery('#{$this->id}_all').attr('checked', jQuery("input[name='$name']").length==jQuery("input[name='$name'][checked=true]").length);{$one}
});
EOD;
		Yii::app()->getClientScript()->registerScript(__CLASS__.'#'.$this->id,$js);
	}
}
?>