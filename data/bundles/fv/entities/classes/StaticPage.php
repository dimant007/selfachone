<?php

/**
 * @method static StaticPageManager getManager()
 */
class StaticPage extends fvRoot
{
    const FORM_NONE = 'none';
    const FORM_PRACTICE = 'practice';
    const FORM_PROBATION = 'probation';
    const FORM_CAREER = 'career';

    static function getEntity(){ return __CLASS__; }

    function validateTech_url( $value )
    {
        $valid = (preg_match( "/^[a-z\_0-9]{1,255}$/", $value ));
        $this->setValidationResult( "tech_url", $valid, "Обязательно для ввода формата: a-z\_" );
        return $valid;
    }

    public function getFormList()
    {
        return array(
            self::FORM_NONE => "нет",
            self::FORM_PROBATION => "Ставжировка",
            self::FORM_PRACTICE => "Практика",
            self::FORM_CAREER => "Карьера",
        );
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getUrl()
    {
        return fvSite::config()->langRoot() . "page/{$this->tech_url}/";
    }

    public function getFormUrl()
    {
        switch( $this->form->get() ){
            case self::FORM_PROBATION:
                return "/form/probation/";
            case self::FORM_PRACTICE:
                return "/form/practice/";
            case self::FORM_CAREER:
                return "/form/career/";
            default:
                return null;
        }
    }
}
