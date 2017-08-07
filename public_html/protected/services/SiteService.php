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
 Yii::import('Ads.models.Annonce');
 Yii::import('application.components.NetReader');
class SiteService extends FServiceBase
{
    /**
    * Create a new post
    *
    * @param array
    */
    public function sendContact($params){
        $contactForm = $this->getModel($params['ContactForm'], 'ContactForm');
        $this->result->processed('contactForm', $contactForm);
        if(!$contactForm->validate())
        {
            return $this->result->fail(0, 'Invalid data');
        }

        $senderEmail = $this->getParam($params, 'senderEmail', '');

        $headers="From: {$contactForm->email}\r\nReply-To: {$contactForm->email}";
        if(!mail($senderEmail,$contactForm->subject,$contactForm->body,$headers)) {
            return $this->result->fail(0, 'E-mail was not sent successfully');
        }
    }

    /**
    * Login
    *
    * @param array
    */
    public function login($params)
    {
        $loginForm = $this->getModel($params['LoginForm'], 'LoginForm');
        $this->result->processed('loginForm', $loginForm);
        if(!$loginForm->validate())
        {
            return $this->result->fail(0, 'Invalid data');
        }

        $identity = new UserIdentity($loginForm->username,$loginForm->password);
        if(!$identity->authenticate()) {
            $loginForm->addError('password','Incorrect username or password.');
            return $this->result->fail(0, 'Incorrect username or password.');
        }

        if($identity->errorCode===UserIdentity::ERROR_NONE)
        {
            $duration=$loginForm->rememberMe ? 3600*24*30 : 0; // 30 days
            Yii::app()->user->login($identity,$duration);
        }
        else {
            $this->result->addError('LoginForm', 'Authentication failed');
        }
    }
    /**
    * Get all Ads from a valid post link
    * 
    * @param mixed $params
    * @return FServiceModel
    */
    public function parserLink($params){
        $categoryId=$this->getParam($params,'categoryId');
        $link=$this->getParam($params,'link');
        //Check if link has content is table or not, if true, change to div content type
        $link=html_entity_decode(urldecode($link));
        if (strpos($link, '_dmd') !== false){
            if (strpos($link, '_dmd=2') !== false){
                //echo 'Co';
                $link= str_replace('_dmd=2','_dmd=1',$link);
             
            }
        }
        else{
            $link.='&_dmd=1';
        }
        $nr = new NetReader($link);
        $doc = $nr->read();
        $xpath = new DOMXPath($doc);
        
        //List all items on page
        $items = $xpath->query('//div[@id="blending_central_panel"]/div/div[@class="rs-oDiv cm-bg"]/div[@class="rs-iDiv cm-br"]');
        if ($items->length == 0) { return; }
        
        foreach($items as $item){
            //Get the first tr ..... table/tbody/tr[0]
            $rootPath=$xpath->query('table',$item)->item(0)->childNodes->item(0);
            if(is_null($rootPath)) continue;
            //var_dump($titlePath->length);die;
            $titlePath=$xpath->query('td[@class="lv-desc-box lv-pl"]/div/div[@class="lv-pb5 lv-title-box"]/a',$rootPath);
            if(is_null($titlePath)) continue;
            //Get Image
            $imagePath=$xpath->query('td[@class="lv-p140"]/div/a/img',$rootPath);
            if(is_null($imagePath)) continue;
            $image=$imagePath->item(0)->getAttribute('src');            
            //Get title
            $title=$titlePath->item(0)->nodeValue;
            //Get link for finding description
            $linkItem=$titlePath->item(0)->getAttribute('href');
            //Get price
            $pricePath=$xpath->query('td[@class="lv-ps"]/div/div/b',$rootPath);
            if(is_null($pricePath)) continue;
            $price=str_replace(' ','',str_replace('â¬','',$pricePath->item(0)->nodeValue));
            $price=intval($price);
             //echo $image.'<br />';
            //Save to db
            $model=new Annonce();
            $model->title=$title;
            $model->price=$price;
            $model->photos=$image;
            $model->category_id=$categoryId;
            $model->public=1;
            //Find full description
            $this->queryLink($linkItem,$model);
            //die;
            $model->id=null;
            if($model->save(false)){
                    $image1=$model->photos;
                    $uploadFolder = 'uploads/ads';
                    if (! is_dir($uploadFolder))
                        mkdir($uploadFolder, 0777, true);
                    // download photo
                    if (!is_null($imagePath))
                    {
                        //$image=$imagePath->item(0)->getAttribute('src');
                        $imageUrl = $nr->getImage($image1, $uploadFolder,$model->id);
                        if ($imageUrl != '')
                        {
                            $arrImages = array($imageUrl);
                            $model->photos = serialize($arrImages);
                            $model->update(array('photos'));    
                        }    
                    }
                    echo "saved successful! ",$title,"<br />";
            }
            else{
                echo "save failed!",$title,"<br />";continue;
            }
        }
        return $this->result;
    }
    
    
    /**
    * Query the inner link content
    * 
    * @param mixed $link
    * @param mixed $model
    */
    private function queryLink($link='',&$model){
        if($link=='') return;
        $nr = new NetReader($link);
        $doc = $nr->read();
        $xpath = new DOMXPath($doc);
        $description='';
        $image='';
        if (strpos($link, 'http://cgi.ebay.fr') !== false)
        { 
            //Get description tab
            $descPath = $xpath->query('//div["vi_tabs"]/div[@class="tb-cw"]/div[@id="vi_tabs_0_cnt"]/div')->item(0);
            if (is_null($descPath)) { return; }
            //This type of description has contained table and other html tags.
            $docDes = new DOMDocument();
            $docDes->appendChild($docDes->importNode($descPath,TRUE));
            $description = trim($docDes->saveHTML());
            //Get node value
            //$description=$descPath->item(0)->nodeValue;
            //$imagePath=$xpath->query('//img[@id="i_vv4-36"]');
        }
        
        if (strpos($link, 'http://annonces.ebay.fr/viewad') !== false)
        {
                $descPath = $xpath->query('//div[@id="fullDesc"]');
                $imagePath = $xpath->query('//img[@id="galleryImg"]');
                if ($imagePath->length == 0) { return; }
                $image=$imagePath->item(0)->getAttribute('src');
                if ($descPath->length == 0) { return; }
                $description=$descPath->item(0)->nodeValue;
                $model->photos=$image;
        }
        //save to model
        $model->description=$description;
        //$model->photos->$image;
    }
}
?>