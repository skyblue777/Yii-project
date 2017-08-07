<?php
class BannerService extends FServiceBase
{
    public function validatePlacement($params)
    {
        $homepageErrors = array();
        $listingpagesErrors = array();
        $adpageErrors = array();
        
        // check in homepage
        $homepage_placement = $this->getParam($params, 'homepage_placement', 1);
        $homepage_code = trim($this->getParam($params, 'homepage_code', ''));
        if ($homepage_code != '')
        {
            if ($homepage_placement == 1)
            {
                if (MoneySettings::ADSENSE_HOMEPAGE_TOP_PLACEMENT == 1 && trim(MoneySettings::ADSENSE_CODE) != '')
                {
                    $this->result->fail('PLACEMENT_PLACED','Error');
                    $homepageErrors[] = 'This location is already used by Adsense. Please select a different location';
                }
            }
            elseif ($homepage_placement == 2)
            {
                if (MoneySettings::ADSENSE_HOMEPAGE_BOTTOM_PLACEMENT == 1 && trim(MoneySettings::ADSENSE_CODE) != '')
                {
                    $this->result->fail('PLACEMENT_PLACED','Error');
                    $homepageErrors[] = 'This location is already used by Adsense. Please select a different location';
                }
            }
            elseif ($homepage_placement == 3)
            {
                if ((MoneySettings::ADSENSE_HOMEPAGE_TOP_PLACEMENT == 1 || MoneySettings::ADSENSE_HOMEPAGE_BOTTOM_PLACEMENT == 1) && trim(MoneySettings::ADSENSE_CODE) != '')
                {
                    $this->result->fail('PLACEMENT_PLACED','Error');
                    $homepageErrors[] = 'This location is already used by Adsense. Please select a different location';
                }
            }
        }
        
        // check in listing pages
        $listingpages_placement = $this->getParam($params, 'listingpages_placement', 1);
        $listingpages_code = trim($this->getParam($params, 'listingpages_code', ''));
        if ($listingpages_code != '')
        {
            if ($listingpages_placement == 1)
            {
                if (MoneySettings::ADSENSE_LISTINGPAGES_TOP_PLACEMENT == 1 && trim(MoneySettings::ADSENSE_CODE) != '')
                {
                    $this->result->fail('PLACEMENT_PLACED','Error');
                    $listingpagesErrors[] = 'This location is already used by Adsense. Please select a different location';
                }
            }
            elseif ($listingpages_placement == 2)
            {
                if (MoneySettings::ADSENSE_LISTINGPAGES_BOTTOM_PLACEMENT == 1 && trim(MoneySettings::ADSENSE_CODE) != '')
                {
                    $this->result->fail('PLACEMENT_PLACED','Error');
                    $listingpagesErrors[] = 'This location is already used by Adsense. Please select a different location';
                }
            }
            elseif ($listingpages_placement == 3)
            {
                if ((MoneySettings::ADSENSE_LISTINGPAGES_TOP_PLACEMENT == 1 || MoneySettings::ADSENSE_LISTINGPAGES_BOTTOM_PLACEMENT == 1) && trim(MoneySettings::ADSENSE_CODE) != '')
                {
                    $this->result->fail('PLACEMENT_PLACED','Error');
                    $listingpagesErrors[] = 'This location is already used by Adsense. Please select a different location';
                }
            }
        }
        
        // check in ad pages
        $adpage_placement = $this->getParam($params, 'adpage_placement', 1);
        $adpage_code = trim($this->getParam($params, 'adpage_code', ''));
        if ($adpage_code != '')
        {
            if ($adpage_placement == 1)
            {
                if (MoneySettings::ADSENSE_ADPAGE_TOP_PLACEMENT == 1 && trim(MoneySettings::ADSENSE_CODE) != '')
                {
                    $this->result->fail('PLACEMENT_PLACED','Error');
                    $adpageErrors[] = 'This location is already used by Adsense. Please select a different location';
                }
            }
            elseif ($adpage_placement == 2)
            {
                if (MoneySettings::ADSENSE_ADPAGE_BOTTOM_PLACEMENT == 1 && trim(MoneySettings::ADSENSE_CODE) != '')
                {
                    $this->result->fail('PLACEMENT_PLACED','Error');
                    $adpageErrors[] = 'This location is already used by Adsense. Please select a different location';
                }
            }
            elseif ($adpage_placement == 3)
            {
                if ((MoneySettings::ADSENSE_ADPAGE_TOP_PLACEMENT == 1 || MoneySettings::ADSENSE_ADPAGE_BOTTOM_PLACEMENT == 1) && trim(MoneySettings::ADSENSE_CODE) != '')
                {
                    $this->result->fail('PLACEMENT_PLACED','Error');
                    $adpageErrors[] = 'This location is already used by Adsense. Please select a different location';
                }
            }
        }
        
        $this->result->processed('homepageErrorMsgs',$homepageErrors);
        $this->result->processed('listingpagesErrorMsgs',$listingpagesErrors);
        $this->result->processed('adpageErrorMsgs',$adpageErrors);
        
        return $this->result;
    }
}
