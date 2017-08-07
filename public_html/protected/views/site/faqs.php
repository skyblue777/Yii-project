<?php $this->pageTitle = Settings::SITE_NAME.' - Help'; ?>
<div id="main">
    <div id="pageBreadCrumb" class="link-top">
        <a class="Home" href="<?php echo baseUrl() ?>">
        <?php echo Language::t(Yii::app()->language,'Frontend.Common.Common','Home')?>
        </a> &gt; 
        <span><?php echo Language::t(Yii::app()->language,'Frontend.GenericContent.Faqs','Help')?></span>    
    </div>
    
    <div style="padding: 0;" class="document">
    <h1 style="padding-bottom: 10px" class="title"><?php echo Language::t(Yii::app()->language,'Frontend.GenericContent.Faqs','Help')?></h1>

    <?php
    Yii::import('Article.models.*');
    $criteria = new CDbCriteria();
    $criteria->compare('status',1);
    $criteria->compare('category_id',settings::FAQ_CATEGORY);
    $criteria->compare('lang',Yii::app()->language);
    $articles = Article::model()->findAll($criteria);
    
    echo '<div style="font-size:12px;">';
    foreach ($articles as $article) {
      echo '<p>'.CHtml::link($article->title,url('/site/faqs', array('alias' => $article->alias))).'</p>';
      if ($this->get('alias','') == $article->alias)
      {
        echo $article->leading_text;
        if ($article->content != '')
          echo $article->content;
        echo '<br />';
      }
    }
    echo '</div>';   
    
    /*echo '<ul>';
    foreach($articles as $article)
        echo '<li>',CHtml::link($article->title,'#'.$article->alias),'</li>';
    echo '</ul>';     
    ?>
    
    <hr style="visibility: hidden;display:block;margin:20px 0;" />
    
    <?php
    echo '<ul>';
    foreach($articles as $article) {
        echo '<li><h3>',CHtml::link($article->title,'#',array('name' => $article->alias)),'<h3></li>';
        echo $article->leading_text;
        echo $article->content;
    }
    echo '</ul>';*/     
    ?>
    
    </div>
</div>