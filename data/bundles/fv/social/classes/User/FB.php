<?php
class User_FB extends User_Social {
    function import(){
        $facebookSDK = Social_FacebookBuilder::build();
        $userData = $facebookSDK->api( "/" . $this->netId,
                                       "GET",
                                       Array( "fields" => "picture.width(400).type(square),first_name,middle_name,last_name,email" ) );

        $this->name = $userData["first_name"];
        $this->surname = $userData["last_name"];
        $this->image = $userData["picture"]["data"]["url"];
        $this->email = $userData["email"];

        return $this;
    }
}