<?php

class Dictionary_Database extends fvDictionary
{

    protected $dictionaryList = Array();

    function __construct()
    {
        $list = $this->getManager()->select()->fetchAll();

        foreach( $list as $item ){
            $this->dictionaryList[$item->keyword->get()] = $item->translation->get();
        }

        unset($list);
    }

    function hasTranslate( $string )
    {
        if( array_key_exists( $string, $this->dictionaryList ) ){
            if( ! empty($this->dictionaryList[$string]) ){
                return true;
            }
        }

        return false;
    }

    protected function getTranslate( $string )
    {
        if( array_key_exists( $string, $this->dictionaryList ) ){
            if( empty($this->dictionaryList[$string]) ){
                return $string;
            }

            return $this->dictionaryList[$string];
        }

        $this->store( $string );
        return $string;
    }

    private function store( $string )
    {
        $dictionary = clone $this->getManager()->getRootObj();
        $dictionary->hydrate( array(
            "keyword" => $string,
        ) );

        if( $dictionary->save() ){
            $this->dictionaryList[$string] = $string;
        }
    }

    public function getAllTranslations()
    {
        return $this->dictionaryList;
    }

    /**
     * @return fvRootManager
     */
    protected function getManager()
    {
        return Dictionary::getManager();
    }


}