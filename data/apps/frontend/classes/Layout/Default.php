<?php

class Layout_Default extends fvLayout
{
    function __construct()
    {
        $this->view()->lang = 'ru';
        //$this->view()->lang = Language::getManager()->getCurrentLanguage()->code;

        $this->addCSS([
            "/theme/stylesheets/style.css",
            "/theme/stylesheets/style2.css"
        ]);

        $this->addJS([
            "",
            "/theme/scripts/common.js",
        ]);
    }

}