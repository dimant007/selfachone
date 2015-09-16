<?php

class Layout_Default extends fvLayout
{
    function __construct()
    {
        $this->view()->lang = 'ru';
        //$this->view()->lang = Language::getManager()->getCurrentLanguage()->code;

        $this->addCSS([
            "/theme/bootstrap-3.3.5/css/bootstrap.min.css",
            "/theme/bootstrap-3.3.5/css/carousel.css"
        ]);

        $this->addJS([
            "/theme/scripts/jquery.min.js",
            "https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js",
            "/theme/bootstrap-3.3.5/js/bootstrap.min.js",
            "http://getbootstrap.com/assets/js/ie10-viewport-bug-workaround.js"
        ]);
    }

}