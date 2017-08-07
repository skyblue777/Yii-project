<?php

class SiteMapController extends BackOfficeController
{
	public function actionIndex()
	{
        set_time_limit(0);
        // create xml tag
        $dom = new DOMDocument("1.0","UTF-8");
        $dom->formatOutput = TRUE;
        // create root urlset
        $root = $dom->createElement('urlset');
        $root->setAttribute('xmlns','http://www.sitemaps.org/schemas/sitemap/0.9');
        $root->setAttribute('xmlns:xsi','http://www.w3.org/2001/XMLSchema-instance');
        $root->setAttribute('xsi:schemaLocation','http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd');
	    $dom->appendChild($root); 
        // go throughout all active ads
        $con = Yii::app()->db;
        $offset = 0;
        $limit = 600;
        $sql = "SELECT id, title, area FROM annonce WHERE public = 1 ORDER BY create_time DESC LIMIT {$offset}, {$limit}";
        $com = $con->createCommand($sql);
        $ads = $com->queryAll(TRUE);
        while(is_array($ads) && count($ads) > 0)
        {
            foreach($ads as $ad)
            {
                $urlParams = array('id'=>$ad['id'],'alias'=>str_replace(array(' ','/','\\'),'-',$ad['title']));
                if ($ad['area'] != '') $urlParams['area'] = $ad['area'];
                $adUrl = baseUrl().Yii::app()->createUrl('/Ads/ad/viewDetails',$urlParams);
                // create a url tag
                $item = $dom->createElement('url');
                $locNode = $dom->createElement('loc');
                $locNode->nodeValue = $adUrl;
                $item->appendChild($locNode);
                // append to rool urlset
                $root->appendChild($item);
            }
            $offset = $offset + $limit;
            $sql = "SELECT id, title, area FROM annonce WHERE public = 1 ORDER BY create_time DESC LIMIT {$offset}, {$limit}";
            $com = $con->createCommand($sql);
            $ads = $com->queryAll(TRUE);
        }
        $dom->save('sitemap.xml');
        echo "XML Sitemap for all active ads is created successfully."; 
    }
}
