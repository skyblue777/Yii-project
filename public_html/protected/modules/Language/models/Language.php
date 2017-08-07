<?php

class Language extends CActiveRecord
{
	const FRONT_END='Frontend';
	const BACK_END='Backend';
	const DEFAULT_LANGUAGE='en';
	private static $_items=array();

	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function tableName()
	{
		return 'language';
	}
	public function rules()
	{
		return array(
			array('lang,code,value,group', 'required'),
			array('module,type','safe')
			);
	}
	public static function t($lang, $type, $code) {
		$index = $lang . '.' . $type;
		if (! isset ( self::$_items [$index] [$code] )) {
			$criteria = new CDbCriteria ();
			$criteria->compare ( 'lang', $lang );
			$criteria->compare ( 'code', $code );
			$list_params = array_diff ( explode ( '.', $type ), array ('' ) );
			if (isset ( $list_params [0] ))
				$criteria->compare ( '`group`', $list_params [0] );
			if (isset ( $list_params [1] ))
				$criteria->compare ( 'module', $list_params [1] );
			if (isset ( $list_params [2] ))
				$criteria->compare ( 'type', $list_params [2] );
			$model = self::model ()->find ( $criteria );
			if (isset ( $model ))
				self::$_items [$index] [$code] = $model->value;
		}
		return isset ( self::$_items [$index] [$code] ) ? self::$_items [$index] [$code] : $code;
	}
    //Get label of language
    static function getLabel_language($language){
    	//Get list all language
		$configFile = dirname ( __FILE__ ).'/../config/'.DIRECTORY_SEPARATOR.'config_languages.php';
    	$list=require($configFile);  
    	return $list[$language];    	
    }
    //Handler datetime formatting
	public static function getInterval($timestamp, $granularity = 1)
	{
    $seconds = time() - $timestamp;
    $units = array( '1 '.Language::t(Yii::app()->language,'Backend.Language.Time','year').'|:count '.Language::t(Yii::app()->language,'Backend.Language.Time','years') => 31536000, '1 '.Language::t(Yii::app()->language,'Backend.Language.Time','week').'|:count '.Language::t(Yii::app()->language,'Backend.Language.Time','weeks') => 604800, '1 '.Language::t(Yii::app()->language,'Backend.Language.Time','day').'|:count '.Language::t(Yii::app()->language,'Backend.Language.Time','days') => 86400, '1 '.Language::t(Yii::app()->language,'Backend.Language.Time','hour').'|:count '.Language::t(Yii::app()->language,'Backend.Language.Time','hours') => 3600, '1 '.Language::t(Yii::app()->language,'Backend.Language.Time','min').'|:count '.Language::t(Yii::app()->language,'Backend.Language.Time','mins') => 60, '1 '.Language::t(Yii::app()->language,'Backend.Language.Time','sec').'|:count '.Language::t(Yii::app()->language,'Backend.Language.Time','secs') => 1);
    $output = '';
    if ($seconds < 31536000)
    {
        foreach ($units as $key => $value)
        {
            $key = explode('|', $key);
            if ($seconds >= $value)
            {
                $count = floor($seconds / $value);
                $output .= ($output ? ' ' : '');
                if ($count == 1)
                {
                    $output .= $key[0];
                }
                else
                {
                    $output .= str_replace(':count', $count, $key[1]);
                }
                $seconds %= $value; $granularity--;
            }
            if ($granularity == 0) { break; }
        }
        if(Yii::app()->language != 'fr' && Yii::app()->language != 'es')
       		if ($output) $output .= ' '.Language::t(Yii::app()->language,'Backend.Language.Time','ago'); else $output = '0 '.Language::t(Yii::app()->language,'Backend.Language.Time','sec').' '.Language::t(Yii::app()->language,'Backend.Language.Time','ago');
       	else 
       		if ($output) $output = Language::t(Yii::app()->language,'Backend.Language.Time','ago').' '.$output; else $output = Language::t(Yii::app()->language,'Backend.Language.Time','sec').' '.Language::t(Yii::app()->language,'Backend.Language.Time','ago').' 0';
    }
    else
    {
        $output = Yii::app()->getDateFormatter()->format(Yii::app()->params['dateTimeFormat'], $timestamp);
    }
    return $output ? $output : '0 '.Language::t(Yii::app()->language,'Backend.Language.Time','sec');
}
}