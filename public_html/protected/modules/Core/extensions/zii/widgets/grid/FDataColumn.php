<?php
Yii::import('zii.widgets.grid.CDataColumn');
Yii::import('application.modules.Core.extensions.zii.widgets.grid.FButtonRow');
class FDataColumn extends CDataColumn
{
    /**
    * create instance of FButtonRow {@link FButtonRow}
    *
    * @var mixed
    */
    public $rowActions=array();
    
    /**
    * Just for calendar
    * 
    * @var mixed
    */
    public $maxCreatedDateYear = null;
    public $minCreatedDateYear = null;
    public $ifFormat = '%Y-%m-%d';

    /**
     * Renders the data cell content.
     * This method renders the view, update and delete buttons in the data cell.
     * @param integer the row number (zero-based)
     * @param mixed the data associated with the row
     */
    protected function renderDataCellContent($row,$data)
    {
        parent::renderDataCellContent($row,$data);
        //render row-actions
        $config = CMap::mergeArray(array('class'=>'FButtonRow', 'htmlOptions'=>array('class'=>'row-actions')), $this->rowActions);
        $rowActions = Yii::createComponent($config, $this->grid);
        $rowActions->init();
        $rowActions->renderDataCell($row);
    }

    /**
	 * Renders the filter cell content.
	 * This method will render the {@link filter} as is if it is a string.
	 * If {@link filter} is an array, it is assumed to be a list of options, and a dropdown selector will be rendered.
	 * Otherwise if {@link filter} is not false, a text field is rendered.
	 * @since 1.1.1
	 */
	protected function renderFilterCellContent()
	{
		if($this->filter!==false && $this->grid->filter!==null && strpos($this->name,'.')===false)
		{
			if ($this->filter == 'calendar')
			{
				$txtFilterName = "txt{$this->name}Filter";
				echo CHtml::activeTextField($this->grid->filter, $this->name, array('id'=> $txtFilterName,'style'=>'width: 80%;'));
				$btnFilter = "btn{$this->name}Filter";
				echo CHtml::image(Yii::app()->theme->baseUrl . '/images/ico-calendar.gif', 'calendar', array('id' => $btnFilter,'align'=>'top','style'=>'margin-left: 2px;'));
				$script_onSelect = "function(cal){
								    	var p = cal.params;
										var update = (cal.dateClicked || p.electric);
										if (update && p.inputField) {
											p.inputField.value = cal.date.print(p.ifFormat);
											if (typeof p.inputField.onchange == 'function')
												p.inputField.onchange();
										}
										if (update && p.displayArea)
											p.displayArea.innerHTML = cal.date.print(p.daFormat);
										if (update && typeof p.onUpdate == 'function')
											p.onUpdate(cal);
										if (update && p.flat) {
											if (typeof p.flatCallback == 'function')
												p.flatCallback(cal);
										}
										if (update && p.singleClick && cal.dateClicked)
											cal.callCloseHandler();
										var settings = $.extend({}, $.fn.yiiGridView.defaults);
										var inputSelector='#{$this->grid->id} .'+settings.filterClass+' input, '+'#{$this->grid->id} .'+settings.filterClass+' select';
										var data = $.param($(inputSelector))+'&ajax={$this->grid->id}';
										$.fn.yiiGridView.update('{$this->grid->id}', {data: data});
									}";
				$options = array('inputField' => $txtFilterName,
									'button' => $btnFilter,
									'stylesheet' => 'blue',
									'ifFormat' => $this->ifFormat,
									'OnSelect' => $script_onSelect,
								);
				$js = "\$(document).ajaxComplete(function(){
								\$.manageAjax.clearCache();
								Calendar.setup(
							    {
					    			inputField      : '{$txtFilterName}',
									button          : '{$btnFilter}',
									ifFormat        : '{$this->ifFormat}',
									onSelect		: {$script_onSelect}
							    }
							  );
							});
						";
				if (!is_null($this->minCreatedDateYear) && !is_null($this->maxCreatedDateYear))
				{
					$options['range'] = "[{$this->minCreatedDateYear}, {$this->maxCreatedDateYear}]";
					$js = "\$(document).ajaxComplete(function(){
									\$.manageAjax.clearCache();
									Calendar.setup(
								    {
					    				inputField      : '{$txtFilterName}',
										button          : '{$btnFilter}',
										ifFormat        : '{$this->ifFormat}',
										range           : '[{$this->minCreatedDateYear}, {$this->maxCreatedDateYear}]',
										onSelect		: {$script_onSelect}
								    }
								  );
								});
							";
				}
				Yii::app()->controller->widget('Core.extensions.gui.calendar.SCalendar', $options);

				
				$cs = Yii::app()->ClientScript;
				$cs->registerScript('setup_calendar_afterajax', $js, CClientScript::POS_END);
			}
			else
			{
				if(is_array($this->filter))
					echo CHtml::activeDropDownList($this->grid->filter, $this->name, $this->filter, array('id'=>false,'prompt'=>''));
				else if($this->filter===null)
					echo CHtml::activeTextField($this->grid->filter, $this->name, array('id'=>false));
				else
					echo $this->filter;
			}
		}
		else
		{
			echo '&nbsp;';
		}
	}
}