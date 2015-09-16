<?php

error_reporting( ini_get( 'error_reporting' ) & ~E_STRICT & ~E_NOTICE );

chdir( __DIR__ );
define( "SCRIPTS_PATH", __DIR__ . "/scripts" );

$loader = require "vendor/autoload.php";

fvBundle::$loader = $loader;

new fvBundle( __DIR__ . "/bundles/facebook" );
new fvBundle( __DIR__ . "/bundles/vkontakte" );
new fvBundle( __DIR__ . "/bundles/fv/entities" );
new fvBundle( __DIR__ . "/bundles/fv/tools" );

fvSite::init();
