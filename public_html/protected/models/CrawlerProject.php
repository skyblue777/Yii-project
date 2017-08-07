<?php

Yii::import('application.components.BingTranslateLib.BingTranslateWrapper');
Yii::import('application.components.NetReader');

class CrawlerProject extends CFormModel {

    public $sourceLang;
    public $targetLang;
    public $appId;
    public $backupAppId;
    public $contentUrl;
    public $categoryId;
    public $postEmail;

    /**
     * Declares the validation rules.
     * The rules state that username and password are required,
     * and password needs to be authenticated.
     */
    public function rules() {

        return array(
            // username and password are required
            array('sourceLang, targetLang, appId, contentUrl, categoryId, postEmail', 'required'),
            // rememberMe needs to be a boolean
            array('contentUrl', 'url'),
            array('postEmail', 'email'),
            array('backupAppId', 'required'),
            array('backupAppId', 'validateBingAppId'),
            array('backupAppId', 'validSameAppId'),
            array('sourceLang, targetLang', 'length', 'max' => 6),
            //Just work with ebay.fr shop link
            array('contentUrl', 'validateEbayFr'),
            //app Id must be a valid Bing ID
            array('appId', 'validateBingAppId'),
        );
    }

    /**
     * This rule validate if the backup appId is the same with appID
     * 
     * @param mixed $attribute
     * @param mixed $params
     */
    public function validSameAppId($attribute, $params) {
        if ($this->{$attribute} == '')
            return true;
        if ($this->{$attribute} == $this->appId) {
            $this->addError($attribute, "{$this->getAttributeLabel($attribute)} value cannot be the same with appID value.");
            return false;
        }
        else
            return true;
    }

    /**
     * This is declared to validate the shop links in ebay french.
     * 
     * @param mixed $attribute
     * @param mixed $params
     */
    public function validateEbayFr($attribute, $params) {
        $link = html_entity_decode(urldecode($this->{$attribute}));
        if (strpos($link, 'ebay.fr') !== false) {
            if (strpos($link, '_dmd') !== false) {
                if (strpos($link, '_dmd=2') !== false)
                    $link = str_replace('_dmd=2', '_dmd=1', $link);
            }
            else
                $link.='&_dmd=1';
        }
        else {
            $this->addError($attribute, "Sorry , We just only parser links of shops on ebay.fr. Please correct it.");
            return false;
        }
        $nr = new NetReader($link);
        $doc = $nr->read();
        $xpath = new DOMXPath($doc);
        //List all items on page
        $items = $xpath->query('//div[@id="blending_central_panel"]/div/div[@class="rs-oDiv cm-bg"]/div[@class="rs-iDiv cm-br"]');
        if ($items->length == 0) {
            $this->addError($attribute, "Sorry, this link of shop is invalid. Please try again.");
            return false;
        }
        else
            return true;
    }

    /**
     * This function declare a validation rule : validate app ID
     * 
     * @param mixed $attribute
     * @param mixed $params
     */
    public function validateBingAppId($attribute, $params) {
        if ($this->{$attribute} == '')
            return true;
        $gt = new BingTranslateWrapper($this->{$attribute});
        try {
            $isValid = $gt->validAppId();
        } catch (Exception $exc) {
            $this->addError($attribute,$exc->getMessage());
            return false;
        }

        if ($isValid == BingTranslateWrapper::APP_ID_INVALID) {
            $this->addError($attribute, "This AppID is invalid or reaches it monthly limitation of 2 millions characters/month.");
            return false;
        }
        else
            return true;
    }

    /**
     * The label of attributes
     * 
     */
    public function attributeLabels() {
        return array(
            'sourceLang' => 'Source Language',
            'targetLang' => 'Target Language',
            'appId' => 'AppID',
            'backupAppId' => 'Backup AppID',
            'contentUrl' => 'Content Url',
            'categoryId' => 'Category',
        );
    }

}

?>
