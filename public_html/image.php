<?php
/**
* Send image file content to web browser.
* 
* This script is mainly used for thumbnail images genarated on the fly
* by resizing the original image and caching into a cache folder. The
* image path must be relative to this file and passed in 'thumb' param
* 
* You can use it to send a normal image file to web browser as well if
* image path is passed in 'path' param
*/
include_once('protected/modules/Core/extensions/web/helpers/FlexImage.php');

/**
* Send image to web browser
* 
* @param string $imagePath
*/
function getImage($imagePath){  
    $size = getimagesize($imagePath);
    if (substr($size['mime'], 0, 6) != 'image/'){
        return;
    } else {
        header("content-type:{$size['mime']}");
        echo file_get_contents($imagePath);
    }
}

//If used to simply send an image
if(isset($_GET['path']))
    return getImage($_GET['path']);
//If used to send a thumbnail image
if(!isset($_GET['thumb'])) return;

$newPath = dirname(__FILE__).'/protected/runtime/fleximage_cache/';
$cachePath = $newPath.basename($_GET['thumb']);

$cached = file_exists($cachePath);
$changed = false;
$imgPath = dirname(__FILE__).'/'.FlexImage::createImageFilename($_GET['thumb'], $changed);
if(!$cached || $changed){
    preg_match('/_(\d+)x(\d+)_(\d)+\.(jpg|gif|png|jpeg)/', $_GET['thumb'], $matches);
    if (!FlexImage::isImage($imgPath)) die;
    $Fimage=new FlexImage($imgPath,$newPath);
    if (!$Fimage->resize($cachePath, $matches[1], $matches[2]))
        die ('Cannot resize');
}
getImage($cachePath);
?>
