<?php
return array(
    'imports' => array(
        'Core.extensions.base.*',
        'Core.extensions.web.*',
        
        //Models, services and controllers
        'Core.models.*',
        'Core.controllers.FrontController',
        'Core.controllers.BackOfficeController',
        'Core.services.*',
        
        //Authentication and Authorization
        'Core.extensions.web.auth.*',
        

        //Helpers
        'Core.extensions.web.helpers.FInput',
        'Core.extensions.web.helpers.FHtml',
        
        //Widget
        'Core.components.*',
        'Core.extensions.web.widgets.*',
        'Core.extensions.zii.widgets.grid.*',
        //Cache
        'application.runtime.cache.*',
    ),
    /**
    * auto load item will be included by Yii::import(...,TRUE)
    * this section should have a very limited number of autoload items
    */
    'autoload' => array(
        'Core.extensions.web.helpers.shortcuts',
    )
);
?>
