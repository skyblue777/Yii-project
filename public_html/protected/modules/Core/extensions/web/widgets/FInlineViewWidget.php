<?php
/**
-------------------------
GNU GPL COPYRIGHT NOTICES
-------------------------
This file is part of FlexicaCMS.

FlexicaCMS is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

FlexicaCMS is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with FlexicaCMS.  If not, see <http://www.gnu.org/licenses/>.*/

/**
 * $Id$
 *
 * @author FlexicaCMS team <contact@flexicacms.com>
 * @link http://www.flexicacms.com/
 * @copyright Copyright &copy; 2009-2010 Gia Han Online Solutions Ltd.
 * @license http://www.flexicacms.com/license.html
 */

/**
* InlineViewWidget use code between begin widget tag and end widget tag
* to render widget content instead of using a separated view file as normal
* Yii widgets. In order to use this type of widgets, the application must
* utilize a view renderer. By default the widget works with PradoViewRenderer.
* 
* Inside the widget block (opened by begin widget and closed by end widget
* tags) you can use following syntax for PHP code:
* 
* {expression} => <?php echo expression ?>
* 
* <{statement}> => <?php statement ?>
* 
* If you want to use other ViewRenderer class than PradoViewRenderer, you will
* have to write code to 'translate' above syntax to your view renderer syntax.
*/
class FInlineViewWidget extends FWidget
{
    /**
    * If a view renderer class is used but not CPradoViewRenderer
    * this widget must be extended with the $rendererClass set to
    * the new class name. Also, you have to override function 
    * inlineToViewRenderer() to translate some special syntax.
    */
    protected $rendererClass='CPradoViewRenderer';
    
    public function init(){
        parent::init();
        ob_start();
        ob_implicit_flush(false);
    }

    /* Renders a view.
     *
     * The named view refers to a PHP script (resolved via {@link getViewFile})
     * that is included by this method. If $data is an associative array,
     * it will be extracted as PHP variables and made available to the script.
     *
     * @param array $data data to be extracted into PHP variables and made available to the view script
     * @param boolean $return whether the rendering result should be returned instead of being displayed to end users
     * @return string the rendering result. Null if the rendering result is not required.
     * @throws CException if the view does not exist
     * @see getViewFile
     */
    public function render($data=null,$return=false)
    {
        $hostView = $this->controller->CurrentViewFile;
        $viewFile = get_class($this).'_'.$this->getId().'.php';
        $viewFile = $this->getInlineViewFile($hostView, $viewFile);
        
        if (YII_DEBUG)
        {
            $viewCode = ob_get_clean();
            $viewCode = $this->inlineToViewRenderer($viewCode);
            file_put_contents($viewFile,$viewCode);
        }
        
        if (!Yii::app()->viewRenderer){
            $renderer = new $this->rendererClass;
            $output=$renderer->renderFile($this,$viewFile,$data,$return);
            if ($return)
                return $output;
            else
                echo $output;
        } else {
            return $this->renderFile($viewFile,$data,$return);
        }
    }
    
    /**
     * Generates the resulting view file path.
     * @param string $file source view file path
     * @return string resulting view file path
     */
    protected function getInlineViewFile($host, $file)
    {
        $crc=sprintf('%x', crc32(get_class(Yii::app()->getViewRenderer()).Yii::getVersion().dirname($host)));
        $viewFile=Yii::app()->getRuntimePath().'/views/'.$crc.'/'.basename($file);
        if(!is_file($viewFile))
            @mkdir(dirname($viewFile),0755,true);
        return $viewFile;
    }
    
    /**
    * Translate inline view widget syntax into PradoViewRenderer's syntax
    * 
    * @param mixed $code
    * @return mixed
    */
    protected function inlineToViewRenderer($code){
        //php tag <% php code %>
        $code = str_replace('<{','<%',$code);
        $code = str_replace('}>','%>',$code);
        
        //echo <%= php expression %>
        $code = str_replace('{','<%=',$code);
        $code = str_replace('}','%>',$code);
        
        return $code;
    }
}