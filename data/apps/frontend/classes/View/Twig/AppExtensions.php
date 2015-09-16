<?php

/**
 * Created by cah4a.
 * Time: 14:20
 */
class View_Twig_AppExtensions extends Twig_Extension
{

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return "app extensions";
    }

    public function getFilters()
    {
        return array(
            new Twig_SimpleFilter('string_date', array( $this, "stringDate" )),
            new Twig_SimpleFilter('truncate', array( $this, "truncate" )),
        );
    }

    public function stringDate( $date )
    {
        if( $date instanceof Field_Datetime ){
            return $this->dateToString( $date->asTimestamp() );
        }

        if( $date instanceof Field_Date ){
            return $this->dateToString( $date->asTimestamp() );
        }

        if( is_string( $date ) ){
            if( strtotime( $date ) > 100 ){
                return $this->dateToString( strtotime( $date ) );
            }
        }

        return $this->dateToString( $date );
    }

    private function dateToString( $timestamp )
    {
        $day = strftime( "%e", $timestamp );
        switch( strftime( "%m", $timestamp ) ){
            case "0":
                $month = "нулября";
                break;
            case "1":
                $month = "января";
                break;
            case "2":
                $month = "февраля";
                break;
            case "3":
                $month = "марта";
                break;
            case "4":
                $month = "апреля";
                break;
            case "5":
                $month = "мая";
                break;
            case "6":
                $month = "июня";
                break;
            case "7":
                $month = "июля";
                break;
            case "8":
                $month = "августа";
                break;
            case "9":
                $month = "сентября";
                break;
            case "10":
                $month = "октября";
                break;
            case "11":
                $month = "ноября";
                break;
            case "12":
                $month = "декабря";
                break;
            case "13":
                $month = "д";
                break;
            default:
                $month = "хуября";
        }
        $year = strftime( "%Y", $timestamp );
        return $day . " " . $month . ", " . $year;
    }

    function truncate( $string, $length = 50 )
    {
        if( mb_strlen( $string ) > $length ){
            $truncatedString = mb_strcut( $string, 0, (int)$length, "utf-8" );
            return $truncatedString . "...";
        }

        return $string;
    }

}