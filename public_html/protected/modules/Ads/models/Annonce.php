<?php

/**
 * This is the model class for table "annonce".
 */

require_once dirname(__FILE__).'/base/AnnonceBase.php';
class Annonce extends AnnonceBase
{
    public $createdDateTime = array('from'=>'','to'=>'');
    public $categoryTitle = '';
    public $reportReplied = '';
    public $pricePlan;
    
    const PAYMENT_PRICE_OPTION = 1;
    const FREE_PRICE_OPTION = 2;
    const CONTACT_PRICE_OPTION = 3;
    const SWAP_TRADE_PRICE_OPTION = 4;
    
    public $arrNotPaymentPriceOptions = array(
        self::FREE_PRICE_OPTION => 'Free',
        self::CONTACT_PRICE_OPTION => 'Please contact',
        self::SWAP_TRADE_PRICE_OPTION => 'Swap / Trade',
    );
    
    // attributes for advanced search
    public $searchedKeyword = '';
    public $exactPhrase = 0;
    public $priceFrom;
    public $priceTo;
    public $searchWithPhoto = 0;
    
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('category_id, type, opt_price, email', 'required'),
            array('title', 'required', 'message' => Language::t(Yii::app()->language,'Backend.Annonce.Common','Title cannot be blank.')),
            array('category_id, viewed, replied, feature_days, send, homepage_days', 'numerical', 'integerOnly'=>true),
            array('price', 'required', 'on'=>'edit_ad_price'),
            array('price', 'numerical', 'on'=>'edit_ad_price', 'message' => Language::t(Yii::app()->language,'Backend.Annonce.Common','Price must be a number.')),
            array('price', 'compare', 'compareValue'=>'0', 'operator'=>'>', 'on'=>'edit_ad_price'),
            array('feature_total, homepage_total', 'numerical'),
            array('type, opt_price, public, featured, feature_status, evt, homepage, homepage_status', 'length', 'max'=>1),
            array('title', 'length', 'max'=>100),
            array('email', 'length', 'max'=>100),
            array('email','email'),
            array('email','checkEmailExistsInUserAccount'),
            array('email','checkEmailUnban'),
            array('area', 'length', 'max'=>200),
            array('zipcode', 'length', 'max'=>10),
            array('code', 'length', 'max'=>50),
            array('feature_mdp, homepage_mdp', 'length', 'max'=>5),
            array('photos', 'safe'),
            array('title','checkBannedWordsInTitle'),
            array('description','checkBannedWordsInDescription'),
            array('category_id','checkValidCategory'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, category_id, title, price, email, public, reportReplied, createdDateTime, viewed,for_import', 'safe', 'on'=>'search'),
        );
    }
    
    /**
     * @desc Check if email already exists in user account
     */
    public function checkEmailExistsInUserAccount($attribute, $params)
    {
        if (!$this->hasErrors($attribute))
        {
            if (Yii::app()->user->isGuest)
            {
                Yii::import('User.models.User');
                $user = User::model()->findByAttributes(array('email' => $this->email,'status'=>1));
                if (!is_null($user)){
                    $this->addError('email', Language::t(Yii::app()->language,'Frontend.Ads.Message','Sorry! This e-mail has been already used by another user account. Or if it is your e-mail, you have to login with this e-mail.'));
                    return false;
                }
            }
        }
    }
    
    public function checkEmailUnban($attribute, $params)
    {
        if (!$this->hasErrors($attribute))
        {
            if (!$this->validateEmailAndClientIP($this->email))
            {
                $this->addError('email',UserSettings::MSG_BANNED_USER);
                return false;
            }
        }
    }
    
    public function deletePhotos()
    {
        $images = unserialize($this->photos);
        for ($i = 0; $i < count($images); $i++) 
        {
          $pathImage = Yii::app()->basePath.'/../uploads/ads/'.$images[$i];
          if (file_exists($pathImage)) {
            unlink($pathImage);
          }
        }
        
    }
    
    protected function createBannedWords()
    {
        $bannedWords = explode(',',AdsSettings::BANNED_WORDS);
        foreach($bannedWords as &$word)
            $word = trim($word);
        return $bannedWords;    
    }
    
    public function checkBannedWordsInTitle($attribute, $params)
    {
        if (empty($this->$attribute)) return TRUE;
        $bannedWords = $this->createBannedWords();
        $value = $this->$attribute;
        $length = strlen($value);
        foreach($bannedWords as $word)
        {
            $word = trim($word);
            if ($word=='') continue;
            if ($value == $word)
            {
                $this->addError($attribute,AdsSettings::MSG_BANNED_CONTENT);
                return FALSE;    
            }
            if (strpos($value,' '.$word.' ') !== FALSE || strpos($value,$word.' ') === 0)
            {
                $this->addError($attribute,AdsSettings::MSG_BANNED_CONTENT);
                return FALSE;
            }
            if (strpos($value,' '.$word) !== FALSE)
            {
                if ((strpos($value,' '.$word) + strlen(' '.$word)) == $length)
                {
                    $this->addError($attribute,AdsSettings::MSG_BANNED_CONTENT);
                    return FALSE;
                }    
            }
        }
    }
    
    public function checkBannedWordsInDescription($attribute, $params)
    {
        if (empty($this->$attribute)) return TRUE;
        $bannedWords = $this->createBannedWords();
        $value = $this->$attribute;
        $length = strlen($value);
        foreach($bannedWords as $word)
        {
            $word = trim($word);
            if ($word=='') continue;
            if ($value == $word)
            {
                $this->addError($attribute,AdsSettings::MSG_BANNED_CONTENT);
                return FALSE;    
            }
            if (strpos($value,' '.$word.' ') !== FALSE || strpos($value,$word.' ') === 0 || strpos($value,$word.'&nbsp;') === 0 || strpos($value,' '.$word.'&nbsp;') !== FALSE || strpos($value,'&nbsp;'.$word.' ') !== FALSE || strpos($value,'&nbsp;'.$word.'&nbsp;') !== FALSE || strpos($value,'>'.$word.'<') !== FALSE || strpos($value,'>'.$word.' ') !== FALSE || strpos($value,' '.$word.'<') !== FALSE || strpos($value,'>'.$word.'&nbsp;') !== FALSE || strpos($value,'&nbsp;'.$word.'<') !== FALSE)
            {
                $this->addError($attribute,AdsSettings::MSG_BANNED_CONTENT);
                return FALSE;
            }
            if (strpos($value,' '.$word) !== FALSE)
            {
                if ((strpos($value,' '.$word) + strlen(' '.$word)) == $length)
                {
                    $this->addError($attribute,AdsSettings::MSG_BANNED_CONTENT);
                    return FALSE;
                }    
            }
        }
    }
    
    public function checkValidCategory()
    {
        if (!$this->hasErrors('category_id'))
        {
            $cat = Category::model()->findByPk($this->category_id);
            if (is_null($cat))
            {
                $this->addError('category_id','This category is not found');
                return FALSE;    
            }
            if ($cat->id == AdsSettings::ADS_ROOT_CATEGORY || $cat->parent_id == AdsSettings::ADS_ROOT_CATEGORY)
            {
                $this->addError('category_id','You must select a sub category');
                return FALSE;    
            }
        }
    }
    
    public function relations()
    {
        return array(
            'category' => array(self::BELONGS_TO, 'Category','category_id'),
        );
    }
    
    public function search()
    {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria=new CDbCriteria;
        $criteria->with = array('category');

        $criteria->compare('public',$this->public);
        $criteria->compare('t.id',$this->id);
        $criteria->compare('category_id',$this->category_id);
        $criteria->compare('title',$this->title,true);
        $criteria->compare('price',$this->price);
        $criteria->compare('viewed',$this->viewed);
        if ($this->reportReplied == '1')
            $criteria->addCondition('replied > 0');
        elseif ($this->reportReplied == '0')
            $criteria->addCondition('replied <= 0');
        $criteria->compare('email',$this->email,true);
        
        // filter date_created - dateCreated
        if (is_array($this->createdDateTime)
            && isset($this->createdDateTime['from'], $this->createdDateTime['to'])
            && (empty($this->createdDateTime['from']) === false
                || empty($this->createdDateTime['to']) === false)) {
            $from = $this->createdDateTime['from'];
            $from = CDateTimeParser::parse($from, 'dd/MM/yyyy');
            if ($from === false) {
                $from = date('Y-m-d', strtotime('-1 years'));
            } else
                $from = date('Y-m-d', $from);
            $to = $this->createdDateTime['to'];
            $to = CDateTimeParser::parse($to, 'dd/MM/yyyy');
            if ($to === false) {
                $to = date('Y-m-d', strtotime('+1 years'));
            } else
                $to = date('Y-m-d', $to);
          
            $criteria->addCondition("DATE(t.create_time) BETWEEN DATE('".$from."') AND DATE('".$to."')");
        }
        
        // filter replied report
        if ((empty($this->reportReplied)) === false) {
          if ($this->reportReplied === 'no')
            $criteria->addCondition("t.replied = 0");
          else
            $criteria->addCondition("t.replied > 0");
        }

        return new CActiveDataProvider(
            get_class($this),
            array(
                'criteria'=>$criteria,
                'pagination'=>array(
                  'pageSize'=>AdsSettings::MAX_RESULTS,),
                'sort'=>array(
                  'attributes'=>array(
                    'price'=>array(
                      'asc'=>'price',
                      'desc'=>'price DESC',),
                    'create_time',
                    'title',
                    'categoryTitle' => array(
                        'asc'=>'category.title',
                        'desc'=>'category.title DESC'    
                    ),
                    'viewed',
                    'email',
                    'reportReplied' => array(
                        'asc'=>'replied',
                        'desc'=>'replied DESC'    
                    ),
                    'pricePlan' => array(
                        'asc'=>'category.paid_ad_required, featured, homepage',
                        'desc'=>'category.paid_ad_required, featured, homepage DESC'    
                    )
                  ),
                  'defaultOrder'=>'create_time DESC'),
        ));
    }
    
    public function listAds($criteria)
    {
      $dataProvider = null;
      
      if (!is_null($criteria)) {
        $dataProvider = new CActiveDataProvider(
          get_class($this),
          array(
            'criteria'=>$criteria,
            'pagination'=>array(
              'pageSize'=>AdsSettings::MAX_RESULTS,),
            'sort'=>array(
              'attributes'=>array(
                'price'=>array(
                  'asc'=>'price',
                  'desc'=>'price DESC',),
                'create_time',
                'title',
                'category_id',
                'viewed',
                'replied'),
              'defaultOrder'=>(AdsSettings::RESULT_SORT == 1)?'create_time DESC':'price'),
        ));
      }
      
      return $dataProvider;
    }
    
    protected function beforeSave()
    {
        $this->description_notag = trim(strip_tags($this->description));
        return TRUE; 
    }
    
    public function getPriceSection()
    {
        $str = '<p class="price">'; 
        if (isset($this->arrNotPaymentPriceOptions[$this->opt_price]))
            $str .= Language::t(Yii::app()->language,'Frontend.Ads.Form',$this->arrNotPaymentPriceOptions[$this->opt_price]);
        else
            $str .= AdsSettings::CURRENCY.' '.$this->price;
        $str .= '</p>';
      
        return $str;
    }
    
    public function getTitleContentSection()
    {
        // title
        $params = array('id'=>$this->id,
                        'alias'=>str_replace(array(' ','/','\\'),'-',$this->title));
        if ($this->area != '')
          $params['area'] = $this->area;
        $str = '<p class="title"><a href="'.
                Yii::app()->createUrl('Ads/ad/viewDetails', $params).'">';
        $str .= $this->title.'</a></p>';
        
        // description
        $desc = strip_tags($this->description);
        $str .= '<p>'.getFirstWordsFromString($desc, 40).'</p>';
        $str .= '<p class="status">';
        if (!empty ($this->category_id))
        {
          $cat = Category::model()->findByPk($this->category_id);
          if (!is_null($cat))
          {
            $params = array('cat_id'=>$cat->id, 'alias'=>$cat->alias);
            $location = Yii::app()->user->getState('location','');
            if ($location != '')
              $params['location'] = $location;
            $str .= '<a href="'.
                      Yii::app()->createUrl('Ads/ad/listByCategory', $params).'">';
            $str .= $cat->title.'</a>';
          }
        }
//        $str .= '<a href="'.Yii::app()->createUrl('Ads/ad/listByCategory',array('cat_id'=>$cat->id)).'">';
//        $str .= $cat->title.'</a>';
        if (!empty($this->area))
        {
          $str .= ' &ndash; <a href="'.Yii::app()->createUrl('Ads/ad/listByArea',
                                         array('location'=>$this->area)).'">';
          $str .= $this->area.'</a>';
        }
        $str .= '<span>';
        if (!empty($this->create_time) && $this->create_time != "0000-00-00 00:00:00")
            $str .= Language::getInterval(strtotime($this->create_time));
        $str .= '</span></p>';
        
        return $str;
    }
    
    public function getImageSection ()
    {
      $params = array('id'=>$this->id,
                      'alias'=>str_replace(array(' ','/','\\'),'-',$this->title));
      if ($this->area != '')
          $params['area'] = $this->area;
      $str = '<a href="'.Yii::app()->createUrl('Ads/ad/viewDetails', $params).'">';
      $images = unserialize($this->photos);
      $imageUrl = Yii::app()->request->getBaseUrl(TRUE).'/images/no-image.jpg';
      if (is_array($images) && count($images) > 0)
      {
        $img_exist = false;
        for ($i = 0; $i < count($images); $i++) 
        {
          $pathImage = 'uploads/ads/'.$images[$i];
          if (file_exists($pathImage)) {
            $imageUrl = Yii::app()->request->getBaseUrl(TRUE).'/image.php?thumb=';
            $imageUrl .= FlexImage::createThumbFilename($pathImage,84,76);
            $img_exist = true;
            break;
          }
        }
        
        if (!$img_exist)
        {
          $this->photos = '';
          $this->update(array('photos'));
          //var_dump($imageUrl);
        }
      }      
      $str .= '<img alt="'.$this->title.'" src="'.$imageUrl.'" />';
      
      return $str.'</a>';
    }
    
    public function getReportSection()
    {
        if ($this->replied > 0)
        {
            return '<div style="text-align: center;">'.CHtml::image(themeUrl().'/images/tick.gif').'</div>';    
        }
        return '';
    }
    
    public function getPricePlanSection()
    {
        $arr = array();
        if (!is_null($this->category) && $this->category->paid_ad_required == 1)
            $arr[] = Language::t(Yii::app()->language,'Backend.Common.Menu','Paid Ads');
        if ($this->featured == 1)
            $arr[] = Language::t(Yii::app()->language,'Backend.Common.Menu','Top Ad');
        if ($this->homepage == 1)
            $arr[] = Language::t(Yii::app()->language,'Backend.Common.Menu','Homepage Gallery');
        
        return implode(', ',$arr);
    }
    
    public function getImageSectionInGrid()
    {
        $images = unserialize($this->photos);
        $imageUrl = baseUrl().'/images/no-image.jpg';
        if (is_array($images) && count($images) > 0)
        {
            $pathImage = 'uploads/ads/'.$images[0];
            if (file_exists($pathImage)) {
                include_once(Yii::getPathOfAlias("webroot").'/protected/modules/Core/extensions/web/helpers/FlexImage.php');
                $imageUrl = baseUrl().'/image.php?thumb='.FlexImage::createThumbFilename($pathImage,84,76);
            }   
        }      
        return '<div style="text-align: center;">'.CHtml::image($imageUrl,'').'</div>';
    }
    public function getShortEmail(){
	if($this->email!=''){
	    $splitEmail=str_split($this->email,10);
	    return '<a href="mailto:'.$this->email.'" title="'.$this->email.'" >'.$splitEmail[0].'...</a>';
	}
	return '';
    }
    
    public function validateEmailAndClientIP($email='')
    {
        Yii::import('User.models.User');
        $bannedEmails = User::model()->getBannedEmails();
        $bannedIps = User::model()->getBannedIps();
        $client_ip = Utility::getIP();
        if ($email!='' && in_array($email,$bannedEmails))
            return FALSE;
        if (in_array($client_ip,$bannedIps))
            return FALSE;
        return TRUE;    
    }
    
    public function getActionsButtonColumn()
    {
        Yii::import('User.models.User');
        $strDel = CHtml::link('<img alt="Delete" src="'.Yii::app()->theme->baseUrl.'/images/buttons/ico-delete.gif" />',Yii::app()->createUrl('/Ads/Ads/delete',array('id'=>$this->id)),array('class'=>'delete'));
        $strUpdate = CHtml::link('<img alt="Update" src="'.Yii::app()->theme->baseUrl.'/images/buttons/ico-edit.gif" />',Yii::app()->createUrl('/Ads/Ads/update',array('id'=>$this->id)),array('class'=>'update'));
        // icon ban
        $banLinkClassName = 'ban-user';
        $banLinkTitle = Language::t(Yii::app()->language,'Backend.Ads.Message','Ban user');
        $banImageUrl = CHtml::image(themeUrl().'/images/buttons/ban.png','Ban user');
        $bannedEmails = User::model()->getBannedEmails();
        if (in_array($this->email,$bannedEmails))
        {
            $banLinkClassName = 'unban-user';
            $banLinkTitle = 'Unban user';
            $banImageUrl = CHtml::image(themeUrl().'/images/buttons/restore.png','Unban user');   
        }
        $strBan = '<a class="'.$banLinkClassName.'" href="'.$this->id.'" title="'.$banLinkTitle.'">'.$banImageUrl.'</a>';
        $strTopAd = CHtml::link('<img alt="'.Language::t(Yii::app()->language,'Backend.Ads.Message','Make this ad to the top ad').'" src="'.Yii::app()->theme->baseUrl.'/images/buttons/go-top.png" />',$this->id,array('class'=>'make-top-ad','title'=>Language::t(Yii::app()->language,'Backend.Ads.Message','Make this ad to the top ad')));
        $strHome = CHtml::link('<img alt="'.Language::t(Yii::app()->language,'Backend.Ads.Message','Add this ad in the Homepage Gallery').'" src="'.Yii::app()->theme->baseUrl.'/images/buttons/home_icon.png" />',$this->id,array('class'=>'add-into-homepage-gallery','title'=>Language::t(Yii::app()->language,'Backend.Ads.Message','Add this ad in the Homepage Gallery')));
        echo $strDel.$strUpdate.$strBan.$strTopAd.$strHome;
    }
    
    public function getTitleSectionInGrid()
    {
        if ($this->public == 1)
        {
            $params = array('id'=>$this->id,
                            'alias'=>str_replace(array(' ','/','\\'),'-',$this->title));
            if ($this->area != '')
              $params['area'] = $this->area;
            return '<a class="lnk-title" target="blank" href="'.Yii::app()->createUrl('Ads/ad/viewDetails', $params).'">'.$this->title.'</a>';
        }
        else
        {
            return $this->title;
        }    
    }
}