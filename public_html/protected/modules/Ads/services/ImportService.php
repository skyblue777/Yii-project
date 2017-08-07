<?php

class ImportService extends FServiceBase {
    
    public function get ($params) {
        $action = $this->getParam($params, 'action', null);
        if($action == 'register') {
            $data = $this->getParam($params, 'data', null);
            if(!is_null($data)) {
                if($data == 'SUCCESS') {
                    $key = $this->getParam($params, 'key', null);
                    $url = urldecode($this->getParam($params, 'url', null));
                    $url = str_replace('&amp;', '&', 'http://'.$url);
                    Yii::import("Core.models.SettingParam");
                    SettingParam::model()->updateAll(array('value'=>1), "name = 'ADS_IMPORT_ENABLED'");
                    SettingParam::model()->updateAll(array('value'=>$key), "name = 'ADS_IMPORT_KEY'");
                    SettingParam::model()->updateAll(array('value'=>$url), "name = 'ADS_URL_CENTRAL'");
                }
            }
        } else if($action == 'import') { 
            $params['data'] = urldecode($params['data']);
            $data = CJSON::decode($params['data'], true);
            if(is_array($data)) {
                $cat = Category::model()->findByPk($data['category_id']);
                if(!is_null($cat)) {
                    $ann = new Annonce;
                    foreach($data as $key => $value) {
                        $ann->$key = $value;
                    }
                    $ann->id = null;
                    if(!$ann->save(false))
                    {
                        $this->result->fail('SAVE_FAILED', 'Import Annonce failed.');
                    }
                    else
                    {
                        // copy photos
                        if (!empty($ann->photos))
                        {
                            $new_photos = array();
                            $photos = unserialize($ann->photos);
                            if(count($photos)>0) {
                                $url = SettingParam::model()->findByPk(214);
                                if (!is_null($url) && $url->value != '' && $url->value != '0')
                                {
                                    foreach($photos as $photo) {
                                        $new_photo = $ann->id.'_'.$photo;
                                        $photo_content = @file_get_contents($url->value.'/uploads/ads/'.$photo);
                                        if ($photo_content == '') continue;
                                        file_put_contents('uploads/ads/'.$new_photo, $photo_content);
                                        $new_photos[] = $new_photo;
                                    }
                                }
                            }
                            $ann->photos = serialize($new_photos);
                            $ann->update(array('photos'));        
                        }    
                    }    
                    return $this->result;
                }
            }
        }
    }
    
}
?>