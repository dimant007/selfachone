<?php

/**
 * Created by cah4a.
 * Time: 16:57
 * Date: 02.07.14
 */
class Field_String_Role extends Field_String_List
{

    function getList()
    {
        $roles = [ ];

        foreach( array_keys( fvSite::config()->get( "roles", array() ) ) as $role ){
            $roles[$role] = $role;
        }

        return $roles;
    }


} 