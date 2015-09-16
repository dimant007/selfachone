<?php
/**
 * Created by JetBrains PhpStorm.
 * User: iceman
 * Date: 13.09.13
 * Time: 17:14
 * To change this template use File | Settings | File Templates.
 */

class Component_Invite extends fvComponent{
    public function getComponentName(){
        return "component";
    }

    public function getUsers(){
        $sdk = Social_VkontakteBuilder::build();

        $user = fvSite::session()->getUser();

        if( !( $user instanceof User ) )
            throw new LogicException("Not logged in");

        ini_set("display_errors", 1);

        $users = $sdk->api(
                     "friends.get",
                         array(
                             "user_id" => $user->netId->get(),
                             "order" => "name",
                             "fields" => "photo_50"
                         )
        );

        return $users["response"];
    }
}