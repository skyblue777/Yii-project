<?php echo CHtml::textArea($this->name,$this->value,array('class'=>'txt-banner-code','rows'=>5,'cols'=>50)); ?>

<?php if ($this->name == 'BANNER_HOMEPAGE_CODE') : ?>
    <script type="text/javascript">
    $('input[type=submit]').live('click',function(){
        $('div.errorMessage').remove();
        $('textarea.txt-banner-code').removeClass('error');
        
        var isValidBanner = true;
        
        $('textarea.txt-banner-code').each(function(){
            var html_code = $(this).val();
            if (html_code.indexOf('google_ad_client')!=-1 || html_code.indexOf('googlesyndication')!=-1)
            {
                isValidBanner = false;
                $(this).addClass('error');
                $(this).after('<div class="errorMessage banner-code-error">'.Language::t(Yii::app()->language,'Backend.Money.Setting','Sorry you can only add banners').'</div>');    
            }        
        });
        
        if (isValidBanner)
        {
            var homepage_placement = $('#BANNER_HOMEPAGE_PLACEMENT').val();
            var listingpages_placement = $('#BANNER_LISTINGPAGES_PLACEMENT').val();
            var adpage_placement = $('#BANNER_ADPAGE_PLACEMENT').val();
            $.ajax({
                'type' : 'POST',
                'async' : false,
                'url' : baseUrl + '/index.php?r=Core/service/ajax',
                'data' :
                {
                    'SID' : 'Money.Banner.validatePlacement',
                    'homepage_placement' : homepage_placement,
                    'listingpages_placement' : listingpages_placement,
                    'adpage_placement' : adpage_placement,
                    'homepage_code' : $('#BANNER_HOMEPAGE_CODE').val(),
                    'listingpages_code' : $('#BANNER_LISTINGPAGES_CODE').val(),
                    'adpage_code' : $('#BANNER_ADPAGE_CODE').val()
                },
                'success' : function(json) {
                    var result = eval(json);
                    if (result.errors.ErrorCode)
                    {
                        isValidBanner = false;
                        if (result.homepageErrorMsgs.length > 0)
                        {
                            var msgs = '<div class="errorMessage">';
                            for (var i in result.homepageErrorMsgs)
                            {
                                msgs += result.homepageErrorMsgs[i]+'<br />';       
                            }
                            msgs += '</div>';
                            $('#BANNER_HOMEPAGE_PLACEMENT').after(msgs);   
                        }
                        if (result.listingpagesErrorMsgs.length > 0)
                        {
                            var msgs = '<div class="errorMessage">';
                            for (var i in result.listingpagesErrorMsgs)
                            {
                                msgs += result.listingpagesErrorMsgs[i]+'<br />';       
                            }
                            msgs += '</div>';
                            $('#BANNER_LISTINGPAGES_PLACEMENT').after(msgs);   
                        }
                        if (result.adpageErrorMsgs.length > 0)
                        {
                            var msgs = '<div class="errorMessage">';
                            for (var i in result.adpageErrorMsgs)
                            {
                                msgs += result.adpageErrorMsgs[i]+'<br />';       
                            }
                            msgs += '</div>';
                            $('#BANNER_ADPAGE_PLACEMENT').after(msgs);   
                        }    
                    }
                }
            });
        }
        
        return isValidBanner;
    });
    </script>
<?php endif; ?>