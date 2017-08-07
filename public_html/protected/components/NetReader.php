<?php
/**
* @desc Class NetReader, read an URL and save to file or xml document
* Hung Nguyen
* 
*/
class NetReader
{
    protected $url;
    
    public function __construct($url)
    {
        $this->url = $url;
    }
    
    public function readRaw()
    {
        $ch = curl_init($this->url);

        $header[0] = "Accept: text/xml,application/xml,application/xhtml+xml";
        $header[0] .= "text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5";
        $header[] = "Cache-Control: max-age=0";
        $header[] = "Connection: keep-alive";
        $header[] = "Keep-Alive: 300";
        $header[] = "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7";
        $header[] = "Accept-Language: en-us,en;q=0.5";
        $header[] = "Pragma: ";

        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.1) Gecko/20061204 Firefox/2.0.0.1');
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip,deflate');
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);        

        curl_setopt($ch, CURLOPT_HEADER, 0);
        //curl_setopt($ch,CURLOPT_TRANSFERTEXT,0);
        $res = curl_exec($ch);
        curl_close($ch);

        return $res;    
    }
    
    public function readRedirected()
    {
    	$ch = curl_init($this->url);
        $header[0] = "Accept: text/xml,application/xml,application/xhtml+xml";
        $header[0] .= "text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5";
        $header[] = "Cache-Control: max-age=0";
        $header[] = "Connection: keep-alive";
        $header[] = "Keep-Alive: 300";
        $header[] = "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7";
        $header[] = "Accept-Language: en-us,en;q=0.5";
        $header[] = "Pragma: ";

        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.1) Gecko/20061204 Firefox/2.0.0.1');
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip,deflate');
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); 
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);        

        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch,CURLOPT_NOBODY,1);
        //curl_setopt($ch,CURLOPT_TRANSFERTEXT,1);
        $res = curl_exec($ch);
        curl_close($ch);

        preg_match('/location: (.*)/', $res, $matches);
        if (empty($matches))
        {
	        echo 'Error! Cannot get redirect url. <br /> \n';
		} else {
			$this->url = $matches[1];
		}
        return $this->readRaw();
    }
    /**
    * @desc Read the $url, return HTML content
    * @param string $file filepath to save the HTML
    * @return DomDocument
    */
    public function read($file = null, $callback = null)
    {

        $res = $this->readRaw();
        //If we got nothing, try a redirect
        if ($res == ''){
            $try = 1;
            while ($res == '' && $try <= 5){
                $res = $this->readRedirected();        
                $try++;
            }
        }
        if ($res == '') return null;
        
        //Save to file
        if ($file != null) $this->saveToFile($file, $res);
        
        //DOM in PHP5.2.x always treat data as UTF-8 so we need to translate $res to UTF-8 first
        $tmp = $this->htmlEntitiesToChars($res);
        if ($tmp != '') $res = $tmp;
        //var_dump($callback);die;
        //if ($callback){
//                echo 'callback called<br />';
//                call_user_func($callback, & $res);
//            }
//        echo $res,'<br />';
        //Not sure the HTML is XML wellform!!!
        error_reporting(E_ERROR);
        $doc = new DOMDocument('1.0','utf-8');
        $doc->loadHTML($res);
//        echo $doc->saveHTML();die;
        error_reporting(E_ALL);
        return $doc;
    }
        
    public function htmlEntitiesToChars($str)
    {
        //Convert HTML decimal/hexa entities to characters
//        if (mb_detect_encoding($str) == 'UTF-8'){
//            decode decimal HTML entities added by web browser
            $str = preg_replace('/&#\d{2,5};/ue', "\$this->utf8_entity_decode('$0')", $str);
//            decode hex HTML entities added by web browser
            $str = preg_replace('/&#x([a-fA-F0-7]{2,8});/ue', "\$this->utf8_entity_decode('&#'.hexdec('$1').';')", $str );           
//            decode named characters
            $str = html_entity_decode($str, ENT_QUOTES, "utf-8");
//            echo $str.'<br />';
//        }
        return $str;    
    }
    
    //callback function for the regex
    protected function utf8_entity_decode($entity){    
        $convmap = array(0x0, 0x10000, 0, 0xfffff); 
        return mb_decode_numericentity($entity, $convmap, 'UTF-8');
    }

    protected function saveToFile($file, $data)
    {
        $f = fopen($file, 'w+');
        fwrite($f, $data);
        fclose($f);
    }
    
     public function getImage($url, $path,$adsId=0){
        $ch = curl_init ($url);
        $agent = "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.1) Gecko/20061204 Firefox/2.0.0.1";
        curl_setopt($ch, CURLOPT_USERAGENT, $agent);
        $httpheader[] = "ContentType: application/xml; charset=utf-8";
        $httpheader[] = "Cache-Control: max-age=0";
        $httpheader[] = "Connection: keep-alive";
        $httpheader[] = "Keep-Alive: 300";
        $httpheader[] = "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7";
        $httpheader[] = "Pragma: ";
        curl_setopt($ch, CURLOPT_HTTPHEADER, $httpheader);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
        $rawdata=curl_exec($ch);
        curl_close ($ch);
        
        
        //Get Image name';
        $b=trim(strrchr($url,'/'),'/');
        //trim the ?id=...
        $c=explode('?',$b);
        $image=explode('.',$c[0]);
        //rename the image name
        $image[0]=md5($image[0]);
        $filename=implode('.',$image);
        if($adsId>0)
            $filename = strtolower($adsId.'_'.$filename);
        $saveTo = $path.'/'.$filename;
        if (!file_exists($saveTo))
        {
            file_put_contents($saveTo, $rawdata);
            //$filename = Utility::waterMarkImage($saveTo,$path);
        }
        return $filename;
    }
}
?>
