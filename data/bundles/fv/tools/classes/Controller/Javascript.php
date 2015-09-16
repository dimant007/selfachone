<?php

class Controller_Javascript extends fvController {

    function __construct(){
        $this->useLayout(false);
    }

    /**
     * @route /tools/add-dictionary-key
     */
    function addDictionaryKeyAction(){
        if( ! $this->getRequest()->key ){
            throw new Error_PageNotFound;
        }

        if( ! fvSite::config()->get("debug") ){
            return '{ "success": false, "message": "not debug mode" }';
        }

        return '{ "success": true, "key": ' . fvSite::dictionary()->translate( $this->getRequest()->key ) . '}';
    }

    /**
     * @route /tools/fvSite
     * @option security off
     */
    function configAction(){
        $configs = Array();
        $sections = fvSite::config()->get("javascript.sections.include", array());
        $exclude = fvSite::config()->get("javascript.sections.exclude", array());

        foreach( $sections as $section ){
            $configs[$section] = fvSite::config()->get( $section );

            foreach( $exclude as $key ){
                $this->exclude( $configs, $key );
            }
        }

        if( fvSite::config()->get("javascript.user", true) ){
            $user = fvSite::session()->getUser();
            if( $user instanceof User ){
                $configs["user"] = $user->toJSON();
            }
        }

        $configs["debug"] = fvSite::config()->get("debug", false);

        $file = file_get_contents(realpath(__DIR__ . "/../../resources/fvSite.js"));
        $file = str_replace( '$CONFIG$', json_encode( $configs ), $file );

        if( fvSite::config()->get("javascript.dictionary", false) ){
            $file = str_replace( '$DICTIONARY$', json_encode( (array)fvSite::dictionary()->getAllTranslations() ), $file );
        } else {
            $file = str_replace( '$DICTIONARY$', "{}", $file );
        }

        header("Content-type: text/javascript");
        return $file;
    }

    private function exclude( & $array, $key ){
        $keys = explode( ".", $key );
        $lastKey = array_pop( $keys );

        foreach( $keys as $key ){
            if( !isset($array[$key]) ){
                return;
            }

            $array = & $array[$key];
        }

        if( isset( $array[$lastKey] ) ){
            unset( $array[$lastKey] );
        }
    }

}