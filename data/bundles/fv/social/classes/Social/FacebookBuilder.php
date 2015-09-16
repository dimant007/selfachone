<?php
class Social_FacebookBuilder {
    /**
     * @return Facebook
     */
    static function build(){
        $sdk = new Facebook(
            Array(
            "appId"  => fvSite::config()->get( "social.facebook.applicationId" ),
            "secret" => fvSite::config()->get( "social.facebook.applicationSecret" )
            )
        );

        return $sdk;
    }
}