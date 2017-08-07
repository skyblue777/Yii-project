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
class ServiceController extends FController 
{
    /**
    * Run only services whose names begins with 'cmd'
    * 
    * @param mixed $SID string Service ID
    */
    public function actionCommand() {
        $SID = $this->get('SID','');
        //check the service ID
        $tmp = explode('.', $SID);
        if (count($tmp) != 3)
            throw new CHttpException(400, 'Service not found.');
        if (substr($tmp[2],0,3) != 'cmd')
            throw new CHttpException(400, 'Cannot execute this command.');
        
        $result = FSM::run($SID, $_GET);
        if (isset($_GET['returnUrl']))
            $this->redirect($_GET['returnUrl']);
    }
    
    /**
    * Serve a RESTful web service request
    */
    public function actionIndex() {
        //TODO: Use http://code.google.com/p/oauth-php/ for OAuth
        if (count($_POST))
            $data = $_POST;
        else
            $data = $_GET;
        if(isset($data['SID'])){
            $sid = $data['SID'];
        } else{
            $sid = $this->get('SID',null);
        }
        if(isset($_GET['action'])) {
            $data['action'] = $this->get('action',null);
        }
        if (empty($sid))
            throw new CHttpException(404, 'Service Not Found.');
        $result = FSM::run($sid, $data);
        
        if ($result->hasErrors()) {
            echo $result->getError('ErrorCode');
        } else {
            if(strtolower($sid) == 'ads.import.register')
                echo 'Payment Successful. Thanks';
        }
           
    }
    
    
    
    /**
    * Serve an Ajax request of a service
    */
    public function actionAjax() {
        if (!Yii::app()->request->IsAjaxRequest)
            throw new CHttpException(400, 'Not an Ajax request.');

        if (count($_POST))
            $data = $_POST;
        else
            $data = $_GET;
        $sid = $data['SID'];
        $result = FSM::run($sid, $data);
        if (isset($data['ajax']) && isset($data['validateOnly']) && $data['validateOnly'] == true)
            // special support for ajax validation
            echo $result->getActiveErrorMessages($result->model);
        elseif (isset($data['FORMAT']) && $data['FORMAT'] == 'text')
            echo $result->toText();
        else
            echo $result->toJson($this->post('SIMPLIFIED',$this->get('SIMPLIFIED',0)));
    }
    
    /**
    * Serve an widget for an Ajax request
    */
    public function actionWidget() {
        if (!Yii::app()->request->IsAjaxRequest)
            throw new CHttpException(400, 'Not an Ajax request.');

        if (count($_POST))
            $data = $_POST;
        else
            $data = $_GET;
        
        $wid = $data['WID'];
        unset($data['WID']);
        echo $this->widget($wid, $data, TRUE);
    }
}
?>