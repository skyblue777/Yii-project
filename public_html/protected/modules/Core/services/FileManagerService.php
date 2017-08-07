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
class FileManagerService extends FServiceBase {
    
    public function cmdConnect($params) {
        error_reporting(E_ALL); // Set E_ALL for debuging

//        if (function_exists('date_default_timezone_set')) {
//            date_default_timezone_set('Europe/Moscow');
//        }

        require_once Yii::app()->core->AssetFolder.'/elfinder-1.1/connectors/php/elFinder.class.php';

        $opts = array(
            'root'            => Yii::app()->getBasePath().'/../'.Settings::UPLOAD_FOLDER,                       // path to root directory
            'URL'             => Yii::app()->request->getBaseUrl(true).'/'.Settings::UPLOAD_FOLDER.'/', // root directory URL
            'rootAlias'       => 'Home',       // display this instead of root directory name
            // 'disabled'     => array(),      // list of not allowed commands
            // 'dotFiles'     => false,        // display dot files
            // 'dirSize'      => true,         // count total directories sizes
            // 'fileMode'     => 0666,         // new files mode
            // 'dirMode'      => 0777,         // new folders mode
            // 'mimeDetect'   => 'auto',       // files mimetypes detection method (finfo, mime_content_type, linux (file -ib), bsd (file -Ib), internal (by extensions))
            'uploadAllow'  => array('images/*'),      // mimetypes which allowed to upload
            'uploadDeny'   => array(),      // mimetypes which not allowed to upload
            'uploadOrder'  => 'deny,allow', // order to proccess uploadAllow and uploadAllow options
            // 'imgLib'       => 'auto',       // image manipulation library (imagick, mogrify, gd)
            'tmbDir'       => '.tmb',       // directory name for image thumbnails. Set to "" to avoid thumbnails generation
            // 'tmbCleanProb' => 1,            // how frequiently clean thumbnails dir (0 - never, 100 - every init request)
            // 'tmbAtOnce'    => 5,            // number of thumbnails to generate per request
            // 'tmbSize'      => 48,           // images thumbnails size (px)
            // 'fileURL'      => true,         // display file URL in "get info"
            // 'dateFormat'   => 'j M Y H:i',  // file modification date format
            // 'logger'       => null,         // object logger
            // 'defaults'     => array(        // default permisions
            //     'read'   => true,
            //     'write'  => true,
            //     'rm'     => true
            //     ),
            // 'perms'        => array(),      // individual folders/files permisions    
            // 'debug'        => true,         // send debug to client
            // 'archiveMimes' => array(),      // allowed archive's mimetypes to create. Leave empty for all available types.
            // 'archivers'    => array()       // info about archivers to use. See example below. Leave empty for auto detect
            // 'archivers' => array(
            //     'create' => array(
            //         'application/x-gzip' => array(
            //             'cmd' => 'tar',
            //             'argc' => '-czf',
            //             'ext'  => 'tar.gz'
            //             )
            //         ),
            //     'extract' => array(
            //         'application/x-gzip' => array(
            //             'cmd'  => 'tar',
            //             'argc' => '-xzf',
            //             'ext'  => 'tar.gz'
            //             ),
            //         'application/x-bzip2' => array(
            //             'cmd'  => 'tar',
            //             'argc' => '-xjf',
            //             'ext'  => 'tar.bz'
            //             )
            //         )
            //     )
        );

        $fm = new elFinder($opts); 
        $fm->run();    
    }
}
?>