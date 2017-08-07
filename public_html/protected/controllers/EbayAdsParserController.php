<?php
Yii::import('Ads.models.Annonce');
Yii::import('application.models.CrawlerProject');
    class EbayAdsParserController extends FrontController
    {
            
        public function actionIndex(){
            set_time_limit(0);
            //$model=new Annonce();
            $model=new CrawlerProject();
            $model->appId='E7C8C31171EE6E2768925057855C13DB23BE79F1';
            $model->backupAppId='E7C8C31171EE6E2768925057855C13DB276A427A';
            if(Yii::app()->request->isPostRequest){
                $model->attributes=$_POST['CrawlerProject'];
                $this->performAjaxValidation($model);
                if($model->validate())
                    $adsModel=FSM::run('application.EbayAds.parserLink',$_POST);
                //$link=$this->post('link','');
//                $categoryId=$this->post('category_id',0);
//                if($link=='') {echo "Link cannot be null";return;}
//                if($categoryId==0) {echo  "Categoty cannot be null";return;}
            }
            $this->render('index',array('model'=>$model));
        }
        
        
        /**
         * Performs the AJAX validation.
         * @param CModel the model to be validated
         */
        protected function performAjaxValidation($model)
        {
            if(isset($_POST['ajax']) && $_POST['ajax']==='crawler-project-form')
            {
                echo CActiveForm::validate($model);
                Yii::app()->end();
            }
        }
   }
  
?>
