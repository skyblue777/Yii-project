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

Yii::import('Core.extensions.base.FException');
class FErrorHandler extends CErrorHandler
{
    //Severity level
    const NO_ERROR = 0;
    const GENERIC_ERROR = 1;
    
    const CAN_RETRY_CONTINUE = 10;
    const CAN_RETRY_STOP_USER_SERVICE = 101;
    const CAN_RETRY_STOP_USER_SESSION = 102;
    
    const NO_RETRY_CONTINUE = 20;            
    const NO_RETRY_STOP_USER_SERVICE = 201;
    const NO_RETRY_STOP_USER_SESSION = 202;
    
    const EXTERNAL_SERVICE_ERROR = 30;
    
    const ALERT_USER_ATTACK = 500;
    const USER_ATTACK = 501;
    
    const APPLICATION_SHUT_DOWN = 1000;
    
    public static $mode;
      
    
    //Error stack
    private $errors = array();
    //Current process highest error severity level
    private $severity = self::NO_ERROR;
    
    public $sendNotificationEmail = true;
    public $sendNotificationTo = '';
    public $excludedCodes = array();

    
    public function setmode($value){
        self::$mode = $value;
    }
    
    public function init(){
        parent::init();
        if (Yii::app()->controller instanceof BackOfficeController)
            $this->errorAction = 'Core/BackOffice/error';
        else
            $this->errorAction = 'Core/Front/error';
    }
    
    /**
    * Log error/exception into the stack for later handling
    * 
    * @param FException $error. USER_EXCEPTION (level 3) error will be dumped to user when current controll calls showUserError()
    * @param mixed $severity 
    */
    public static function logError($error, $severity = FErrorHandler::GENERIC_ERROR){
        if (!is_a($error, 'FException') && !is_string($error))
            $error = print_r($error, true);

        if (is_string($error))
            $error = new FException($error, FException::USER_EXCEPTION);
        
        $appErrorHandler = Yii::app()->getErrorHandler();
        $appErrorHandler->errors[] = $error;
        //Log error with user information
        Yii::log($error->getErrorMessage(), 'error');
        
        $appErrorHandler->setSeverity($severity);
    }
    
    /**
    * Log an array of error/exception into the stack for later handling
    * 
    * @param array $errors
    * @param mixed $severity
    */
    public static function logErrors($errors, $severity = FErrorHandler::GENERIC_ERROR){
        foreach($errors as $error)
            self::logError($error, $severity);
    }
    
    /**
    * Get all errors logged
    * @return array
    */
    public static function getErrors(){
        return Yii::app()->getErrorHandler()->errors;
    }
    
    /**
    * Check if there is any error in the application
    */
    public static function hasErrors() {
        return count(Yii::app()->getErrorHandler()->errors) > 0;
    }
    
    /**
    * Get exceptions to display to user
    * 
    * While application in development mode, all logged exceptions will be returned
    * 
    */
    public static function getUserExceptions(){
        if (self::$mode == 'debug')
            return Yii::app()->getErrorHandler()->errors;
            
        $errors = Yii::app()->getErrorHandler()->errors;
        $userErrors = array();
        $c = count($errors);
        for($i = 0; $i < $c; $i++){
            $e = $errors[$i];
            if ($e instanceof FException && $e->getCategory() == FException::USER_EXCEPTION){
                $userErrors[] = $e;
            }
        }
        return $userErrors;
    }
    
    public function setSeverity($severity){
        if ($this->severity >= $severity)
            return;
            
        $this->severity = $severity;
        $this->safeUser();
    }
    
    /**
    * protect user from losing data and defend if attack is detected
    * 
    */
    protected function safeUser(){
        switch($this->severity){
            case self::CAN_RETRY_STOP_USER_SERVICE:
                $this->processErrorStack();
                break;
            case self::CAN_RETRY_STOP_USER_SESSION:
                $this->processErrorStack();
                $this->logUserOut();
                break;
            case self::NO_RETRY_STOP_USER_SERVICE:
                $this->processErrorStack();
                break;
            case self::NO_RETRY_STOP_USER_SESSION:
                $this->processErrorStack();
                $this->logUserOut();
                break;
            //TODO: For possible attack detected or detected acttack, blacklist user and process app defend
            default:
                break;
        } 
    }
    
    /**
    * Process error stack and do reporting
    * 
    */
    public function processErrorStack(){
        if ($this->severity == self::NO_ERROR) return;
        if (Yii::app() instanceof CApplicationComponent){
            foreach($this->errors as $e){
                //Send email if needed
            }        
        }elseif(Yii::app() instanceof CConsoleApplication){
            echo "Error(s): \n";
            foreach($this->errors as $e){
                echo $e->getErrorMessage()."\n";
            }                
        }
    }
    
    /**
    * Override CErrorHandler handle event to process error stack
    * 
    * @param mixed $event
    */
    public function handle($event){
        // Process the error stack first
        $this->processErrorStack();
        if (YII_DEBUG) {
            // try to write out whatever is bufferred
            while(@ob_end_flush());
        } else {
            // get all bufferred content to send in the email
            $output = '';
            while(($buffer = @ob_get_flush()) !== false)
                $output .= "\n".$buffer;
        }
            
        
        // Handle error event as usual
        parent::handle($event);

        // Send email to admin if debug is turn off
        if (YII_DEBUG == false && $this->sendNotificationEmail == true)
        {
            if (array_search($this->Error['code'], $this->excludedCodes) !== false) return;
            
            try {
                Yii::import('application.modules.Core.extensions.vendors.mail.YiiMailMessage');
                Yii::app()->mail->viewPath = 'application.modules.Core.views.mails';
                //send mail alert password changed
                $message = new YiiMailMessage;
                $message->view = 'error_notification';
                $message->setSubject('Error happens on '.Settings::SITE_NAME.' ('.Yii::app()->request->getHostInfo().')');

                /**
                * @var CHttpException
                */
                $message->setBody(array(
                    'error' =>$this->Error,
                    'output' => $output,
                ));
                
                if (!empty($this->sendNotificationTo))
                    $message->addTo($this->sendNotificationTo);
                else
                    $message->addTo(Settings::ADMIN_EMAIL);
                $message->setFrom(Settings::ADMIN_EMAIL);
                
                Yii::app()->mail->send($message);
            } catch(Exception $ex){
                // well, there is really nothing we can do here !!!
                throw $ex;
            }
        }
    }
    
    
}
?>
