<?php

class User_Vk extends User_Social {
    function import(){
        $vkSDK = Social_VkontakteBuilder::build();

        $userData = $vkSDK->api( "users.get",
                                 array( "user_ids" => $this->netId->get(),
                                        "fields" => "photo_200") );

        if( $userData["error"] ){
            throw new Exception( $userData["error"]["error_msg"] );
        }

        $userDataInfo = $userData["response"][0];

        $this->name = $userDataInfo["first_name"];
        $this->surname = $userDataInfo["last_name"];
        $this->image = $userDataInfo["photo_200"];

        return $this;
    }
}