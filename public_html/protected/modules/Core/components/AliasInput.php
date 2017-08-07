<?php
class AliasInput extends CInputWidget
{
    /**
    * @var string column name of the title field
    */
    public $titleField = 'title';
       
    public function run() {
        if ($this->model) {
            $class = get_class($this->model);
            echo CHtml::activeTextField($this->model, $this->attribute, $this->htmlOptions);
        } else {
            $class = '';
            $this->attribute = $this->name;
            echo CHtml::textField($this->attribute, $this->value, $this->htmlOptions);
        }
        
        $script = "
        var title = $('#".$class."_".$this->titleField."');
        title.click(function(){
            title.data('value', title.val());
        });

        title.blur(function(){
            var text = $(this).val();
            if (text == title.data('value')) return;
            var alias = text
                    .toLowerCase() // change everything to lowercase
                    .replace(/^\s+|\s+$/g, '') // trim leading and trailing spaces        
                    .replace(/[_|\s]+/g, '-') // change all spaces and underscores to a hyphen
                    .replace(/[^a-z0-9-]+/g, '') // remove all non-alphanumeric characters except the hyphen
                    .replace(/[-]+/g, '-') // replace multiple instances of the hyphen with a single instance
                    .replace(/^-+|-+$/g, '') // trim leading and trailing hyphens                
                    ;
            $('#".$class."_".$this->attribute."').val(alias);
        });
        ";
        
        cs()->registerScript('title-to-slug',$script, CClientScript::POS_READY);        
    }
}
?>
