<?php
/**
 * Created by cah4a.
 * Time: 16:47
 * Date: 11.09.14
 */
class BackendApp extends fvApp {

    protected function init()
    {
        $this->setDefaultLayout( "AdminBundle\\Layout\\NotAuth" );
        fvTemplateFinder::addTemplatesFolder("", "vendor/fv/admin-bundle/views");
    }

    protected function loadBundles()
    {
        new fvBundle( "vendor/fv/admin-bundle", "AdminBundle" );
        new fvBundle( "bundles/orders", "OrdersBundle" );
    }

}