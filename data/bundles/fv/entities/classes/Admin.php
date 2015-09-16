<?php

/**
 * Class Admin
 * @property Field_String_Password password
 */
class Admin extends fvRoot
{

    public function hasAcl( $acl )
    {
        $acl = trim( $acl );

        $role = $this->role->get() ? : "default";
        $aclList = fvSite::config()->get( "roles.{$role}", array() );

        if( is_string( $aclList ) ){
            $aclList = (array)$aclList;
        }

        if( in_array( "*", $aclList ) ){
            return ! in_array( "-" . $acl, $aclList );
        }

        return in_array( $acl, $aclList );
    }

}