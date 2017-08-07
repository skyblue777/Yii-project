<?php echo CHtml::hiddenField($this->name,$this->value); ?>
<a class="lnk-add-location" href="#"><?php echo Language::t(Yii::app()->language,'Backend.Ads.Setting','Add a new location')?></a>

<table id="location-list" cellpadding="2" cellspacing="5" border="0">
    <tbody>
        <?php foreach($locations as $key => $loc) : ?>
            <tr class="location-row">
                <td>
                    <label><?php echo Language::t(Yii::app()->language,'Backend.Ads.Common','Location')?>:</label>
                    <?php echo CHtml::textField('',$loc['name'],array('class'=>'loc-name')); ?>    
                </td>
                <td>
                    <label><?php echo Language::t(Yii::app()->language,'Backend.Ads.Setting','Latitude')?>:</label>
                    <?php echo CHtml::textField('',$loc['lat'],array('class'=>'loc-lat')); ?>    
                </td>
                <td>
                    <label><?php echo Language::t(Yii::app()->language,'Backend.Ads.Setting','Longitude')?>:</label>
                    <?php echo CHtml::textField('',$loc['lng'],array('class'=>'loc-lng')); ?>    
                </td>
                <td>
                    <a href="#" class="lnk-remove-location"><img src="<?php echo themeUrl().'/images/buttons/ico-delete.gif' ?>" /></a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<div style="float: right; margin-right: 105px;"><a target="_blank" href="http://itouchmap.com/latlong.html"><?php echo Language::t(Yii::app()->language,'Backend.Ads.Setting','Find out Latitude / Longitude')?></a></div>

<script type="text/javascript">
$('a.lnk-add-location').click(function(){
    var col_loc_name = $('<td><label><?php echo Language::t(Yii::app()->language,'Backend.Ads.Common','Location')?>:</label><input class="loc-name" type="text" /></td>');
    var col_loc_lat = $('<td><label><?php echo Language::t(Yii::app()->language,'Backend.Backend.Ads.Setting','Latitude')?>:</label><input class="loc-lat" type="text" /></td>');
    var col_loc_lng = $('<td><label><?php echo Language::t(Yii::app()->language,'Backend.Backend.Ads.Setting','Longitude')?>:</label><input class="loc-lng" type="text" /></td>');
    var col_link_del = $('<td><a href="#" class="lnk-remove-location"><img src="<?php echo themeUrl().'/images/buttons/ico-delete.gif' ?>" /></a></td>');
    var new_row = $('<tr class="location-row"></tr>').append(col_loc_name).append(col_loc_lat).append(col_loc_lng).append(col_link_del);
    $('#location-list tbody').append(new_row);
    return false;    
});

$('#location-list tbody tr.location-row td a.lnk-remove-location').live('click',function(){
    $(this).parent().parent().remove();
    return false;    
});

$('input[type=submit]').live('click',function(){
    var locations = new Array();
    var check = true;
    // collect locations
    $('#location-list input').removeClass('error');
    $('#location-list tbody tr.location-row').each(function(){
        var loc_name_textbox = $(this).find('td input.loc-name');
        var loc_name = $.trim(loc_name_textbox.val());
        // check location name
        if (loc_name=='')
        {
            check = false;
            loc_name_textbox.addClass('error');
            alert('Please input location name!');
            return false;
        }
        if (loc_name.indexOf('|') != -1)
        {
            check = false;
            loc_name_textbox.addClass('error');
            alert('Please do not use the character "|" in location name!');
            return false;    
        }
        // check lat & lng
        var lat = $.trim($(this).find('td input.loc-lat').val());
        var lng = $.trim($(this).find('td input.loc-lng').val());
        // check lat
        if (lat=='' || lat.indexOf(',') != -1)
        {
            check = false;
            $(this).find('td input.loc-lat').addClass('error');
            alert('Please input latitude without the comma!');
            return false;    
        }
        if (lng=='' || lng.indexOf(',') != -1)
        {
            check = false;
            $(this).find('td input.loc-lng').addClass('error');
            alert('Please input longitude without the comma!');
            return false;    
        }
        locations.push(loc_name+' | '+lat+','+lng);        
    });
    
    if (check == false)
    {
        return check;    
    }
    
    $('#AREA_LIST').val(locations.join(' ; '));
    
    return check;
});
</script>