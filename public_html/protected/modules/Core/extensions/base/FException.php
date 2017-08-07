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

class FException extends CException
{
    //Exception time, in y/m/d format
    private $time;    
    //Exception category
    private $category;    
    //Class reports the exception
    private $senderClass;
    //Resolution code
    private $resolution;    
    
    const BUGS = 0;
    const DEVELOPER_EXCEPTION = 1;
    const ADMIN_EXCEPTION = 2;
    const USER_EXCEPTION = 3;
    
    
    public function __construct($message, $category = 1, &$sender = null, $resolution = null){
        parent::__construct();
        $this->message= $message;
        $this->category = $category;
        $this->time = date('Y/m/d h:i:s', time());
        
        if (!is_null($sender))
            $this->senderClass = get_class($sender);
            
        if (!is_null($resolution))
            $this->resolution = $resolution;
    }
    
    /**
    * Get detailed exception message for non user error
    * For user error, return only the message for friendly dipslay to user
    */
    public function getErrorMessage(){
        $categoryArray=array('Bugs','Code','Management','User');
    
        if ($this->category != self::USER_EXCEPTION)
            return $this->time."\t".$categoryArray[$this->category]."\t\t".$this->senderClass.":\n".$this->message."\n";
        else
            return $this->message;
    }
    
    /**
    * Return error category
    * 
    */
    public function getCategory(){
        return $this->category;
    }
    
    /**
    * Return a resolution code that can be translated into text message to help resolve the issue
    * 
    */
    public function getResolution(){
        return $this->resolution;
    }
}
?>
