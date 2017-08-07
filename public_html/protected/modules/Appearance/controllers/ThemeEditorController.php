<?php
class ThemeEditorController extends BackOfficeController
{
    /**
    * edit content of template file
    * + edit css files
    * + edit template files
    */
    public function actionTemplateEditor() {
        if (!isset($_GET['file'])) {
            $this->redirect(url('/Appearance/themeEditor/templateEditor', array('file' => 'general.css')));
        }

        $availableThemes = $this->getAvailableThemes();
        $currentTheme = Settings::THEME;
        if (array_key_exists($currentTheme, $availableThemes) === false) {
            throw new CHttpException(500, "Theme '{$currentTheme}' not available.");
        }

        //get style, views template of current theme
        $cssFiles = $this->getCssFiles($availableThemes[$currentTheme]);

        list($currentFile, $parserFile) = $this->configThemeEditor($cssFiles);

        $this->render('TemplateEditor', array(
            'currentTheme'=>$currentTheme,
            'currentFile'=>$currentFile,
            'parserFile'=>$parserFile,
            'cssFiles'=>$cssFiles,
        ));
    }

    /**
    * read all files, folders in current folder
    *
    * @param string $dir
    * @return array
    */
    protected function readDir($dir, $recursive=false){
        $childs = array();
        if ($handle = opendir($dir)) {
            while (false !== ($file = readdir($handle))) {
                if($file != "." && $file != ".." && count($this->removeExclude($file)) > 0){
                    if(is_dir($dir.DIRECTORY_SEPARATOR.$file) && $recursive) {
                        $sub = $this->readDir($dir.DIRECTORY_SEPARATOR.$file, $recursive);
                        $childs = array_merge($childs, $sub);
                    } else {
                        array_push($childs, $dir.DIRECTORY_SEPARATOR.$file);
                    }
                }
            }
            closedir($handle);
        }
        return $childs;
    }

    /**
    * remove exclude item
    *
    * @param array $arr
    * @return array
    */
    protected function removeExclude($arr) {
        if (is_array($arr) === false) $arr = array($arr);
        $exclude = array('global', 'install', 'BackOffice', '.svn');
        return array_diff($arr, $exclude);
    }

    /**
    * get all available themes
    *
    * @return array
    */
    protected function getAvailableThemes() {
        $themePath = Yii::app()->themeManager->basePath;
        $availableNameThemes = $this->readDir($themePath);
        $availableThemes = array();
        foreach ($availableNameThemes as $path) {
            $name = ltrim(str_replace($themePath, '', $path), DIRECTORY_SEPARATOR);
            $availableThemes[$name] = $path;
        }
        return $availableThemes;
    }

    /**
    * get array name => absolute path of css file
    *
    * @param mixed $themePath
    * @return array
    */
    protected function getCssFiles($themePath) {
        $stylePath = $themePath.DIRECTORY_SEPARATOR.'styles';
        $cssNameFiles = $this->readDir($stylePath);
        $cssFiles = array();
        foreach ($cssNameFiles as $path) {
            $name = ltrim(str_replace($stylePath, '', $path), DIRECTORY_SEPARATOR);
            $cssFiles[$name] = $path;
        }
        return $cssFiles;
    }

    /**
    * get parser file, content of current file
    * update content of current file
    *
    * @param array $cssFiles
    * @param array $templateFiles
    * @return array
    */
    protected function configThemeEditor($cssFiles) {
        $currentFile=null;
        $parserFile = '"parsexml.js", "tokenizejavascript.js", "parsejavascript.js", "tokenizephp.js", "parsecss.js"';
        if (isset($_GET['file']) === true) {
            $file = null;
            $filename = $_GET['file'];
            if (array_key_exists($filename, $cssFiles) === true) {
                $file = $cssFiles[$filename];
            }
            if ($file !== null && file_exists($file) === true) {
                if (isset($_POST['code']) === true) {
                    $currentFile = stripcslashes($_POST['code']);
                    if (is_writeable($file) === true) {
                        file_put_contents($file, $currentFile);
                        $this->message = "File {$file} has been saved.";
                    } else {
                        Yii::app()->user->setFlash('error', "Fail write file. File {$file} is readonly.");
                    }
                } else {
                    $currentFile = file_get_contents($file);
                }
            } else {
                Yii::app()->user->setFlash('error', "File '{$filename}' not found.");
            }

            if (strpos($filename, '.css') === false) {
                $parserFile .= ', "parsephp.js", "parsephphtmlmixed.js"';
            }
        }
        return array($currentFile, $parserFile);
    }
}