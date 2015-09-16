<?php

class Dictionary_DatabaseLocaled extends Dictionary_Database
{

    protected function getManager()
    {
        return DictionaryLocaled::getManager();
    }


}