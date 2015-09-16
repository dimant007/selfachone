<?php
error_reporting(E_ALL);

header('P3P: CP="NOI ADM DEV COM NAV OUR STP"');

define( "FV_PROFILE", isset( $_GET['profiler'] ) );
define( "FV_PRODUCTION", FALSE );

if( FV_PROFILE ){
    $startTime = microtime(true);
}

require_once("../data/bootstrap.php");
fvResponse::getInstance()->setPragma(true);

$dispatcher = new fvDispatcher();
$response = $dispatcher->dispatch();
$response->send();

if( FV_PROFILE && ! fvRequest::getInstance()->isXmlHttpRequest() ){
    Profile::startTime( $startTime );
    Profile::show();
}
