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


class Utility
{      
    const TAG_STRIP = 1;
    const TAG_CONVERT = 2;
    const TAG_RESERVE = 3;
    const IMAGE_CACHE_FOLDER = '/uploadedfiles/cached/';
    /**
    * Subsite first N word in the text as an intro text
    * 
    * @param mixed $originalString
    * @param mixed $wordsCount
    * @param int $tagFlag Use Utility::TAG_ constants
    * @return string
    */
    public static function getFirstWordsFromString($originalString, $wordsCount, $tagFlag = Utility::TAG_CONVERT)
    {
        $originalString = str_replace('<p>&nbsp;</p>','', $originalString);
        $words = explode(' ', stripslashes($originalString));
        if(count($words) > $wordsCount) 
        {
            $str = implode(' ', array_slice($words, 0, $wordsCount)).'...';
        } else {
            $str = $originalString;
        }
        
        if ($tagFlag == Utility::TAG_STRIP)
            $str = strip_tags($str);
        if ($tagFlag == Utility::TAG_CONVERT){
            $str = preg_replace(array('/<h1>/','/<h2\>/','/<h3\>/','/<h4>/','/<h5>/',
                                      '/<\/h1>/','/<\/h2>/','/<\/h3>/','/<\/h4>/','/<\/h5>/',
                                      ),
                                array('<p><strong><em>','<p><strong><em>','<p><strong>','<p><strong>','<p><strong>',
                                      '</em></strong></p>','</em></strong></p>','</strong></p>','</strong></p>','</strong></p>',
                                      ), 
                                $str);
            $str = strip_tags($str, '<p>,<br>,<strong>,<em>,<ul>,<li>,<a>');
        }
        $purifier = new CHtmlPurifier();
        $str = $purifier->purify($str); 

            
        return $str;
    }
    
    /**
    * @param string $input
    * @param int $numWords
    * @param mixed $restoreTags false|auto|purifier
    * 
    * @return string
    */
    public static function truncateWords($input, $numWords=50, $restoreTags='auto')
    {
        if (preg_match("/(\S+\s*){0,$numWords}/", $input, $matches))
        {
            $shortDesc = trim($matches[0]);
            switch ($restoreTags)
            {
                case 'auto':
                    $shortDesc = self::restoreTags($shortDesc);
                    break;
                case 'purifier':
                    $purifier = new CHtmlPurifier();
                    $shortDesc = $purifier->purify($shortDesc);
                    break;
            }
        }
        else
            $shortDesc = $input;
        return $shortDesc;
    }
    
    public static function restoreTags($input)
    {
        $opened = array();

        // loop through opened and closed tags in order
        if(preg_match_all("/<(\/?[a-z]+)>?/i", $input, $matches)) {
            foreach($matches[1] as $tag) {
                if(preg_match("/^[a-z]+$/i", $tag, $regs)) {
                    // a tag has been opened
                    if(strtolower($regs[0]) != 'br') $opened[] = $regs[0];
                } elseif(preg_match("/^\/([a-z]+)$/i", $tag, $regs)) {
                    // a tag has been closed
                    unset($opened[array_pop(array_keys($opened, $regs[1]))]);
                }
            }
        }

        // close tags that are still open
        if($opened) {
            $tagstoclose = array_reverse($opened);
            foreach($tagstoclose as $tag) $input .= "</$tag>";
        }

        return $input;
    }
          
    public static function toAscii($from, $to, $str)
    {
        mb_internal_encoding("UTF-8");        
        for ($i=0; $i<mb_strlen($from); $i++)
        {
            $fromChar = mb_substr($from, $i,1);
            $toChar = mb_substr($to, $i,1);
            $str = mb_eregi_replace("\\".$fromChar, $toChar, $str);
        }
        //return $str;
        return mb_convert_encoding($str,'ASCII');
    }
    
    /**
    * Create an URL alias for a string that may contain special characters
    * 
    * @param mixed $str
    * @return string
    */
    public static function urlAlias($str)
    {        
        $uni_from = ' ()!$?:,&+=><ÀàÁáÂâẢảÃãÄäÅåĀāĂăĄąǞǟǺǻÆæǼǽẠạẬậẶặẤấẮắẦầẪẫẨẩẲẳẴẵẰằḂḃĆćÇçČčĈĉĊċḐḑĎďḊḋĐđðǄǆÈèÉéĚěÊêỂểËëĒēĔĕĘęĖėƷʒǮǯẸẹỆệẺẻỄễẾếỀềẼẽḞḟǴǵĢģǦǧĜĝĞğĠġĤĥĦħÌìÍíÎîĨĩÏïĪīĬĭĮįİıỊịỈỉĴĵḰḱĶķǨǩĹĺĻļĽľĿŀŁłṀṁŃńŅņŇňÑñŉŊŋǊǌÒòÓóƠơỜờÔôÕõÖöŌōŎŏØøŐőǾǿŒœỌọỢợỘộỒồỞởỎỏỚớỐốỔổỖỗỠỡṖṗŔŕŖŗŘřɼŚśŞşŠšŜŝṠṡſßŢţŤťṪṫŦŧÞþÙùÚúÛûỦủŨũÜŮůŪūŬŭŲųŰűỤụỰựÙừỨứƯưỬửỮữẀẁẂẃŴŵẄẅỲỳÝýŶŷŸÿỴỵỸỹỶỷŹźŽžŻż`~\`';    
        $uni_to = '-____________AaAaAaAaAaAaAaAaAaAaAaAaAaAaAaAaAaAaAaAaAaAaAaAaAaBbCcCcCcCcCcDdDdDdDddDdEeEeEeEeEeEeEeEeEeEeEeEeEeEeEeEeEeEeEeFfGgGgGgGgGgGgHhHhIiIiIiIiIiIiIiIiIiIiIiJjKkKkKkLlLlLlLlLlMmNnNnNnNnnNnNnOoOoOoOoOoOoOoOoOoOoOoOoOoOoOoOoOoOoOoOoOoOoOoOoPpRrRrRrrSsSsSsSsSsSsTtTtTtTtTtUuUuUuUuUuUUuUuUuUuUuUuUuUuUuUuUuUuWwWwWwWwYyYyYyYyYyYyYyZzZzZz___';
        $from = "\\'\"/.@#%^*".$uni_from;
        $to   = '__________'.$uni_to;
       
        return strtolower(self::toAscii($from, $to, $str));
    }
    
    /**
    * check duplicate alias in database
    * 
    * @param mixed $model
    * @param String $alias
    * @param Boolean $flag
    */
    public function checkDuplicateAlias($model, $alias)
    {             
        $criteria = new CDbCriteria();
        $criteria->condition = "Alias = :alias";
        $criteria->params = array(':alias' => $alias);
        $objectList = $model->model()->count($criteria);        
            
        if($objectList > 0){                         
            for($i=2; $i<99; $i++){                
                $criteria->condition = "Alias = :alias";
                $criteria->params = array(':alias' => $alias . '_' . $i);
                $exist = $model->model()->count($criteria);
                if($exist == 0)
                    return $alias . '_' . $i;
            }
        }        
        return $alias;
    }
    
    /**
    * Create an alias
    * 
    * @param String $title
    * @param Int $id
    * @return String alias
    */
    
    public function createAlias($model, $title)
    {        
        $alias = trim($title);
        $alias = self::urlAlias($title);        
        return self::checkDuplicateAlias($model, $alias);
    }
    
    /**
    * Resize image
    * @param String $oldImagePath: Path of image you want to resize
    * @param String $newImagePath: new path for new image
    * @param String $width: Path of image you want to resize
    * @param String $height: Path of image you want to resize
    */
    public static function resize($oldImagePath, $newImagePath, $width, $height)
    {
//        Yii::import('application.utilities.FlexImage');
        include_once(dirname(__FILE__).'/../extensions/fleximage/fleximage.php');
        $fleximage = new FlexImage($oldImagePath);
        if ($fleximage->errorMessage != null)
            return $fleximage->errorMessage;
        else
            return $fleximage->resize($newImagePath, $width, $height, true);    
    }
    
    /**
    * Create a thumb image file name in form of 'old_image_123x123_timestamp.jpg'
    * 
    * @param string $filename must be relative to parent folder of Application's base path
    * @param int $width new width
    * @param int $height new height
    */
    public static function createThumbFilename($filePath, $width, $height)
    {
        $absFilePath = Yii::getPathOfAlias('webroot').'/'.ltrim($filePath,'/');
        if (!file_exists($absFilePath)) return '';
        $baseName = basename($filePath);
        $ext = end(explode('.', $baseName));
        $name = preg_replace('/\.'.$ext.'$/', '', $baseName);
        //list($name, $ext) = explode('.', $baseName);
        $lastChanged = filemtime($absFilePath);
//        Yii::log("Absolute path: {$absFilePath}, Last changed: {$lastChanged}", CLogger::LEVEL_INFO);
        return str_replace($baseName, "{$name}_{$width}x{$height}_{$lastChanged}.{$ext}", $filePath);
        
    }
    
    public static function createImageFilename($thumbPath, &$changed = false)
    {
        preg_match('/_(\d+)\.(jpg|gif|png)/i',$thumbPath,$matches);
        $time = $matches[1];

        $filePath = preg_replace('/_\d+x\d+_\d+\.(jpg|gif|png)/i', '.$1', $thumbPath);
        if(file_exists($filePath) && filemtime($filePath) != $time)
            $changed = true;
        else
            $changed = false;
            
        return $filePath;
    }
    
    /**
    * Generate a random string with a specific length
    * 
    * @param mixed $length
    */
    public static function generateRandomString($length = 8)
    {
        $chars = "abcdefghijkmnopqrstuvwxyz023456789";
        srand((double)microtime()*1000000);
        $i = 0;
        $string = '' ;

        while ($i <= $length) {
            $num = rand() % 33;
            $tmp = substr($chars, $num, 1);
            $string = $string . $tmp;
            $i++;
        }

        return $string;
    }
    
    /**
    * Open an URL using CURL
    * 
    * @param string $url Url to open
    * @param string $referer referer url
    * @param string $postfields data to post, in querystring format
    * @param string $method post method, possible value: post, get
    * @param bool $useSSL whether to use SSL or not
    * @param bool $header prepend HTTP header into response
    * @param array $httpheader HTTP header to post with the request
    * @param string $cookieFile path to cookie file to save and load cookies, must be r/writable
    * 
    * @return string
    */
    public function curlRequest($url, $referer = '', $postfields = '', $method = 'get', $useSSL = false, $header = false, $httpheader = array(), $cookieFile = '')
    {
        //Set agent
        $agent = @$_SERVER['HTTP_USER_AGENT'];
        if(empty($agent))
            $agent = "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.1) Gecko/20061204 Firefox/2.0.0.1";
            
        if ($method=='get' && $postfields != '')
            $url .= '?'.$postfields;

        $ch = curl_init(); 
        curl_setopt($ch, CURLOPT_URL, $url); 
        curl_setopt($ch, CURLOPT_USERAGENT, $agent); 
        
        if ($postfields != '')
            curl_setopt($ch, CURLOPT_POST, true);
        else
            curl_setopt($ch, CURLOPT_HTTPGET, true);
            
        if ($method == 'post');
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
        
        if ($method == 'get')
            curl_setopt($ch, CURLOPT_HTTPGET, true);
        
        if ($header)
            curl_setopt($ch, CURLOPT_HEADER, 1); 
        
        $httpheader[] = "ContentType: application/xml; charset=utf-8";
        $httpheader[] = "Cache-Control: max-age=0";
        $httpheader[] = "Connection: keep-alive";
        $httpheader[] = "Keep-Alive: 300";
        $httpheader[] = "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7";
        $httpheader[] = "Pragma: ";
        curl_setopt($ch, CURLOPT_HTTPHEADER, $httpheader);
            
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); 
        
        //The contents of the "Referer: " header to be used in a HTTP request.
        if ($referer != '')
            curl_setopt($ch, CURLOPT_REFERER, $referer);
        
        if (!empty($cookieFile))
        {
            $cookie_file_path = realpath($cookieFile);
            // The name of the file containing the cookie data. The cookie file can be in Netscape format, or just plain HTTP-style headers dumped into a file. 
            curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file_path);
            // The name of a file to save all internal cookies to when the connection closes. 
            curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file_path); 
            
            if ($method == 'post')
            {
                $file = fopen($cookie_file_path, 'w');
                fwrite($file, '');
                fclose($file);
            }
        }
        
        if ($useSSL)
        {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0); 
        }
        
        $result = curl_exec($ch); // grab URL and pass it to the variable.
        curl_close($ch); // close curl resource, and free up system resources.
        
        return $result;
    }
    
    /**
     * Get Ip of user
     */
    public static function getIP() {

        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        }else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }       
        return $ip;
    }
    
    public static function deleteDir($dir)
    {
        $files = glob( $dir . '*', GLOB_MARK );
        foreach( $files as $file ){
            if( is_dir( $file ) )
                self::deleteDir( $file );
            else
                unlink( $file );
        }
      
        if (is_dir($dir)) rmdir( $dir );
    }
    
    /**
    * Get an array of file names in a directory
    * 
    * @param mixed $dirPath
    * @return array An array of files or null if path does not exist
    */
    public static function getDirFiles($dirPath)
    {
        if (!is_dir($dirPath)) return null;
        
        $dh = opendir($dirPath);
        $files = array();
        if ($dh !== FALSE)
        {
            while (($file = readdir($dh)) !== FALSE)
            {
                if (filetype($dirPath . '/' . $file) == 'file')
                {
                    $files[] = $file;
                }
            }
        }
        closedir($dh);
        return $files;
    }
    
    /**
    * Get visitor's country code
    * 
    * @return string $countryCode Country Code
    */
    public static function getVisitorCountryCode()
    {
        $ip = $_SERVER['REMOTE_ADDR'];
        include_once Yii::getPathOfAlias('application').'/modules/Statistics/services/geoip.php';
        $gi = geoip_open(Yii::app()->assetManager->basePath.'/GeoIP.dat', GEOIP_STANDARD);
        $countryCode = geoip_country_code_by_addr($gi, $ip);
        geoip_close($gi);
        return $countryCode;
    }
    
    /**
    * Get cached array of shared categories
    */
    public static function getCachedCategories() {
        //Include cached file of category cache
        $file = Yii::app()->getBasePath().'/runtime/cache/categories.php';
        if (!file_exists($file)){
            //cache does not exist, build it
            FSM::_run('Core.Category.cache', array());
        }
        include($file);
        return $categories;    
    }
    
    public static function safeParam($name, $default) {
        if (defined($name)) {
            return $name;
        } else {
            return $default;
        }
    }
    
    public static function getFriendlyTimezoneList()
    {
        return array(
            'Pacific/Midway' => '(GMT-11:00) Midway Island, Samoa',
            'America/Adak' => '(GMT-10:00) Hawaii-Aleutian',
            'Etc/GMT+10' => '(GMT-10:00) Hawaii',
            'Pacific/Marquesas' => '(GMT-09:30) Marquesas Islands',
            'Pacific/Gambier' => '(GMT-09:00) Gambier Islands',
            'America/Anchorage' => '(GMT-09:00) Alaska',
            'America/Ensenada' => '(GMT-08:00) Tijuana, Baja California',
            'Etc/GMT+8' => '(GMT-08:00) Pitcairn Islands',
            'America/Los_Angeles' => '(GMT-08:00) Pacific Time (US & Canada)',
            'America/Denver' => '(GMT-07:00) Mountain Time (US & Canada)',
            'America/Chihuahua' => '(GMT-07:00) Chihuahua, La Paz, Mazatlan',
            'America/Dawson_Creek' => '(GMT-07:00) Arizona',
            'America/Belize' => '(GMT-06:00) Saskatchewan, Central America',
            'America/Cancun' => '(GMT-06:00) Guadalajara, Mexico City, Monterrey',
            'Chile/EasterIsland' => '(GMT-06:00) Easter Island',
            'America/Chicago' => '(GMT-06:00) Central Time (US & Canada)',
            'America/New_York' => '(GMT-05:00) Eastern Time (US & Canada)',
            'America/Havana' => '(GMT-05:00) Cuba',
            'America/Bogota' => '(GMT-05:00) Bogota, Lima, Quito, Rio Branco',
            'America/Caracas' => '(GMT-04:30) Caracas',
            'America/Santiago' => '(GMT-04:00) Santiago',
            'America/La_Paz' => '(GMT-04:00) La Paz',
            'Atlantic/Stanley' => '(GMT-04:00) Faukland Islands',
            'America/Campo_Grande' => '(GMT-04:00) Brazil',
            'America/Goose_Bay' => '(GMT-04:00) Atlantic Time (Goose Bay)',
            'America/Glace_Bay' => '(GMT-04:00) Atlantic Time (Canada)',
            'America/St_Johns' => '(GMT-03:30) Newfoundland',
            'America/Araguaina' => '(GMT-03:00) UTC-3',
            'America/Montevideo' => '(GMT-03:00) Montevideo',
            'America/Miquelon' => '(GMT-03:00) Miquelon, St. Pierre',
            'America/Godthab' => '(GMT-03:00) Greenland',
            'America/Argentina/Buenos_Aires' => '(GMT-03:00) Buenos Aires',
            'America/Sao_Paulo' => '(GMT-03:00) Brasilia',
            'America/Noronha' => '(GMT-02:00) Mid-Atlantic',
            'Atlantic/Cape_Verde' => '(GMT-01:00) Cape Verde Is.',
            'Atlantic/Azores' => '(GMT-01:00) Azores',
            'Europe/Belfast' => '(GMT) Greenwich Mean Time : Belfast',
            'Europe/Dublin' => '(GMT) Greenwich Mean Time : Dublin',
            'Europe/Lisbon' => '(GMT) Greenwich Mean Time : Lisbon',
            'Europe/London' => '(GMT) Greenwich Mean Time : London',
            'Africa/Abidjan' => '(GMT) Monrovia, Reykjavik',
            'Europe/Amsterdam' => '(GMT+01:00) Amsterdam, Berlin, Bern, Rome, Stockholm, Vienna',
            'Europe/Belgrade' => '(GMT+01:00) Belgrade, Bratislava, Budapest, Ljubljana, Prague',
            'Europe/Brussels' => '(GMT+01:00) Brussels, Copenhagen, Madrid, Paris',
            'Africa/Algiers' => '(GMT+01:00) West Central Africa',
            'Africa/Windhoek' => '(GMT+01:00) Windhoek',
            'Asia/Beirut' => '(GMT+02:00) Beirut',
            'Africa/Cairo' => '(GMT+02:00) Cairo',
            'Asia/Gaza' => '(GMT+02:00) Gaza',
            'Africa/Blantyre' => '(GMT+02:00) Harare, Pretoria',
            'Asia/Jerusalem' => '(GMT+02:00) Jerusalem',
            'Europe/Minsk' => '(GMT+02:00) Minsk',
            'Asia/Damascus' => '(GMT+02:00) Syria',
            'Europe/Moscow' => '(GMT+03:00) Moscow, St. Petersburg, Volgograd',
            'Africa/Addis_Ababa' => '(GMT+03:00) Nairobi',
            'Asia/Tehran' => '(GMT+03:30) Tehran',
            'Asia/Dubai' => '(GMT+04:00) Abu Dhabi, Muscat',
            'Asia/Yerevan' => '(GMT+04:00) Yerevan',
            'Asia/Kabul' => '(GMT+04:30) Kabul',
            'Asia/Yekaterinburg' => '(GMT+05:00) Ekaterinburg',
            'Asia/Tashkent' => '(GMT+05:00) Tashkent',
            'Asia/Kolkata' => '(GMT+05:30) Chennai, Kolkata, Mumbai, New Delhi',
            'Asia/Katmandu' => '(GMT+05:45) Kathmandu',
            'Asia/Dhaka' => '(GMT+06:00) Astana, Dhaka',
            'Asia/Novosibirsk' => '(GMT+06:00) Novosibirsk',
            'Asia/Rangoon' => '(GMT+06:30) Yangon (Rangoon)',
            'Asia/Bangkok' => '(GMT+07:00) Bangkok, Hanoi, Jakarta',
            'Asia/Krasnoyarsk' => '(GMT+07:00) Krasnoyarsk',
            'Asia/Hong_Kong' => '(GMT+08:00) Beijing, Chongqing, Hong Kong, Urumqi',
            'Asia/Irkutsk' => '(GMT+08:00) Irkutsk, Ulaan Bataar',
            'Australia/Perth' => '(GMT+08:00) Perth',
            'Australia/Eucla' => '(GMT+08:45) Eucla',
            'Asia/Tokyo' => '(GMT+09:00) Osaka, Sapporo, Tokyo',
            'Asia/Seoul' => '(GMT+09:00) Seoul',
            'Asia/Yakutsk' => '(GMT+09:00) Yakutsk',
            'Australia/Adelaide' => '(GMT+09:30) Adelaide',
            'Australia/Darwin' => '(GMT+09:30) Darwin',
            'Australia/Brisbane' => '(GMT+10:00) Brisbane',
            'Australia/Hobart' => '(GMT+10:00) Hobart',
            'Asia/Vladivostok' => '(GMT+10:00) Vladivostok',
            'Australia/Lord_Howe' => '(GMT+10:30) Lord Howe Island',
            'Etc/GMT-11' => '(GMT+11:00) Solomon Is., New Caledonia',
            'Asia/Magadan' => '(GMT+11:00) Magadan',
            'Pacific/Norfolk' => '(GMT+11:30) Norfolk Island',
            'Asia/Anadyr' => '(GMT+12:00) Anadyr, Kamchatka',
            'Pacific/Auckland' => '(GMT+12:00) Auckland, Wellington',
            'Etc/GMT-12' => '(GMT+12:00) Fiji, Kamchatka, Marshall Is.',
            'Pacific/Chatham' => '(GMT+12:45) Chatham Islands',
            'Pacific/Tongatapu' => '(GMT+13:00) Nuku\'alofa',
            'Pacific/Kiritimati' => '(GMT+14:00) Kiritimati',
        );
    }
    
    public static function formatDateTime($pattern, $timestamp)
    {
        $date = new DateTime();
        $tz = $date->getTimezone();
        $timezone = Yii::app()->user->getState('Timezone', Settings::TIMEZONE);
        Yii::app()->setTimeZone($timezone);
        $datetime = Yii::app()->getDateFormatter()->format($pattern, $timestamp);
        Yii::app()->setTimeZone($tz->getName());
        return $datetime;
    }
    
    /**
    * Parse a date time string in current locale format to timestamp
    * value and format it if a format pattern is provided.
    * 
    * The pattern is the same used by PHP date() function.
    * 
    * @param mixed $timeString
    */
    public static function str2time($timeString, $pattern = null){
        $appPattern = Yii::app()->getLocale()->getDateFormat('short');
        $ts = CDateTimeParser::parse($timeString, $appPattern);
        if ($pattern)
            return date($pattern, $ts);
        else
            return $ts;
    }
}
?>
