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

 /**
 * Generate random string
 * 
 * @param string $chars possible values: lower,upper,numbers,special,all
 * @param int $length
 */
function randomString($chars, $length = 8)
{
    // Assign strings to variables
    $lc = 'abcdefghijklmnopqrstuvwxyz'; // lowercase
    $uc = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'; // uppercase
    $nr = '1234567890'; // numbers
    $sp = '^@*+-+%()!?'; //special characters
    // Set cases for our characters
    switch ($chars) {
        case 'lower': $chars = $lc; break;
        case 'upper': $chars = $uc; break;
        case 'numbers': $chars = $nr; break;
        case 'special': $chars = $sp; break;
        case 'all': $chars = $lc.$uc.$nr.$sp; break;
    }
    // Length of character list
    $chars_length = strlen($chars) - 1;
    // Start our string
    $string = $chars{rand(0, $chars_length)};
    // Generate random string
    for ($i = 1; $i < $length; $i = strlen($string))
    {
    // Take random character from our list
        $random = $chars{rand(0, $chars_length)};
        // Make sure the same two characters donâ€™t appear next to each other
        if ($random != $string{$i - 1}) $string .= $random;
    }
    //return our generated string
    return $string;
}

/**
* Get the first N words of a string
* 
* @param string $string
* @param int $count Number of words
* @return string
*/
function firstWords($string, $count)
{
    $string = strip_tags($string);
    return implode(' ', array_slice(explode(' ',$string), 0, $count));
}

/**
* convert a name/title to slug
* @param string $string
* @param string $spaceChar Character used to replace space
*/
function toSlug($string, $spaceChar = '-') {
    $string = strtr($string, 'ÃÀÁÄÂÈÉËÊÌÍÏÎÒÓÖÔÙÚÜÛãàáäâèéëêìíïîòóöôùúüûÑñÇç', 
                             'AAAAAEEEEIIIIOOOOUUUUaaaaaeeeeiiiioooouuuunncc');
    $string = strtolower($string); // change everything to lowercase
    $string = preg_replace('/^\s+|\s+$/', '',$string);// trim leading and trailing spaces        
    $string = preg_replace('/[_|\s]+/', '-',$string);// change all spaces and underscores to a hyphen
    $string = preg_replace('/[^a-z0-9-]+/', '',$string);// remove all non-alphanumeric characters except the hyphen
    $string = preg_replace('/[-]+/', '-',$string);// replace multiple instances of the hyphen with a single instance
    $string = preg_replace('/^-+|-+$/', '',$string);// trim leading and trailing hyphens                
    
    $string = str_replace('-', $spaceChar, $string);
    return $string;
}

function getFirstWordsFromString($originalString, $wordsCount)
{
    $words = explode(' ', stripslashes($originalString));
    if(count($words) > $wordsCount) 
    {
        $originalString = implode(' ', array_slice($words, 0, $wordsCount)).'...';
    } 
    return $originalString;
}

function getInterval($timestamp, $granularity = 1)
{
    $seconds = time() - $timestamp;
    $units = array( '1 year|:count years' => 31536000, '1 week|:count weeks' => 604800, '1 day|:count days' => 86400, '1 hour|:count hours' => 3600, '1 min|:count mins' => 60, '1 sec|:count secs' => 1);
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
        if ($output) $output .= ' ago'; else $output = '0 sec ago';
    }
    else
    {
        $output = Yii::app()->getDateFormatter()->format(Yii::app()->params['dateTimeFormat'], $timestamp);
    }
    return $output ? $output : '0 sec';
}

function cutString($str,$limit=10)
{
    $len = strlen($str);
    if ($len > $limit)
        return substr($str,0,$limit).'...';
    return $str;    
}
?>