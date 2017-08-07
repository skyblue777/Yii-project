<?php

class LanguageController extends BackOfficeController
{
	
    public function actionGeneral() {
        list($controller,$actionId) = Yii::app()->createController('/Admin/Setting/index');
        $controller->attachEventHandler('OnBeforeFindSettingParameters', array($this,'filterLanguageSettingsParams'));      
        $controller->init();
        $controller->run($actionId);      
    }
	public function filterLanguageSettingsParams($event){
        $criteria = &$event->params['criteria'];
        $criteria->condition = '';
        $criteria->order = 'ordering';
        $criteria->addInCondition('name',array(
            'LANG'
        ));

        $event->params['modules'] = null;
    }
	public function actionTranslate() {
		if (sizeof ( LanguageForm::getList_languages_exist () ) > 0) {
			$model = new LanguageForm ();
			if (isset ( $_GET ['language'] ))
				$model->lang = $_GET ['language'];
			if (isset ( $_POST ['LanguageForm'] )) {
				if (isset ( $_POST ["LanguageForm"] ['list_records'] ))
					$list_records = $_POST ["LanguageForm"] ['list_records'];
				if (isset ( $_POST ["LanguageForm"] ['list_store_records'] ))
					$list_old_records = $_POST ["LanguageForm"] ['list_store_records'];
				$model->lang=$_POST['LanguageForm']['lang'];
				if ($model->saveArray2Data ( $list_old_records, $list_records )) {
					$model->setCategory($_POST['LanguageForm']['lang'],$_POST['LanguageForm']['group'],$_POST['LanguageForm']['module']);
					$this->message = Language::t ( Yii::app ()->language, 'Backend.Common.Message', 'Save successfully' );
				}
			} else {
				$language = isset ( $_GET ['language'] ) ? $_GET ['language'] : Yii::app ()->language;
				$model->setCategory ( $language, LanguageForm::DEFAULT_GROUP, LanguageForm::DEFAULT_MODULE );
			}
			$this->render ( 'translate', array ('model' => $model ) );
		} else
			throw new CHttpException ( 400, Language::t ( Yii::app ()->language, 'Backend.Language.Message', 'Empty list' ) );
	}
	public function actionChangeList($action) {  	
  		$model=new LanguageForm();
  		$model->lang=$_POST['current_language'];
  		if(isset($_POST["LanguageForm"]['list_records']))
 			$list_records=$_POST["LanguageForm"]['list_records'];		
 		if(isset($_POST["LanguageForm"]['list_store_records']))
 			$list_old_records=$_POST["LanguageForm"]['list_store_records'];	
  		$model->saveArray2Data($list_old_records,$list_records);	
  		$model->setCategory($_POST['LanguageForm']['lang'],$_POST['LanguageForm']['group'],$_POST['LanguageForm']['module']);
  		$this->renderPartial('change-list',array('model'=>$model,'action'=>$action));
    }
	public function actionManager() {
		$list_language_not_exist = LanguageForm::getList_languages_not_exist ();
		$list_language_exist = LanguageForm::getList_languages_exist ();
		$model = new LanguageForm ();
		reset ( $list_language_not_exist );
		$model->lang = key ( $list_language_not_exist );
		$model->origin_lang = Language::DEFAULT_LANGUAGE;
		if (isset ( $_POST ['create'] )) {
			if (sizeof ( $list_language_not_exist ) > 0 && sizeof ( $list_language_exist ) > 0) {
				/*
  		$model->group=LanguageForm::DEFAULT_GROUP;
  		$model->module=LanguageForm::DEFAULT_MODULE;
  		*/
				if (isset ( $_POST ['LanguageForm'] )) {
					
					$model->setLanguage ( $_POST ['LanguageForm'] ['origin_lang'] );
					$model->lang = $_POST ['LanguageForm'] ['lang'];
					//$model->setData($_POST['LanguageForm']);
					Yii::import ( 'Article.models.*' );
					$list_static_pages = Article::model ()->findAll ( 'lang = "' . $_POST ['LanguageForm'] ['origin_lang'] . '"' );
					if ($model->saveData ()) {
						//Create page static
						foreach ( $list_static_pages as $page ) {
							$new_page = new Article ();
							$new_page->attributes = $page->attributes;
							$new_page->lang = $_POST ['LanguageForm'] ['lang'];
							$new_page->save ();
						}
						//Create file template email
						$dir = Yii::app()->basePath.'/runtime/emails';
						foreach (array('activation_email','expriation_email','registration_email','reset_password_email','reply_to_ad') as $type){
							$file=$dir.'/'.$type.'_'.$_POST ['LanguageForm']['origin_lang'].'.php';
							$file_to=$dir.'/'.$type.'_'.$_POST ['LanguageForm'] ['lang'].'.php';
							copy($file,$file_to);
						}
						$this->redirect ( array ('translate', 'language' => $model->lang ) );
					}
				}
				/*
  	  	$list_language_not_exist=LanguageForm::getList_languages_not_exist();
  		reset($list_language_not_exist);
  		$model->setLanguage(key($list_language_not_exist));   
  		*/
			}
		}
		if (isset ( $_POST ['delete'] )) {
			$language = $_POST ['LanguageForm'] ['lang'];                                       
			if ($language != Language::DEFAULT_LANGUAGE) {
				$command = Yii::app ()->db->createCommand ();
				if ($command->delete ( 'language', 'lang=:lang', array (':lang' => $language ) )) {
					//Delete page static
					Yii::import ( 'Article.models.*' );
					$list_pages=Article::model()->findAll('lang=:lang',array(':lang'=>$language));
					foreach ($list_pages as $page){
						$page->delete();
					}
					//Delete file template email
						$dir = Yii::app()->basePath.'/runtime/emails';
						foreach (array('activation_email','expriation_email','registration_email','reset_password_email') as $type){
							$file=$dir.'/'.$type.'_'.$language.'.php';
							unlink($file);
						}
					if ($language == Yii::app ()->language) {
						$setting = SettingParam::model ()->find ( "name = 'LANG'" );
						$setting->value = Language::DEFAULT_LANGUAGE;
						if ($setting->save ())
							$this->redirect ( array ('translate' ) );
					} else
						$this->message = Language::t ( Yii::app ()->language, 'Backend.Common.Message', 'Remove successfully' );
				}
			}
		}
		$this->render ( 'manager', array ('model' => $model ) );
	}
	public function actionChangeLanguage($action) {  	
  		$model=new LanguageForm();
  		$model->setLanguage($_POST['language']);  	
  		$this->renderPartial('change-language',array('model'=>$model,'action'=>$action));
    }
 	public function actionExportFile($language=null) {
		$data = array ();
		//Set header for file excel
		$data [0] ['code'] = 'CODE';
		$data [0] ['english'] = 'ENGLISH';
		if($language != null)
			$data [0] ['translation'] = strtoupper($language);
		else 	
			$data [0] ['translation'] = 'TRANSLATION';
		$data [0] ['group'] = 'BACKEND/FRONTEND';
		$data [0] ['module'] = 'MODULE';
		$data [0] ['type'] = 'CATEGORY';
		//Load file config
 		$list_setting_value = require (dirname ( __FILE__ ).'/../config/'.DIRECTORY_SEPARATOR.'list_setting_value.php');
    	$list_languages= require (dirname ( __FILE__ ).'/../config/'.DIRECTORY_SEPARATOR.'config_languages.php'); 
 		$configFile = dirname ( __FILE__ ).'/../config/'.DIRECTORY_SEPARATOR.'config_records.php';
    	$tmp=require($configFile); 
    	$list_params=SettingParam::model()->findAll();	
    	foreach($list_params as $params){
    		if($params->label != "")
    			$tmp[Language::BACK_END]['Admin']['Setting'][$params->label]=$params->label;
    		if($params->value != "" && in_array($params->name,$list_setting_value))
    			$tmp[Language::BACK_END]['Admin']['Setting-Value'][$params->name]=$params->value;
    		if($params->setting_group != "")	
    			$tmp[Language::BACK_END]['Admin']['Setting-Group'][$params->setting_group]=$params->setting_group;
    	}
 		foreach ($list_languages as $item){
    		$tmp[Language::BACK_END]['Language']['List'][$item]=$item;
    	}
 		$i = 1;
		foreach ( $tmp as $index_group => $list_groups ) {
			foreach ( $list_groups as $index_module => $list_modules ) {
				foreach ( $list_modules as $index_type => $list_type ) {
					foreach ( $list_type as $index => $record ) {
						$data [$i] ['code'] = $index;
						$data [$i] ['english'] = $record;
						$data [$i] ['translation'] = $record;
						$data [$i] ['group'] = $index_group;
						$data [$i] ['module'] = $index_module;
						$data [$i] ['type'] = $index_type;
						$i ++;
					}
				}
			}
		}
		if ($language != null) {
			$criteria = new CDbCriteria ();
			$criteria->compare ( 'lang', $language );
			//Set list records
			$list = Language::model ()->findAll ( $criteria );
			foreach ( $list as $record ) {
				if (! isset ( $result [$record->group] ))
					$result [$record->group] = array ();
				if (! isset ( $result [$record->group] [$record->module] ))
					$result [$record->group] [$record->module] = array ();
				if (! isset ( $result [$record->group] [$record->module] [$record->type] ))
					$result [$record->group] [$record->module] [$record->type] = array ();
				$tmp [$record->group] [$record->module] [$record->type] [$record->code] = $record->value;
			}
		}
		$i=1;
		foreach ( $tmp as $index_group => $list_groups ) {
			foreach ( $list_groups as $index_module => $list_modules ) {
				foreach ( $list_modules as $index_type => $list_type ) {
					foreach ( $list_type as $index => $record ) {
						$data [$i] ['code'] = $index;
						if($language != null)
							$data [$i] ['translation'] = $record;
						else 
							$data [$i] ['translation'] = '...';
						$data [$i] ['group'] = $index_group;
						$data [$i] ['module'] = $index_module;
						$data [$i] ['type'] = $index_type;
						$i++;
					}
				}
			}
		}
		Yii::import('Language.extensions.vendors.PHPExcel',true);
		// Create new PHPExcel object
    	$objPHPExcel = new PHPExcel();
    	
    	// Set properties
    	$objPHPExcel->getProperties()->setCreator("Flexica")
                         ->setLastModifiedBy("Flexica")
                         ->setTitle("Language")
                         ->setSubject("Language")
                         ->setDescription("Language")
                         ->setKeywords("language")
                         ->setCategory("Language");            
        foreach ($data as $index=>$item){
        	 $j=$index+1;
        	 $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A' .$j, isset($item['code'])?$item['code']:'');  
        	 $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B' .$j, isset($item['english'])?$item['english']:'');    
        	 $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C' .$j, isset($item['translation'])?$item['translation']:'');    
        	 $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D' .$j, isset($item['group'])?$item['group']:'');    
        	 $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E' .$j, isset($item['module'])?$item['module']:'');    
        	 $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F' .$j, isset($item['type'])?$item['type']:'');      	
        }
		//Export file CSV
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        if(isset($language))
        	$file_path=Yii::getPathOfAlias('webroot').DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.'language'.DIRECTORY_SEPARATOR.'language-'.$language.'.xlsx';        
        else 	
        	$file_path=Yii::getPathOfAlias('webroot').DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.'language'.DIRECTORY_SEPARATOR.'language.xlsx';  
        $objWriter->save($file_path);
        // force to download a file
		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Content-Type: application/force-download");
		header( "Content-Disposition: attachment; filename=".basename($file_path));
		header( "Content-Description: File Transfer");
		@readfile($file_path);         
		}
	public function actionImportFile() {
		$model=new ImportForm();
		$model->lang=Language::DEFAULT_LANGUAGE;
	 	if(isset($_POST['ImportForm'])&&CUploadedFile::getInstance($model,'file') != null)
        {
            $model->file=CUploadedFile::getInstance($model,'file');
            $file=$model->file->getTempName();
            $model->lang=$_POST['ImportForm']['lang'];
			Yii::import('Language.extensions.vendors.PHPExcel',true);					
			$objReader = PHPExcel_IOFactory::createReader('Excel2007');
            $objPHPExcel = $objReader->load($file); //$file --> your filepath and filename          
            $objWorksheet = $objPHPExcel->getActiveSheet();
            $highestRow = $objWorksheet->getHighestRow(); // e.g. 10
            $highestColumn = $objWorksheet->getHighestColumn(); // e.g 'F'
            $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn); // e.g. 5
            for ($row = 2; $row <= $highestRow; ++$row) {
                $code=$objWorksheet->getCellByColumnAndRow(0, $row)->getValue();
                $value=$objWorksheet->getCellByColumnAndRow(2, $row)->getValue();
                $group=$objWorksheet->getCellByColumnAndRow(3, $row)->getValue();
                $module=$objWorksheet->getCellByColumnAndRow(4, $row)->getValue();
                $type=$objWorksheet->getCellByColumnAndRow(5, $row)->getValue();    
               	$criteria = new CDbCriteria ();
				$criteria->compare ( 'lang', $model->lang );
				$criteria->compare ( '`group`', $group );
				$criteria->compare ( 'module', $module );
				$criteria->compare ( 'type', $type );
				$criteria->compare ( 'code', $code );
				$list = Language::model ()->findAll ( $criteria );
				if(sizeof($list)>0){
					foreach ($list as $item){
						$item->value=$value;
						$item->save();
					}
				}
				else {
					$item=new Language();
					$item->lang = $model->lang;
					$item->code = $code;
					$item->value = $value;
					$item->group = $group;
					$item->module = $module;
					$item->type = $type;
					$item->save();
				}
            }
		}
		$this->render('import',array('model'=>$model));
	}

	public function actionEntities() {
//		$langs = Language::model()->findAll();
//		foreach($langs as $lang){
//			$lang->value = htmlentities($lang->value);
//			$lang->save();
//		}

		Yii::import('Article.models.Article');
		$articles = Article::model()->findAll();
		foreach($articles as $article){
			$article->title = htmlentities($article->title);
			$article->leading_text = htmlentities($article->leading_text);
			$article->content = htmlentities($article->content);
			$article->save();
		}


	}
}

