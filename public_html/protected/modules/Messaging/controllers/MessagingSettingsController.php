<?php

class MessagingSettingsController extends BackOfficeController
{
    public function actionEditEmailTemplate()
    {
        $dir = Yii::app()->basePath.'/runtime/emails';
        
        if (Yii::app()->request->getIsPostRequest()) {
            $file = $dir.'/'.$this->post('file','');
            if (is_file($file)) {
                $content = $this->post('content','','xss,notag');

                $content = str_replace('{','<?php echo $',$content);
                $content = str_replace('}','; ?>',$content);
 
                file_put_contents($file, $content);
            }
        }
        
        if ($dh = @opendir($dir)) {
            // if a file is selected
            $selectedFile = $this->get('template','').'_'.Yii::app()->language.'.php';
            if (file_exists($dir.'/'.$selectedFile)) {
                $selectedFilePath = $dir.'/'.$selectedFile;
                $content = file_get_contents($selectedFilePath);
            } else 
                throw new CHttpException(400,'This email template does not exist.');
            
            $this->render('edit_email_template', array(
                'content' => $content,
                'selectedFile' => $selectedFile
            ));
        } else {
            $this->renderText('Email template folder not found. It should be "emails" folder under /protedtec/runtime/.');
        }
    }   
}