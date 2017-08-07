<?php
/**
-------------------------
GNU GPL COPYRIGHT NOTICES
-------------------------
This file is part of FlexicaCMS.

FlexicaCMS is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

FlexicaCMS is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with FlexicaCMS.  If not, see <http://www.gnu.org/licenses/>.*/

/**
 * $Id$
 *
 * @author FlexicaCMS team <contact@flexicacms.com>
 * @link http://www.flexicacms.com/
 * @copyright Copyright &copy; 2009-2010 Gia Han Online Solutions Ltd.
 * @license http://www.flexicacms.com/license.html
 */

define('MEMORY_TO_ALLOCATE', '100M');
define('DEFAULT_QUALITY', 100);
//CACHE_DIR need trailing slash
//define('CACHE_DIR', '/../../../../../../runtime/fleximage_cache/');
//define('CACHE_DIR', YiiBase::getPathOfAlias('application.runtime').'/fleximage_cache/');
/**
* Class FlexImage
* @author Hung Nguyen
* @version 1.0
*/
class FlexImage{
    public $mime = null;
    public $width;
    public $height;
    public $sourcePath;
    public $cachePath;
    public $errorMessage = null;

    protected $image;
    protected $outputFunction;
    protected $creationFunction;
    protected $doSharpen;
    protected $convertedMime;
    protected $quality;    
    
    public function __construct($imagePath,$cachePath){
        // If file exists
        //if (!file_exists($imagePath) || @exif_imagetype($imagePath) === false){
//        if (@exif_imagetype($imagePath) === false){
//            $this->errorMessage = "Error: File $imagePath does not exist.";
//            return null;
//        }
            
        // Get the size and MIME type of the requested image
        $size    = getimagesize($imagePath);
        $this->mime    = $size['mime'];
        if (substr($this->mime, 0, 6) != 'image/'){
            $this->errorMessage = "Error: File $imagePath is not an image.";
            return null;
        }
            

        $this->sourcePath = $imagePath;
        $this->cachePath = $cachePath;
        $this->width            = $size[0];
        $this->height            = $size[1];
        // Set up the appropriate image handling functions based on the original image's mime type
        $mime = '';
        switch ($size['mime'])
        {
            case 'image/gif':
                // We will be converting GIFs to PNGs to avoid transparency issues when resizing GIFs
                // This is maybe not the ideal solution, but IE6 can suck it
                $creationFunction    = 'ImageCreateFromGif';
                $outputFunction        = 'ImageGif';
                $mime                = 'image/png'; // We need to convert GIFs to PNGs
                $doSharpen            = FALSE;
                $quality            = round(10 - (DEFAULT_QUALITY / 10)); // We are converting the GIF to a PNG and PNG needs a compression level of 0 (no compression) through 9
            break;
            
            case 'image/x-png':
            case 'image/png':
                $creationFunction    = 'ImageCreateFromPng';
                $outputFunction        = 'ImagePng';
                $doSharpen            = FALSE;
                $quality            = round(10 - (DEFAULT_QUALITY / 10)); // PNG needs a compression level of 0 (no compression) through 9
            break;
            
            default:
                $creationFunction    = 'ImageCreateFromJpeg';
                $outputFunction         = 'ImageJpeg';
                $quality                = DEFAULT_QUALITY;
                $doSharpen            = TRUE;
            break;
        }
        
        $this->creationFunction = $creationFunction;
        $this->outputFunction = $outputFunction;
        $this->doSharpen = $doSharpen;
        $this->quality = $quality;
        $this->convertedMime = $mime == '' ? $this->mime : $mime;
    }
    /**
    * Check if a given file is an image
    * 
    * This is an utility method that can be called directly 
    * without initializing the FlexImage object
    * 
    * @param mixed $imagePath
    */
    public static function isImage($imagePath){
        // If file exists
//        if (!file_exists($imagePath) || exif_imagetype($imagePath) === false){
        if (!file_exists($imagePath)){
            return false;
        }

        // Get the size and MIME type of the requested image
        $size    = GetImageSize($imagePath);
        $mime    = $size['mime'];

        // Make sure that the requested file is actually an image
        if (substr($mime, 0, 6) != 'image/')
        {
            return false;
        }
        return true;
    }
    
    /**
    * Resize image
    * 
    * If filepath for the new resized image is not set, the function return an array with raw
    * image data in $return['image']
    * 
    * This also try to sharpen JPEG image.
    * 
    * @param string $imagePath absolute path of the original image
    * @param string $newPath absolute path of the target resized image
    * @param int $maxWidth
    * @param int $maxHeight
    * @param string $bgColor Optional, HEX color for filling transparent PNGs, no # prefix
    * @param string $cropRatio Optional, ratio of width to height to crop filnal image (1:1 or 3:2)
    * @param bool $noCache read image form cach if turn On
    * @param float $quality JPEC image quality, default is 100%
    */
    public function resize($newPath = null, $maxWidth, $maxHeight, $bgColor = false, $cropRatio = '', $noCache = false){
        /*$maxWidth=100;
        $maxHeight=100;*/
        //File not exist or file is not an image
        if (!$this->mime) return false;        
        $creationFunction = $this->creationFunction;
        
        if (is_array($bgColor) && count($bgColor) == 3)
        { 
            // in case rgb is stored in a (r,g,b) array
            $color = dechex($bgColor[0]).dechex($bgColor[1]).dechex($bgColor[2]);
        } 
        else if (is_array($bgColor) && count($bgColor) != 3)
        {
            $color = false;
        }
        else if ($bgColor !== false)
            $color        = preg_replace('/[^0-9a-fA-F]/', '', $bgColor);
        else
            $color = false;

        
        $maxWidth        = (isset($maxWidth)) ? (int) $maxWidth : 0;
        $maxHeight        = (isset($maxHeight)) ? (int) $maxHeight : 0;

        // If we don't have a max width or max height, OR the image is smaller than both
        
        // we do not want to resize it, so we simply output the original image and exit        
        if ((!$maxWidth && !$maxHeight) || (!$color && $maxWidth >= $this->width && $maxHeight >= $this->height))
        {
            if (!is_null($newPath)){
                copy($this->sourcePath, $newPath);
                return true;
            }
            
            $this->image = $creationFunction($this->sourcePath);   
            return true;
        }

        // Ratio cropping
        $offsetX    = 0;
        $offsetY    = 0;

        if ($cropRatio != '')
        {
            $cropRatio        = explode(':', (string) $cropRatio);
            if (count($cropRatio) == 2)
            {
                $ratioComputed        = $this->width / $this->height;
                $cropRatioComputed    = (float) $cropRatio[0] / (float) $cropRatio[1];
                
                if ($ratioComputed < $cropRatioComputed)
                { // Image is too tall so we will crop the top and bottom
                    $origHeight    = $this->height;
                    $this->height  = $this->width / $cropRatioComputed;
                    $offsetY    = ($origHeight - $this->height) / 2;
                }
                else if ($ratioComputed > $cropRatioComputed)
                { // Image is too wide so we will crop off the left and right sides
                    $origWidth    = $this->width;
                    $this->width        = $this->height * $cropRatioComputed;
                    $offsetX    = ($origWidth - $this->width) / 2;
                }
            }
        }

        // Setting up the ratios needed for resizing. We will compare these below to determine how to
        // resize the image (based on height or based on width)
        $xRatio        = $maxWidth / $this->width;
        $yRatio        = $maxHeight / $this->height;

        if ($xRatio * $this->height < $maxHeight)
        { // Resize the image based on width
            $tnHeight    = ceil($xRatio * $this->height);
            $tnWidth    = $maxWidth;
        }
        else // Resize the image based on height
        {            
            $tnWidth    = ceil($yRatio * $this->width);
            $tnHeight    = $maxHeight;
        }
        
        // Before we actually do any crazy resizing of the image, we want to make sure that we
        // haven't already done this one at these dimensions. To the cache!
        // Note, cache must be world-readable

        // We store our cached image filenames as a hash of the dimensions and the original filename
        $resizedImageSource        = $tnWidth . 'x' . $tnHeight . 'x' . $this->quality;
        if ($color)
            $resizedImageSource    .= 'x' . $color;
        if (isset($cropRatio))
            $resizedImageSource    .= 'x' . (string) $cropRatio;
        $resizedImageSource        .= '-' . basename($this->sourcePath);

        $resizedImage    = md5($resizedImageSource);
          
        //$resized        = CACHE_DIR . $resizedImage;
        $resized        = $this->cachePath.$resizedImage;
        // Check the modified times of the cached file and the original file.
        // If the original file is older than the cached file, then we simply serve up the cached file
        if (!isset($noCache) && file_exists($resized))
        {
            $imageModified    = filemtime($this->sourcePath);
            $thumbModified    = filemtime($resized);
            
            if($imageModified < $thumbModified) {
                if (!is_null($newPath)){
                    copy($resized, $newPath);
                    return true;
                }
                $this->image = $creationFunction($resized); 
                return true;
            }
        }

        // We don't want to run out of memory
        $oldMemoryLimit = ini_get('memory_limit');
        ini_set('memory_limit', MEMORY_TO_ALLOCATE);

        // Set up a blank canvas for our resized image (destination)
        $dst    = imagecreatetruecolor($tnWidth, $tnHeight);

        // Read in the original image
        $src    = $creationFunction($this->sourcePath);

        if (in_array($this->mime, array('image/gif', 'image/png')))
        {
            if (!$color)
            {
                // If this is a GIF or a PNG, we need to set up transparency
                imagealphablending($dst, false);
                imagesavealpha($dst, true);
            }
            else
            {
                // Fill the background with the specified color for matting purposes
                if ($color[0] == '#')
                    $color = substr($color, 1);
                
                $background    = FALSE;
                
                if (strlen($color) == 6)
                    $background    = imagecolorallocate($dst, hexdec($color[0].$color[1]), hexdec($color[2].$color[3]), hexdec($color[4].$color[5]));
                else if (strlen($color) == 3)
                    $background    = imagecolorallocate($dst, hexdec($color[0].$color[0]), hexdec($color[1].$color[1]), hexdec($color[2].$color[2]));
                if ($background)
                    imagefill($dst, 0, 0, $background);
            }
        }

        // Resample the original image into the resized canvas we set up earlier
        ImageCopyResampled($dst, $src, 0, 0, $offsetX, $offsetY, $tnWidth, $tnHeight, $this->width, $this->height);
        $this->width = $tnWidth;
        $this->height = $tnHeight;

        if ($this->doSharpen)
        {
            // Sharpen the image based on two things:
            //    (1) the difference between the original size and the final size
            //    (2) the final size
            $sharpness    = $this->findSharp($this->width, $tnWidth);
            
            $sharpenMatrix    = array(
                array(-1, -2, -1),
                array(-2, $sharpness + 12, -2),
                array(-1, -2, -1)
            );
            $divisor        = $sharpness;
            $offset            = 0;
            imageconvolution($dst, $sharpenMatrix, $divisor, $offset);
        }

        // Make sure the cache exists. If it doesn't, then create it 
        //echo CACHE_DIR;die;       
        //if (!file_exists(CACHE_DIR))
        if (!file_exists($this->cachePath))
            mkdir($this->cachePath, 0777);
        // Make sure we can read and write the cache directory
        
        if (!is_readable($this->cachePath))
        {
            $this->errorMessage = 'Error: the cache directory is not readable';
            ini_set('memory_limit', $oldMemoryLimit);
            return false;
        }
        else if (!is_writable($this->cachePath))
        {
            $this->errorMessage = 'Error: the cache directory is not writable';
            ini_set('memory_limit', $oldMemoryLimit);
            return false;
        }

        $outputFunction = $this->outputFunction;
        // Write the resized image to the cache
        $outputFunction($dst, $resized, $this->quality);

        // Save to file
       
        if (!is_null($newPath))
            $outputFunction($dst, $newPath, $this->quality);
        else
            $this->image = $creationFunction($resized);
        // Clean up the memory
        ImageDestroy($src);
        ImageDestroy($dst);

        ini_set('memory_limit', $oldMemoryLimit);
        return true;
    }

    protected function findSharp($orig, $final) // function from Ryan Rud (http://adryrun.com)
    {
        $final    = $final * (750.0 / $orig);
        $a        = 52;
        $b        = -0.27810650887573124;
        $c        = .00047337278106508946;
        
        $result = $a + $b * $final + $c * $final * $final;
        
        return max(round($result), 0);
    }

    /**
    * Create a canvas filled with given color
    * 
    * @param int $width
    * @param int $height
    * @param array $color array('r' => 255, 'g' => 255, 'b' => 255)
    * 
    * @return resource
    */
    public static function canvas($width, $height, $color = array(255, 255, 255)) {
        
        $image = imageCreateTrueColor($width, $height);
        
        list($r, $g, $b) = $color;
        imageFill($image, 1, 1, 
            imageColorAllocate($image, $r, $g, $b)
            );

        return $image;
    }
    
    /**
    * Copy source into target image
    * 
    * @param mixed $target
    * @param mixed $source
    * @param mixed $destination_x
    * @param mixed $destination_y
    * @param mixed $source_w
    * @param mixed $source_h
    * @return bool
    */
    public function copy(&$target, &$source, $destination_x, $destination_y, $source_w, $source_h) {

        imageAlphaBlending($target, true);
        $r = imageCopy($target, $source,
            $destination_x, $destination_y,
            0, 0,
            $source_w, $source_h
            );
        imageAlphaBlending($target, false);
        
        return $r;
    }
    
    public function crop($newPath, $x, $y, $x1, $y1) {
        $width = $x1-$x;
        $height = $y1-$y;
        $creationFunction = $this->creationFunction;
        $outputFunction = $this->outputFunction;
        $src    = $creationFunction($this->sourcePath);
        $dst    = imagecreatetruecolor($width, $height);
        imagecopyresampled($dst, $src, 0, 0, $x, $y, $width, $height, $width, $height);        
        
        imagedestroy($src);
        $outputFunction($dst,$newPath,$this->quality);
        imagedestroy($dst);
    }
    
    public function frame($newPath = null, $frameWidth, $frameHeight, $bgColor = array(255, 255, 255)){
        if (!$this->mime) return false;
        
        if (!$this->resize(null, $frameWidth, $frameHeight, $bgColor)){
            return false;
        }
        $left = round(($frameWidth - $this->width)/2);
        $top  = round(($frameHeight - $this->height)/2);
        
        $canvas = self::canvas($frameWidth, $frameHeight, $bgColor);
        $this->copy($canvas, $this->image, $left, $top, $this->width, $this->height);
        
        if (!is_null($newPath)){
            $outputFunction = $this->outputFunction;
            $outputFunction($canvas, $newPath, $this->quality);
            return true;
        }else{
            $this->image = $canvas;
            $this->width = $frameWidth;
            $this->height = $frameHeight;
            return true;
        }
    }
    
    /**
    * Create a thumb image file name in form of 'image_123x123_timestamp.jpg'
    * 
    * @param string $filename 
    * @param int $width new width
    * @param int $height new height
    */
    public static function createThumbFilename($filePath, $width, $height)
    {        
        if (!file_exists($filePath)) return '';
        $baseName = basename($filePath);
        $ext = end(explode('.', $baseName));
        $name = preg_replace('/\.'.$ext.'$/', '', $baseName);
        $lastChanged = filemtime($filePath);
        return str_replace($baseName, "{$name}_{$width}x{$height}_{$lastChanged}.{$ext}", $filePath);
        
    }
    
    public static function createImageFilename($thumbPath, &$changed = false)
    {
        preg_match('/_(\d+)\.(jpg|gif|png|jpeg)/i',$thumbPath,$matches);
        $time = $matches[1];

        $filePath = preg_replace('/_\d+x\d+_\d+\.(jpg|gif|png|jpeg)/i', '.$1', $thumbPath);
        if(file_exists($filePath) && filemtime($filePath) != $time)
            $changed = true;
        else
            $changed = false;
            
        return $filePath;
    }
}

?>