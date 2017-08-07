<?php

Yii::import('Ads.models.Annonce');
Yii::import('application.components.NetReader');
Yii::import('application.components.BingTranslateLib.BingTranslateWrapper');
Yii::import('User.models.User');

/**
 * 
 */
class EbayAdsService extends FServiceBase {

    /**
     * Get all Ads from a valid post link
     * 
     * @param array $params
     * @return FServiceModel
     */
    public function parserLink($params) {

        $appId = $this->getParam($params['CrawlerProject'], 'appId');
        $sourceLang = $this->getParam($params['CrawlerProject'], 'sourceLang');
        $targetLang = $this->getParam($params['CrawlerProject'], 'targetLang');
        $contentUrl = $this->getParam($params['CrawlerProject'], 'contentUrl');
        $categoryId = $this->getParam($params['CrawlerProject'], 'categoryId');
        $postEmail = $this->getParam($params['CrawlerProject'], 'postEmail');
        $backupAppId = $this->getParam($params['CrawlerProject'], 'backupAppId', '');
        $gt = new BingTranslateWrapper($appId);
        $link = html_entity_decode(urldecode($contentUrl));
        //Check if this is frenche ebay
        if (strpos($link, 'ebay.fr') !== false) {
            //Check if link has content is table or not, if true, change to div content type
            if (strpos($link, '_dmd') !== false) {
                if (strpos($link, '_dmd=2') !== false) {
                    //echo 'Co';
                    $link = str_replace('_dmd=2', '_dmd=1', $link);
                }
            } else {
                $link.='&_dmd=1';
            }
        }
        $nr = new NetReader($link);
        $doc = $nr->read();
        $xpath = new DOMXPath($doc);

        //List all items on page
        $items = $xpath->query('//div[@id="blending_central_panel"]/div/div[@class="rs-oDiv cm-bg"]/div[@class="rs-iDiv cm-br"]');
        if ($items->length == 0) {
            return;
        }

        foreach ($items as $item) {
            //Get the rootNode
            $rootPath = $xpath->query('table', $item)->item(0)->childNodes->item(0);
            if (is_null($rootPath))
                continue;
            //Get title
            $titlePath = $xpath->query('td[@class="lv-desc-box lv-pl"]/div/div[@class="lv-pb5 lv-title-box"]/a', $rootPath);
            if (is_null($titlePath))
                continue;
            $title = $titlePath->item(0)->nodeValue;
            //Get the inner link
            $linkItem = $titlePath->item(0)->getAttribute('href');
            //Get Image
            $imagePath = $xpath->query('td[@class="lv-p140"]/div/a/img', $rootPath);
            if (is_null($imagePath))
                continue;
            $image = $imagePath->item(0)->getAttribute('src');
            //Get price
            $pricePath = $xpath->query('td[@class="lv-ps"]/div/div/b', $rootPath);
            if (is_null($pricePath))
                continue;
            //Remove the unit
            $price1 = str_replace(' ', '', str_replace('â¬', '', $pricePath->item(0)->nodeValue));
            $price = intval($price1);

            //Save to parsedAds table 
            // Check this feed is existed in the system or not
            //$parsedAds=ParsedAds::model()->find('url LIKE :linkItem',array(':linkItem'=>$linkItem));
            // Add to table ParsedAds if this url does not exist in the system
//            if(!$parsedAds){
//                $parsedAds=new ParsedAds();
//                $parsedAds->url=$linkItem;
//                $parsedAds->parsed=0;
//                $parsedAds->save(false);
//            }
//            else {
//                echo "Skipped ",$linkItem," (existed)<br />";
//                continue;//
//            }
            //Save to db
            $model = new Annonce();
            if ($gt->validAppId() == 0) {
                if ($backupAppId != '') {
                    $newGt = new BingTranslateWrapper($backupAppId);

                    try {
                        $model->title = stripslashes($newGt->translate(addslashes($title), $sourceLang, $targetLang));
                    } catch (Exception $exc) {
                        echo "skipped ".$title."(".$exc->getMessage() . ")<br />";
                        continue;
                    }
                } else {
                    echo 'Skipped ' . $linkItem . '(Cannot translate, please fill backup appID field) <br />';
                    continue;
                }
            }
            else
            {
                try {
                    $model->title = stripslashes($gt->translate(addslashes($title), $sourceLang, $targetLang));
                } catch (Exception $exc) {
                    echo "skipped ".$title."(".$exc->getMessage() . ")<br />";
                    continue;
                }
                        }
            $model->price = $price;
            $model->photos = $image;
            $model->category_id = $categoryId;
            $model->public = 1;
            $model->email = $postEmail;
            $model->create_time = date('Y-m-d H:m:s');
            $model->type=1;
            if($price1=="Gratuit")
                $model->opt_price=  Annonce::FREE_PRICE_OPTION;
            else $model->opt_price=  Annonce::PAYMENT_PRICE_OPTION;
            //Find full description
            $this->queryLink($linkItem, $model);

            $model->id = null;
            //Check appID
            if ($gt->validAppId() == 0) {
                if ($backupAppId != '') {
                    $newGt = new BingTranslateWrapper($backupAppId);

                    try {
                        $model->description = stripslashes($newGt->translate(addslashes($model->description), $sourceLang, $targetLang));
                    } catch (Exception $exc) {
                        echo "skipped ".$title."(".$exc->getMessage() . ")<br />";
                        continue;
                    }
                } else {
                    echo 'Skipped ' . $linkItem . '(Cannot translate, please fill backup appID field) <br />';
                    continue;
                }
            }
            else{
                try {
                    $model->description = stripslashes($gt->translate(addslashes($model->description), $sourceLang, $targetLang));
                } catch (Exception $exc) {
                    echo "skipped ".$title."(".$exc->getMessage() . ")<br />";
                    continue;
                }
            }

            //Check dublicate
            $isDublicate = Annonce::model()->find('title LIKE :title AND category_id=:catID AND description LIKE :description', array(
                ':title' => $model->title,
                ':catID' => $model->category_id,
                ':description' => $model->description,
                    ));
            if ($isDublicate) {
                echo "skipped " . $model->title . " (existed) <br />";
                continue;
            }

            if ($model->save(false)) {
                //If the query link fucntion has modified the photo, change the photo.
                $image1 = $model->photos;
                $uploadFolder = 'uploads/ads';
                if (!is_dir($uploadFolder))
                    mkdir($uploadFolder, 0777, true);
                // download photo
                if (!is_null($imagePath)) {
                    //$image=$imagePath->item(0)->getAttribute('src');
                    $imageUrl = $nr->getImage($image1, $uploadFolder, $model->id);
                    if ($imageUrl != '') {
                        $arrImages = array($imageUrl);
                        $model->photos = serialize($arrImages);
                        $model->update(array('photos'));
                    }
                }
                //Update this itemLink for the next time
                //$parsedAds=ParsedAds::model()->find('url LIKE :linkItem',array(':linkItem'=>$linkItem));
//                    $parsedAds->parsed=1;
//                    $parsedAds->update('parsed');

                echo "Saved successful! ", $title, "<br />";
            } else {
                echo "Save failed! ", $title, "<br />";
                continue;
            }
        }
        //Check if the email exist 
        $user = User::model()->findByAttributes(array('email' => $postEmail));
        if (is_null($user)) {
            $user = new User();
            $user->email = $postEmail;
            $user->username = $postEmail;
            $user->first_name = $user->last_name = ' ';
            $user->password = md5('12345');
            $user->status = User::STATUS_DEACTIVE;
            $user->save(FALSE);
        }

        return $this->result;
    }

    /**
     * Query the inner link content
     * 
     * @param mixed $link
     * @param mixed $model
     */
    private function queryLink($link='', &$model) {
        if ($link == '')
            return;
        $nr = new NetReader($link);
        $doc = $nr->read();
        $xpath = new DOMXPath($doc);
        $description = '';
        $image = '';
        if (strpos($link, 'http://cgi.ebay.fr') !== false) {
            //Get description tab
            $descPath = $xpath->query('//div["vi_tabs"]/div[@class="tb-cw"]/div[@id="vi_tabs_0_cnt"]/div')->item(0);
            if (is_null($descPath)) {
                return;
            }
            //This type of description has contained table and other html tags.
            $docDes = new DOMDocument();
            $docDes->appendChild($docDes->importNode($descPath, TRUE));
            $description = trim($docDes->saveHTML());
            //Get node value
            //$description=$descPath->item(0)->nodeValue;
            //$imagePath=$xpath->query('//img[@id="i_vv4-36"]');
        }

        if (strpos($link, 'http://annonces.ebay.fr/viewad') !== false) {
            $descPath = $xpath->query('//div[@id="fullDesc"]');
            $imagePath = $xpath->query('//img[@id="galleryImg"]');
            if ($imagePath->length == 0) {
                return;
            }
            $image = $imagePath->item(0)->getAttribute('src');
            if ($descPath->length == 0) {
                return;
            }
            $description = $descPath->item(0)->nodeValue;
            $model->photos = $image;
        }
        //save to model
        $model->description = $description;
        //$model->photos->$image;
    }

}

?>