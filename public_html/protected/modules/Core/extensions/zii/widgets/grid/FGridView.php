<?php
Yii::import('zii.widgets.grid.CGridView');
class FGridView extends CGridView
{
    public $pagerCssClass='Pager';
    public $itemsCssClass='items';
    public $selectableRows=2;
    
    public $bulkOptions=array();
    
    /**
     * Initializes the grid view.
     * This method will initialize required property values and instantiate {@link columns} objects.
     */
    public function init()
    {
        if($this->baseScriptUrl===null)
            $this->baseScriptUrl=Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('Core.extensions.zii.widgets.grid.assets'));
        parent::init();
    }

    public function renderItems()
    {
        parent::renderItems();
        if($this->dataProvider->getItemCount()>0)
        {
            $this->renderBulk();
        }
    }
    
    public function renderBulk() {
        $this->bulkOptions = CMap::mergeArray($this->bulkOptions, array('grid'=>$this));
        $this->widget('Core.extensions.web.widgets.BulkAction', $this->bulkOptions);
    }

    /**
     * Renders the table header.
     */
    public function renderTableHeader()
    {
        if(!$this->hideHeader)
        {
            echo "<thead>\n";

            if($this->filterPosition===self::FILTER_POS_HEADER)
                $this->renderFilter();

            echo "<tr>\n";
            $index=1;
            $total=count($this->columns);
            foreach($this->columns as $column){
                $class = '';
                if (isset($column->headerHtmlOptions['class'])) {
                    $class = $column->headerHtmlOptions['class'];
                }
                if (empty($class) === false) $class .= ' ';
                if ($index === 1)
                     $column->headerHtmlOptions['class'] = $class.'first';
                elseif ($index === $total)
                    $column->headerHtmlOptions['class'] = $class.'last';
                $index++;
                $column->renderHeaderCell();
            }
            echo "</tr>\n";

            if($this->filterPosition===self::FILTER_POS_BODY)
                $this->renderFilter();

            echo "</thead>\n";
        }
        else if($this->filter!==null && ($this->filterPosition===self::FILTER_POS_HEADER || $this->filterPosition===self::FILTER_POS_BODY))
        {
            echo "<thead>\n";
            $this->renderFilter();
            echo "</thead>\n";
        }
    }

    /**
     * Registers necessary client scripts.
     */
    public function registerClientScript()
    {
        parent::registerClientScript();
        $cs=Yii::app()->getClientScript();
        $cs->registerScriptFile($this->baseScriptUrl.'/jquery.ajaxmanager.js');
        $js="$('.grid-view table.view tbody tr').live('mouseover',function(){
    $('.row-actions', this).css('visibility', 'visible');
});
$('.grid-view table.view tbody tr').live('mouseout',function(){
    $('.row-actions', this).css('visibility', 'hidden');
});";
		Yii::app()->getClientScript()->registerScript(__CLASS__.'#Actions', $js);
    }
}
