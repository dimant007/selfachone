<?php
/**
 * Created by JetBrains PhpStorm.
 * User: iceman
 * Date: 08.08.13
 * Time: 18:02
 * To change this template use File | Settings | File Templates.
 */

class Social_VkontakteBuilder {
    static function build(){
        $sdk = new vkapi(
            fvSite::config()->get("social.vkontakte.applicationId"),
            fvSite::config()->get("social.vkontakte.applicationSecret")
        );

        return $sdk;
    }
}