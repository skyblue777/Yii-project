<?php $this->pageTitle = Settings::SITE_NAME.' - '.$article->title; ?>
<div id="main">
    <div id="pageBreadCrumb" class="link-top">
        <a class="Home" href="<?php echo baseUrl() ?>">
        <?php echo Language::t(Yii::app()->language,'Frontend.Common.Common','Home')?>
        </a> &gt; 
        <span><?php echo Language::t(Yii::app()->language,'Frontend.GenericContent.Article',$article->title) ?></span>    
    </div>
    
    <div style="padding: 0;" class="document">
      <h1 style="padding-bottom: 10px" class="title"><?php echo Language::t(Yii::app()->language,'Frontend.GenericContent.Article',$article->title) ?></h1>
      <div style="font-size: 12px;">
      <?php
          echo $article->leading_text;
          echo $article->content;
      ?>
      </div>
    </div>
</div>