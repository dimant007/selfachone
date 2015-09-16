<?php
/**
 * Created by cah4a.
 * Time: 16:47
 * Date: 11.09.14
 */

class FrontendApp extends fvApp {

    protected function init()
    {
        $this->setDefaultLayout("default");

        new fvBundle( "bundles/facebook" );
        new fvBundle( "bundles/vkontakte" );

        View_Twig::twig()->addExtension( new View_Twig_AppExtensions() );
    }

} 