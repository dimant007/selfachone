<?php
/**
 * @property Field_String_Password $password
 * @property Field_String_Image $image
 */
class User extends fvRoot
{

    function signIn()
    {
        fvSite::session()->userId = $this->getId();
        return $this;
    }

    function signOut()
    {
        fvSite::session()->userId = null;
        return $this;
    }

    function getFullName()
    {
        return trim( sprintf( "%s %s", $this->name->get(), $this->surname->get() ) );
    }

    function __toString()
    {
        return $this->getFullName();
    }

    function getImage( $width = 45 )
    {
        if( $this->image->get() ){
            return $this->image->change()->grab( $width, $width )->render();
        }

        return '/images/share.png';
    }

    function toJSON()
    {
        return array(
            "id" => $this->getId(),
            "name" => $this->name->get(),
            "surname" => $this->surname->get(),
            "image" => $this->image->get(),
            "email" => $this->email->get()
        );
    }

}