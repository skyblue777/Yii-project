<?php
class AdsenseService extends FServiceBase
{
    public function validatePlacement($params)
    {
        $homepageErrors = array();
        $listingpagesErrors = array();
        $adpageErrors = array();
        
        $adsense_code = trim($this->getParam($params, 'adsense_code', ''));
        if ($adsense_code == '')
        {
            $this->result->processed('homepageErrorMsgs',$homepageErrors);
            $this->result->processed('listingpagesErrorMsgs',$listingpagesErrors);
            $this->result->processed('adpageErrorMsgs',$adpageErrors);
            return $this->result;
        }
        
        // check in homepage
        $top_homepage = $this->getParam($params, 'top_homepage', 0);
        $bottom_homepage = $this->getParam($params, 'bottom_homepage', 0);
        if ($top_homepage == 1)
        {
            if (in_array(MoneySettings::BANNER_HOMEPAGE_PLACEMENT,array(1,3)) && trim(MoneySettings::BANNER_HOMEPAGE_CODE) != '')
            {
                $this->result->fail('PLACEMENT_PLACED','Error');
                $homepageErrors[] = 'The location "Top" is already used by a banner. Please select a different location';
            }
        }
        if ($bottom_homepage == 1)
        {
            if (in_array(MoneySettings::BANNER_HOMEPAGE_PLACEMENT,array(2,3)) && trim(MoneySettings::BANNER_HOMEPAGE_CODE) != '')
            {
                $this->result->fail('PLACEMENT_PLACED','Error');
                $homepageErrors[] = 'The location "Bottom" is already used by a banner. Please select a different location';
            }
        }
        
        // check in listing pages
        $top_listingpages = $this->getParam($params, 'top_listingpages', 0);
        $bottom_listingpages = $this->getParam($params, 'bottom_listingpages', 0);
        if ($top_listingpages == 1)
        {
            if (in_array(MoneySettings::BANNER_LISTINGPAGES_PLACEMENT,array(1,3)) && trim(MoneySettings::BANNER_LISTINGPAGES_CODE) != '')
            {
                $this->result->fail('PLACEMENT_PLACED','Error');
                $listingpagesErrors[] = 'The location "Top" is already used by a banner. Please select a different location';
            }
        }
        if ($bottom_listingpages == 1)
        {
            if (in_array(MoneySettings::BANNER_LISTINGPAGES_PLACEMENT,array(2,3)) && trim(MoneySettings::BANNER_LISTINGPAGES_CODE) != '')
            {
                $this->result->fail('PLACEMENT_PLACED','Error');
                $listingpagesErrors[] = 'The location "Bottom" is already used by a banner. Please select a different location';
            }
        }
        
        // check in ad pages
        $top_adpage = $this->getParam($params, 'top_adpage', 0);
        $bottom_adpage = $this->getParam($params, 'bottom_adpage', 0);
        if ($top_adpage == 1)
        {
            if (in_array(MoneySettings::BANNER_ADPAGE_PLACEMENT,array(1,3)) && trim(MoneySettings::BANNER_ADPAGE_CODE) != '')
            {
                $this->result->fail('PLACEMENT_PLACED','Error');
                $adpageErrors[] = 'The location "Top" is already used by a banner. Please select a different location';
            }
        }
        if ($bottom_adpage == 1)
        {
            if (in_array(MoneySettings::BANNER_ADPAGE_PLACEMENT,array(2,3)) && trim(MoneySettings::BANNER_ADPAGE_CODE) != '')
            {
                $this->result->fail('PLACEMENT_PLACED','Error');
                $adpageErrors[] = 'The location "Bottom" is already used by a banner. Please select a different location';
            }
        }
        
        $this->result->processed('homepageErrorMsgs',$homepageErrors);
        $this->result->processed('listingpagesErrorMsgs',$listingpagesErrors);
        $this->result->processed('adpageErrorMsgs',$adpageErrors);
        
        return $this->result;
    }
}
