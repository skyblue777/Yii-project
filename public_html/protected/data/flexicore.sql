/*
SQLyog Enterprise - MySQL GUI v8.18 
MySQL - 5.1.41 : Database - flexicore
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`flexicore` /*!40100 DEFAULT CHARACTER SET utf8 */;

USE `flexicore`;

/*Table structure for table `article` */

DROP TABLE IF EXISTS `article`;

CREATE TABLE `article` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) DEFAULT NULL,
  `title` varchar(512) NOT NULL,
  `alias` varchar(512) NOT NULL,
  `leading_text` text NOT NULL,
  `content` text,
  `photo` varchar(256) DEFAULT NULL,
  `tags` text,
  `is_featured` tinyint(1) NOT NULL DEFAULT '0',
  `status` int(11) NOT NULL,
  `create_time` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT NULL,
  `author_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_post_author` (`author_id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=latin1;

/*Data for the table `article` */

insert  into `article`(`id`,`category_id`,`title`,`alias`,`leading_text`,`content`,`photo`,`tags`,`is_featured`,`status`,`create_time`,`update_time`,`author_id`) values (4,1,'Adding Article module','adding-article-module','<p>The reason I use “Article” as the module name instead of Blog is I would like to use this module for different features, not limited to blog.</p>\n\n<p>To differentiate articles belong to blog and articles belong to other features, we would use category. We will setup a root category for blog and create a setting parameter call BLOG_ROOT_CATEGORY for it. So, all articles belong to this category or its child categories are considered blog posts.</p>','<p>Read more about CORE category</p>\n\n<p>Read more about CORE settings</p>','','module',0,1,1308117529,1308117529,1),(5,1,'Generating code with Gii','generating-code-with-gii','<p>You can use URL with ?r=gii to access Gii tool or you can go to Admin, Tools &gt; Code Generator (Gii)</p>\n\n<p>Our principle is making code most reusable as possible. Therefore:</p>\n\n<ul><li>We want to create module for every new set of related features</li>\n\n<li>We also keep back-end pages (admin features) in the module. Admin controllers go into module/controllers/admin/</li>\n\n</ul>','<p>Some front-end features are common and can be considered to be part of the module. Otherwise, we can always create sub-folder of the protected/controllers/ for front-end controllers.</p>\n\n<p>CORE’s Gii generate module exactly the same to Yii template. There is no enhancement on module generating. However, note that CORE generate model and CRUD page seriously different.</p>\n\n<h2>Generate Article model</h2>\n\n<p>While using Gii tool, this model path should be Article.models</p>\n\n<p>Note that there are 2 files generated for a model. Your modification should only change the Article.php, not ArticleBase.php so next time if your underlying table change, you can always regenerate the ArticleBase.</p>\n\n<h2>Generate CRUD for Article</h2>\n\n<p>The steps are exactly the same to what you are familiar with in Yii. Specify the model you want to generate CRUD pages for and the controller path. In this case</p>\n\n<ul><li>the model path should be Article.models.Article</li>\n\n<li>the controller ID should be Article/admin/article</li>\n\n</ul><p>Note the list of files to be generated you will see one more file called ArticleAPIService.php in the services folder. This is the first difference you can notice about CORE code for CRUD.</p>\n\n<p><img src=\"http://localhost/products/flexicore/Source/uploads/tutorials/module-folder-structure.png\" alt=\"\" width=\"208\" height=\"505\" /></p>\n\n<p>Read more about CORE target of SOA programming</p>','','crud,codegenerator',0,1,1308117889,1308126306,1),(6,1,'Adding menu in the Backend','adding-menu-in-the-backend','<p>After generating CRUD pages, you can try them using the “try it” link. You can create menu in the Backend to let users access these pages easily.</p>','<p>In the database, edit table <strong>module</strong> to add or update your module. Set value ‘y’ for column <strong>has_back_end</strong>.</p>\n\n<p>Edit the module class to add a getMenus() function that return an array of menu configuration. This array is the same as the <strong>items</strong> array used by <a href=\"http://www.yiiframework.com/doc/api/1.1/CMenu/\">CMenu</a>.</p>\n\n<p>    public function getMenus(){</p>\n\n<p>        return array(</p>\n\n<p>            array(\'label\'=&gt;\'Manage article|View and search articles\',\'url\'=&gt;array(\'/Article/admin/article/admin\')),</p>\n\n<p>            array(\'label\'=&gt;\'Write a new article\',\'url\'=&gt;array(\'/Article/admin/article/create\')),</p>\n\n<p>        );</p>\n\n<p>    }</p>','','module, menu',0,1,1308126457,1308126457,1),(7,1,'Improving the Edit Article page','improving-the-edit-article-page','<p>CORE try to use the form.css which is compatible with Yii generated code and as a standard for CORE projects you should try to use this CSS file.</p>\n\n<p>Note that the form is generated using class=”form” but you may prefer using class=”form wide” on a wide screen.</p>','<h2>Rich Text Editor fields</h2>\n\n<p>The first thing you can improve is to use rich-text-editor (RTE) for fields such as <strong>leading text</strong> and <strong>content</strong>. CORE has a widget using TinyMCE as the RTE. This widget also integrate with the File Explorer tool so support adding images into content easily.</p>\n\n<p>          &lt;?php $this-&gt;widget(\'Core.components.tinymce.ETinyMce\', array(</p>\n\n<p>            \'model\'=&gt;$model,</p>\n\n<p>            \'attribute\'=&gt;\'leading_text\',</p>\n\n<p>            \'editorTemplate\'=&gt;\'simple\',</p>\n\n<p>            \'width\'=&gt;\'500px\',</p>\n\n<p>            \'height\'=&gt;\'200px\',</p>\n\n<p>        )); ?&gt;</p>\n\n<p>Use the similar code for the content field.</p>\n\n<p>Note that at this moment if you try to submit the article, content in RTE will be stripped off all of the tags. This is due to the CORE mechanism of preventing XSS attack.</p>\n\n<h2>Lookup fields</h2>\n\n<p>We should make the Status field more friendly by listing 2 options: Active and Inactive.</p>\n\n<p>CORE has a Lookup list that helps you to define friendly constants in your system. This follows Yii’s standard Lookup implementation so you might already familiar with it. You can manage lookup items by going to Settings &gt; Lookup list.</p>\n\n<p>In this case, we add the Inactive item for the existing type “status”.</p>\n\n<p>echo $form-&gt;dropDownList($model,\'status\', Lookup::items(\'status\'));</p>\n\n<p>If you are not familiar with the Lookup utility, you should check the Lookup class in Core/models.</p>\n\n<p>Read more about Lookup</p>\n\n<h2>Category fields</h2>\n\n<p>You don’t need to create category for each type of data in your project as CORE has a master category system that can be used in combination with Settings to manage categories of different content type.</p>\n\n<p>To manage categories, go to Settings &gt; Categories</p>\n\n<p>CORE’s category system is a hierarchical categories tree with unlimited nested levels. It has an intuitive drag-n-drop tool to manage the tree so you can arrange the categories visually.</p>\n\n<p><img src=\"http://localhost/products/flexicore/Source/uploads/tutorials/category-tree.png\" alt=\"\" width=\"682\" height=\"224\" /></p>\n\n<p>In the backend, as our articles are used for different purpose we will not limit the categories for any specific content type.</p>\n\n<p>While using the tool to add categories, you might notice that the drop-down in Edit Category page display the category tree nicely. We can use this widget in the Edit Article page.</p>\n\n<p>&lt;?php $this-&gt;widget(\'CategoryDropDownList\', array(</p>\n\n<p>            \'model\' =&gt; $model,</p>\n\n<p>            \'attribute\' =&gt; \'category_id\',</p>\n\n<p>));?&gt;</p>\n\n<p>Note that this widget has many useful configurations such as listing only a sub-tree. You should refer to the widget code in Core/components for more details.</p>\n\n<h2>Author field</h2>\n\n<p>To set current logged in user as the article’s author we just need to remove the &lt;div&gt; for author_id field and add an hidden field.</p>\n\n<p>&lt;?php echo $form-&gt;hiddenField($model,\'author_id\', array(\'value\' =&gt; user()-&gt;Id)); ?&gt;</p>\n\n<h2>Other fields</h2>\n\n<p>There are still fields that we need to improve but we will leave them for now:</p>\n\n<ul><li>Create time, Update time: These should be auto-generated and not editable. We will add behavior to solve the auto-generate task</li>\n\n<li>Tags: we will change it to be an auto-complete field and write our first API to list all tags in our article sphere.</li>\n\n<li>Title and Alias: the alias is generated automatically from the title and its intention is for friendly URL (some call it slug). We will use utility and an SEO widget to improve these fields.</li>\n\n<li>Photo: Normally, users upload photo using a File field but in case of the form in CORE backend, we have the other simpler and more intuitive solution.</li>\n\n</ul><p>For each content type, we should have a root category so we will create one for our blog.</p>','','',0,1,1308126596,1308126596,1),(8,3,'Writing the first API','writing-the-first-api','<p>We will change the Tags field in Edit Article into an auto-complete field so users can add tags more precisely and quickly.</p>','<p>Generate model ArticleTag for table article_tag in module Article.</p>\n\n<p>For auto-complete field, we will use CJuiAutoComplete widget in Yii. This widget requires a source option to provide list of available options for a search term. Therefore, we will create a server-side function to receive a search parameter and return a list of tags that match the search string.</p>\n\n<p>You can create an action in a controller, i.e. Article/controllers/admin/ArticleController, called actionSearchTags.</p>\n\n<p>public function actionSearchTags() {</p>\n\n<p>        $tag = new ArticleTag(\'search\');</p>\n\n<p>        $tag-&gt;name = $_GET[‘term’]; </p>\n\n<p>        $matches = $tag-&gt;search()-&gt;Data;</p>\n\n<p>        // convert the matches array into a JSON string and echo the result.</p>\n\n<p>}</p>\n\n<p>However, if you do this, the code you write to search all tags that contain a search term is not very reusable. The only way to reuse it in other action is to this action as a function. In term of OOP, this is resource consuming because actionSearchTags is a method of ArticleController and the ArticleController need lot of resource to load. Also, in term of OOP, ArticleController does not created to server this purpose and the work ‘action’ in the method name doesn’t make sense.</p>\n\n<p>That’s the reason CORE make another layer in the M-V-C to make code more reusable.</p>\n\n<p>    /**</p>\n\n<p>    * List all tags in article sphere that contain the search term</p>\n\n<p>    *</p>\n\n<p>    * @param string $term the search term</p>\n\n<p>    */</p>\n\n<p>    public function searchTags($params = array()) {</p>\n\n<p>        $term = $this-&gt;getParam($params, \'term\', \'\');</p>\n\n<p>        $tag = new ArticleTag(\'search\');</p>\n\n<p>        $tag-&gt;name = $term; </p>\n\n<p>        $this-&gt;result-&gt;processed(\'matches\', $tag-&gt;search()-&gt;Data);</p>\n\n<p>        return $this-&gt;result;</p>\n\n<p>    }</p>\n\n<p>As you can see, the function name makes more sense. Also, as ArticleAPIService is simply an utility class, you can call the function as</p>\n\n<p>$api = new ArticleAPIService();</p>\n\n<p>$api-&gt;searchTags(array(‘term’ =&gt; ‘ab’));</p>\n\n<p>In CORE, there is a safer way to call an API like this:</p>\n\n<p>FSM::run(‘Article.ArticleAPI.searchTags’, array(‘term’ =&gt; ‘ab’));</p>\n\n<p>One mysterious here is the $result object and it will be explained later.</p>\n\n<p>For the Tags field in the _form.php, we would use a CJuiAutoComplete widget like this:</p>\n\n<p>&lt;?php $this-&gt;widget(\'zii.widgets.jui.CJuiAutoComplete\', array(</p>\n\n<p>                \'model\' =&gt; $model,</p>\n\n<p>                \'attribute\'=&gt;\'tags\',</p>\n\n<p>                \'source\'=&gt; \'js:function( request, response ) {</p>\n\n<p>                    $.ajax({</p>\n\n<p>                        url: \"\'.serviceUrl(\'Article.ArticleAPI.searchTags\').\'\",</p>\n\n<p>                        dataType: \"json\",</p>\n\n<p>                        data: {term: request.term},</p>\n\n<p>                        complete: function( obj ) {</p>\n\n<p>                            data = eval(obj.responseText);</p>\n\n<p>                            response( $.map( data.matches, function( item ) {</p>\n\n<p>                                return {</p>\n\n<p>                                    label: item.name,</p>\n\n<p>                                    value: item.name</p>\n\n<p>                                }</p>\n\n<p>                            }));</p>\n\n<p>                        }</p>\n\n<p>                    });</p>\n\n<p>                }\',</p>\n\n<p>        ));?&gt;</p>\n\n<p>This follows JqueryUI guide for configuring its AutoComplete plugin. The first important note is the url of the AJAX request.</p>\n\n<p>…</p>\n\n<p>The returned data of our API is caught in the <strong>complete</strong> event. You can console.log(data) on FireBug to see the JSON string.</p>','','api,auto-complete,cjuiautocomplete',0,1,1308126658,1308137693,1),(9,1,'Create a behavior for default value of create_time and update_time','create-a-behavior-for-default-value-of-create-time-and-update-time','<p>We don’t want user to type the current create (if create new) and update time when edit an article. Also as a good practice, most of table to store user submitted data should have create_time and update_time (also status and author_id in our view point) we would consider a reusable solution.</p>','<p>CORE has a behavior to help you quickly update these two fields, all you need is to attach the behavior into your model class:</p>\n\n<p>    public function behaviors()</p>\n\n<p>    {</p>\n\n<p>        return array(</p>\n\n<p>            \'timestamp\'=&gt;array(</p>\n\n<p>                \'class\'=&gt;\'Core.extensions.db.ar.TimestampBehavior\',</p>\n\n<p>            ),</p>\n\n<p>        );</p>\n\n<p>    }</p>\n\n<p>You can see the class path from code above. If you are not familiar with behavior in Yii, it’s very worthy to master this concept. Use the path information to see the sample code.</p>\n\n<h3>Just in case you want to know about Yii behavior in a few words</h3>\n\n<p>Behavior is a more convenience way to add additional methods to a class without sub-classing it. It also allow to you provide default behaviors for events.</p>\n\n<p>By using behavior instead of the classic sub-classing technique you make your class more reusable. As you can see in this scenario, if we need a sub-class such as TimestampActiveRecord extens CActiveRecord then our Article must extends TimestampActiveRecord. Therefore you cannot extend Article from any other ActiveRecord class which may provide you many more useful methods than just updating the timestamp fields.</p>','','behavior',0,1,1308126714,1308126714,1),(10,1,'Saving HTML content','saving-html-content','<p>As you have already change the Leading Text and the Content of your article to use RTE you will notice that HTML code are all stripped off.</p>\n\n<p>CORE implement the input filter mechanism to automatically prevent you from XSS attach. By default, each input runs through ‘newline’, ‘notag’ and ‘xss’ filters. The newline standardize the newline character, notag strips off all tags in your input with PHP’s striptags() function and xss use HtmlPurifier to remove malicious tags but keep safe tags in the input.</p>','<p>To enable HTML content, in the save() API you should exclude the <strong>notag</strong> filter</p>\n\n<p>        $model = $this-&gt;getModel($params[\'Article\'],\'Article\', array(</p>\n\n<p>            \'leading_text\' =&gt; \'notag\',</p>\n\n<p>            \'content\' =&gt; \'notag\'</p>\n\n<p>        ));</p>\n\n<p>The getModel() method of ServiceBase class create a new object of a model class whose name specified in the second parameter. It then assign values for the model attributes using data in the first parameters which should be an associative array.</p>\n\n<p>The third parameter of getModel() allow you to specify filter you do not want to run on each model attribute.</p>\n\n<p>Beside getModel() the ServiceBase class also provide getParam() to extract a single parameter from the parameter array. This function also filter parameter’s value before returning it.</p>','','post,get,input filter,xss',0,1,1308126809,1308126809,1),(11,1,'Using get() and post() instead of $_GET and $_POST','using-get-and-post-instead-of-get-and-post','<p>As a good practice, we found it is faster and safer to extract the submitted data from $_GET or $_POST if</p>\n\n<ul><li>We do not receive error (in case of E_ALL) if parameter is not submitted. Also, we can use a default value in this case</li>\n\n<li>Convert the value to the data type we want, i.e. we need a number but if a string submitted we will get 0 as the vlaue.</li>\n\n<li>Data is filtered to avoid security issue</li>\n\n</ul>','<p>CORE base controller provide you this ability via get() and post()  method. In most case, if you are not sure about $_GET and $_POST, use  the provided function to reduce your risk, save your time and make the  code look better.</p>\n\n<p>An example of use is to retrieve the id parameter from the query string:</p>\n\n<p>$id = $this-&gt;get(‘id’,0);</p>','','post,get,input filter',0,1,1308126867,1308151776,1);

/*Table structure for table `article_comment` */

DROP TABLE IF EXISTS `article_comment`;

CREATE TABLE `article_comment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `article_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `name` varchar(128) NOT NULL,
  `email` varchar(128) NOT NULL,
  `url` varchar(256) DEFAULT NULL,
  `comment` text NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '2',
  `create_time` datetime DEFAULT NULL,
  `update_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Data for the table `article_comment` */

/*Table structure for table `article_tag` */

DROP TABLE IF EXISTS `article_tag`;

CREATE TABLE `article_tag` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL,
  `frequency` int(11) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

/*Data for the table `article_tag` */

insert  into `article_tag`(`id`,`name`,`frequency`) values (1,'yii',1),(2,'blog',1),(3,'test',1),(4,'table',1),(5,'television',1);

/*Table structure for table `authassignment` */

DROP TABLE IF EXISTS `authassignment`;

CREATE TABLE `authassignment` (
  `itemname` varchar(64) NOT NULL,
  `userid` int(11) NOT NULL,
  `bizrule` text,
  `data` text,
  PRIMARY KEY (`itemname`,`userid`),
  CONSTRAINT `FK_authassignment` FOREIGN KEY (`itemname`) REFERENCES `authitem` (`name`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

/*Data for the table `authassignment` */

insert  into `authassignment`(`itemname`,`userid`,`bizrule`,`data`) values ('administrators',1,NULL,NULL);

/*Table structure for table `authitem` */

DROP TABLE IF EXISTS `authitem`;

CREATE TABLE `authitem` (
  `name` varchar(64) NOT NULL,
  `type` int(11) NOT NULL,
  `description` text,
  `bizrule` text,
  `data` text,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

/*Data for the table `authitem` */

insert  into `authitem`(`name`,`type`,`description`,`bizrule`,`data`) values ('administrators',2,'Super administrative role',NULL,NULL);

/*Table structure for table `authitemchild` */

DROP TABLE IF EXISTS `authitemchild`;

CREATE TABLE `authitemchild` (
  `parent` varchar(64) NOT NULL,
  `child` varchar(64) NOT NULL,
  PRIMARY KEY (`parent`,`child`),
  KEY `FK_authitemchild_1` (`child`),
  CONSTRAINT `FK_authitemchild` FOREIGN KEY (`parent`) REFERENCES `authitem` (`name`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_authitemchild_1` FOREIGN KEY (`child`) REFERENCES `authitem` (`name`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

/*Data for the table `authitemchild` */

/*Table structure for table `cache` */

DROP TABLE IF EXISTS `cache`;

CREATE TABLE `cache` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `last_update` datetime DEFAULT NULL,
  `duration` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

/*Data for the table `cache` */

insert  into `cache`(`id`,`name`,`description`,`last_update`,`duration`) values (2,'CORE_CATEGORIES','','2011-06-18 01:50:08',NULL);

/*Table structure for table `category` */

DROP TABLE IF EXISTS `category`;

CREATE TABLE `category` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `alias` varchar(255) NOT NULL,
  `description` text,
  `image` varchar(255) DEFAULT NULL,
  `parent_id` bigint(20) DEFAULT '0',
  `is_active` tinyint(1) DEFAULT '1',
  `ordering` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `alias_unique` (`alias`,`parent_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

/*Data for the table `category` */

insert  into `category`(`id`,`title`,`alias`,`description`,`image`,`parent_id`,`is_active`,`ordering`) values (1,'Blog','blog','','',0,1,1),(2,'News','news','','',0,1,2),(3,'FlexiCORE','flexicore','','',1,1,1),(4,'PHP Programming','php-programming','','',1,1,2),(6,'Yii','yii','','',0,1,0);

/*Table structure for table `extension` */

DROP TABLE IF EXISTS `extension`;

CREATE TABLE `extension` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `event` varchar(255) NOT NULL,
  `class` varchar(64) NOT NULL,
  `method` varchar(64) NOT NULL,
  `config` text,
  `enabled` smallint(6) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Data for the table `extension` */

/*Table structure for table `lookup` */

DROP TABLE IF EXISTS `lookup`;

CREATE TABLE `lookup` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL,
  `code` bigint(20) NOT NULL,
  `type` varchar(128) NOT NULL,
  `position` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

/*Data for the table `lookup` */

insert  into `lookup`(`id`,`name`,`code`,`type`,`position`) values (1,'Active',1,'status',1),(2,'Inactive',2,'status',2);

/*Table structure for table `module` */

DROP TABLE IF EXISTS `module`;

CREATE TABLE `module` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `friendly_name` varchar(255) DEFAULT NULL,
  `description` text,
  `version` varchar(64) DEFAULT NULL,
  `has_back_end` char(1) NOT NULL DEFAULT 'y',
  `ordering` int(11) DEFAULT NULL,
  `icon` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

/*Data for the table `module` */

insert  into `module`(`id`,`name`,`friendly_name`,`description`,`version`,`has_back_end`,`ordering`,`icon`) values (1,'Article','Articles','Manage article content type in your site. Articles can be assigned into different categories and can be tagged.\r\nYou can use articles for many purposes, including blog and news.','1.0','y',0,NULL);

/*Table structure for table `setting` */

DROP TABLE IF EXISTS `setting`;

CREATE TABLE `setting` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `label` varchar(64) DEFAULT NULL,
  `value` text NOT NULL,
  `description` text,
  `setting_group` varchar(128) DEFAULT NULL,
  `ordering` int(11) DEFAULT NULL,
  `visible` smallint(6) DEFAULT NULL,
  `module` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=41 DEFAULT CHARSET=utf8;

/*Data for the table `setting` */

insert  into `setting`(`id`,`name`,`label`,`value`,`description`,`setting_group`,`ordering`,`visible`,`module`) values (1,'ADMIN_EMAIL','Administrator\'s email','hung5s@gmail.com','Administrator email','1. General settings',5,1,''),(2,'ARTICLE_LEADING_WORDS','Number of words for listing articles','120','','1. General settings',0,1,'News'),(3,'BO_PAGE_SIZE','Entries per page in Admin panel','10','Number of entries per page in Back Office','2. Appearance',1,1,''),(4,'BO_THEME','Back Office theme','Admin','Back Office theme','',0,0,''),(5,'CATEGORIES_POPUP','Number of sub-categories listed','5','Number of sub-categories listed in popup when mouse hover a category in the Browse By Category box','2. Appearance',2,1,''),(6,'CONTACT_ROOT_CATEGORY_ID','Contact root category Id','4','Contact root category Id','1. General settings',0,1,'Support'),(7,'DEFAULT_BO_LAYOUT','Default BO layout','default','Default Back Office layout','',0,0,''),(8,'DEFAULT_LAYOUT','Default layout','main','Default layout','',0,0,''),(9,'DEFAULT_META_DESCRIPTION','Default meta description','',NULL,'1. General settings',0,1,''),(10,'DEFAULT_META_KEYWORDS','Default meta keywords','',NULL,'1. General settings',0,1,''),(11,'DEFAULT_PAGE_ID','Default page','1','Default frontend page ID','',0,0,''),(12,'EMAIL_TEMPLATE_FOLDER','Email Template Folder','uploadedfiles/EmailTemplates','','1. General settings',1,1,'Messaging'),(13,'GOOGLE_ANALYTICS','Google Analytics','abc','Google Analytics','1. General settings',0,1,'Statistics'),(14,'LOGO','Logo','uploadedfiles/logo.gif',NULL,'1. General settings',0,1,''),(15,'LOGO_HEIGHT','Height of logo','60','Height of logo','1. General settings',2,1,''),(16,'LOGO_WIDTH','Width of logo','215','Width of logo','1. General settings',1,1,''),(17,'MAIL_METHOD','Mail sending method','smtp','Method to send mails','3. Email',1,1,''),(18,'MAIL_SENDER_NAME','Email sender name','Contact FlexicaCmsDemo','Email sender name','3. Email',7,1,''),(19,'MAIL_SERDER_ADDRESS','Email sender address','contact@flexicacmsdemo.com','Email sender address','3. Email',8,1,''),(20,'MAIL_SIGNATURE','Email signature','Best regards<br/> FlexicaCmsDemo','Email signature','3. Email',9,1,''),(21,'PAGER_HEADER','Leading text for pager','','Leading text for page','2. Appearance',6,1,''),(22,'PAGER_NEXT_PAGE_LABEL','Next page','>','Next page button\'s text','2. Appearance',5,1,''),(23,'PAGER_PREV_PAGE_LABEL','Previous page','<','Previous page button\'s text','2. Appearance',4,1,''),(24,'PAGE_SIZE','Entries per page','5','Number of entry per page','2. Appearance',0,1,''),(25,'QUESTION_ROOT_CATEGORY_ID','Question Root Category Id','5','Question Root Category Id','1. General settings',0,1,'Support'),(26,'QUOTE_FILES_PATH','Quote file path','../uploadedfiles/quote_files/',NULL,'1. General settings',0,1,''),(27,'ROOT_CATEGORY_ID','Root category','2','Module root category','1. General settings',0,1,'News'),(28,'ROOT_CATEGORY_ID','Root category','1','Module root category','1. General settings',0,1,'User'),(29,'SITE_COPYRIGHT','Copyright','Copyright 2010 FlexicaCms. All rights reserved.','Copyright text on footer','1. General settings',3,1,''),(30,'SITE_NAME','Site name','FlexicaCms Demo','Site name, displayed on browser\'s title and used for SEO','1. General settings',4,1,''),(31,'SITE_SECRET_KEY','Site secret key','','Site secret key','',0,0,''),(32,'SMTP_HOST','SMTP host','smtp.gmail.com','SMTP host name','3. Email',2,1,''),(33,'SMTP_PASSWORD','SMTP password','','SMTP password','3. Email',5,1,''),(34,'SMTP_PORT','SMTP port','465','SMTP port','3. Email',3,1,''),(35,'SMTP_SECURE','SMTP sercure connection','tls','SMTP secure connection','3. Email',6,1,''),(36,'SMTP_USERNAME','SMTP username','','SMTP username','3. Email',4,1,''),(37,'THEME','Theme','NewBlog','Frontend theme','',0,0,''),(38,'UPLOAD_FOLDER','User upload folder','uploads','User uploaded folder (you must grant write permission on this folder)','1. General settings',6,1,''),(39,'URL_EXT','URL extension','.html','Url extension','',0,0,''),(40,'VERSION','FlexicaCmsDemo version','1.0',NULL,'',0,0,'');

/*Table structure for table `user` */

DROP TABLE IF EXISTS `user`;

CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(64) NOT NULL,
  `password` varchar(32) NOT NULL,
  `email` varchar(64) DEFAULT NULL,
  `created_date` date DEFAULT NULL,
  `last_login` date DEFAULT NULL,
  `validation_code` varchar(64) DEFAULT NULL,
  `validation_type` smallint(6) DEFAULT NULL,
  `validation_expired` smallint(6) DEFAULT NULL,
  `status` smallint(6) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

/*Data for the table `user` */

insert  into `user`(`id`,`username`,`password`,`email`,`created_date`,`last_login`,`validation_code`,`validation_type`,`validation_expired`,`status`) values (1,'admin','827ccb0eea8a706c4c34a16891f84e7b','admin@flexicacms.com','0000-00-00','2011-07-13','12345',1,1,1);

/*Table structure for table `yii_cache` */

DROP TABLE IF EXISTS `yii_cache`;

CREATE TABLE `yii_cache` (
  `id` char(128) NOT NULL,
  `expire` int(11) DEFAULT NULL,
  `value` longblob,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Data for the table `yii_cache` */

insert  into `yii_cache`(`id`,`expire`,`value`) values ('3057a2accf45a51616c04495c92b0174',0,'a:2:{i:0;s:632:\"a:5:{i:6;a:5:{s:5:\"title\";s:3:\"Yii\";s:5:\"alias\";s:3:\"yii\";s:5:\"level\";i:0;s:6:\"parent\";s:1:\"0\";s:8:\"children\";a:0:{}}i:1;a:5:{s:5:\"title\";s:4:\"Blog\";s:5:\"alias\";s:4:\"blog\";s:5:\"level\";i:0;s:6:\"parent\";s:1:\"0\";s:8:\"children\";a:2:{i:3;s:1:\"3\";i:4;s:1:\"4\";}}i:3;a:5:{s:5:\"title\";s:9:\"FlexiCORE\";s:5:\"alias\";s:9:\"flexicore\";s:5:\"level\";i:1;s:6:\"parent\";s:1:\"1\";s:8:\"children\";a:0:{}}i:4;a:5:{s:5:\"title\";s:15:\"PHP Programming\";s:5:\"alias\";s:15:\"php-programming\";s:5:\"level\";i:1;s:6:\"parent\";s:1:\"1\";s:8:\"children\";a:0:{}}i:2;a:5:{s:5:\"title\";s:4:\"News\";s:5:\"alias\";s:4:\"news\";s:5:\"level\";i:0;s:6:\"parent\";s:1:\"0\";s:8:\"children\";a:0:{}}}\";i:1;O:18:\"CDbCacheDependency\":7:{s:12:\"connectionID\";s:2:\"db\";s:3:\"sql\";s:58:\"SELECT last_update FROM cache WHERE name=\'CORE_CATEGORIES\'\";s:6:\"params\";N;s:23:\"\0CDbCacheDependency\0_db\";N;s:23:\"\0CCacheDependency\0_data\";a:1:{s:11:\"last_update\";s:19:\"2011-06-18 01:50:08\";}s:14:\"\0CComponent\0_e\";N;s:14:\"\0CComponent\0_m\";N;}}');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
