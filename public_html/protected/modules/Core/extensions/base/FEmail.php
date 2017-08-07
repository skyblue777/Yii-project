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
 
class FEmail
{
    /**
    * Html template which should be stored in assets/emailtemplates
    * 
    * @var string
    */
    public $template;
    
    /**
    * Params in the template
    * 
    * @var array
    */
    public $params;
    
    /**
    * Email subject
    * 
    * @var string
    */
    public $subject;
    
    /**
    * From address
    * 
    * @var string
    */
    public $from;

    /**
    * To addresses
    *     
    * @var string
    */
    public $to;
    
    /**
    * Cc addresses
    * 
    * @var string
    */
    public $cc;
    
    /**
    * Bcc addresses
    * 
    * @var string
    */
    public $bcc;
    
    /**
    * Email body
    * 
    * @var string
    */
    public $body;
    
    /**
    * Email attachments
    * 
    * @var array
    */
    public $attachments;
    
    /**
    * Batch send flag
    * 
    * @var bool
    */
    public $isBatchSend = false;
    
    /**
    * Send mail method which is smtp, sendmail or mail
    * 
    * @var mixed
    */
    public $sendMailMethod = 'smtp';
    
    /**
    * Email content type
    * 
    * @var mixed
    */
    public $contentType = 'text/html';
    
    public function __construct()
    {
        
    }
    
    public function send(){
        // If the body is not set manually, try to parse the template
        if (empty($this->body))
        {
            $this->body = $this->parseTemplate();
        }
        
        if ($this->body === null)
            return false;
        else
        {
            //Send immediately
            require_once(dirname(__FILE__).'/../vendors/SwiftMailer/swift_required.php');
            
            switch ($this->sendMailMethod){
                case 'smtp':
                    $transport = Swift_SmtpTransport::newInstance();
                    $transport->setHost(Settings::SMTP_HOST);
                    $transport->setPort(Settings::SMTP_PORT);
                    $transport->setEncryption(Settings::SMTP_SECURE);
                    $transport->setUsername(Settings::SMTP_USERNAME);
                    $transport->setPassword(Settings::SMTP_PASSWORD);
                    break;
                case 'sendmail':
                    $transport = Swift_SendmailTransport::newInstance('/usr/sbin/exim -bs');
                    break;
                case 'mail':
                    $transport = Swift_SendmailTransport::newInstance();
                default:
                    break;
            }
            
            $mailer = Swift_Mailer::newInstance($transport);
            $message = Swift_Message::newInstance();
            if (! empty($this->contentType))
            {
                $message->setContentType($this->contentType);
            }
            $message->setFrom(array(Settings::MAIL_SERDER_ADDRESS => Settings::MAIL_SENDER_NAME));
            $message->setSubject($this->subject);
            
            $message->setBody($this->body);

            if (is_array($this->attachments))
                foreach($this->attachments as $file)
                    $message->attach(Swift_Attachment::fromPath($file));

            $this->addMailAddresses($message, $this->to);
            $this->addMailAddresses($message, $this->cc, 'cc');
            $this->addMailAddresses($message, $this->bcc, 'bcc');
            
            if ($this->isBatchSend)
                $result = $mailer->batchSend($message);
            else
                $result = $mailer->send($message);
                
            return $result;
        }
    }
    
    /**
    * Parse an email template and replace variables with values provided in params
    *
    * @param mixed $template relative template path, from resource/emails folder
    * @param mixed $params associative array of values to fill the template
    * @return mixed
    */
    public function parseTemplate(){
        $template = $this->template;
        $template = Yii::app()->assetManager->basePath . '/emailtemplates/'.$template.'.html';
        if (!file_exists($template) || ($body = file_get_contents($template))=== false){
            FErrorHandler::logError('Cannot parse email template. The email template [' . $template . '] might not exist.');
            return null;
        }

        $body = preg_replace_callback('/\{[\w|-]*\}/',
                array($this, 'replaceParams'),
                $body);

        return $body;
    }
    
    private function replaceParams($matches){
        $var = substr($matches[0],1,strlen($matches[0])-2);
        return $this->params[$var];
    }
    
    /**
    * Add email addresses
    *
    * @param Swift_Message $mail
    * @param string $addresses
    * @param string $field Address field, can be 'to','cc','bcc'
    */
    public function addMailAddresses(&$mail, $addresses, $field = 'to'){
        if (trim($addresses) == '') return;
        $addrs = explode(';', $addresses);
        foreach($addrs as $addr){
            //Reformat standar email "name" <email> to name,email
            $addr = str_replace('" <','"<',$addr);
            $addr = str_replace('"<',',',$addr);
            $addr = str_replace('"','',$addr);
            $addr = str_replace('>','',$addr);

            //Fix address format: name,email
            if(strpos($addr, ',') === false)
                $addr = ','.$addr;

            $tmp = explode(',', $addr);
            switch($field){
                case 'cc':
                    $mail->addCc($tmp[1], $tmp[0]);
                    break;
                case 'bcc':
                    $mail->addBcc($tmp[1], $tmp[0]);
                    break;
                default:
                    $mail->addTo($tmp[1], $tmp[0]);
                    break;
            }
        }
    }
}
?>