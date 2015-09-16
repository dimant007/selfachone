<?php

class MiddleWare_FacebookApplication extends fvMiddleWare
{

    const ACCEPTABLE_ALGORITHM = "HMAC-SHA256";

    public function handle( fvRequest $request, fvResponse $response, fvMiddleWaresChain $chain )
    {
        $signedRequest = $request->signed_request;

        if( $signedRequest ){
            fvSite::session()->signedRequest = $signedRequest;

            $this->verifyRequest( $signedRequest );
            $this->parseData( $signedRequest );
        }

        $chain->next();
    }

    private function verifyRequest( $signedRequest )
    {
        list($signatureEncrypted, $payloadEncrypted) = explode( ".", $signedRequest, 2 );

        $signature = $this->decrypt( $signatureEncrypted );
        $data = json_decode( $this->decrypt( $payloadEncrypted ), true );

        if( strtoupper( $data['algorithm'] ) !== self::ACCEPTABLE_ALGORITHM ){
            throw new Exception("Unsupportable algorithm!");
        }

        $applicationSecret = fvSite::config()->get( "facebook.applicationSecret" );
        $expect = hash_hmac( "sha256", $payloadEncrypted, $applicationSecret, true );

        if( $signature !== $expect ){
            fvResponse::getInstance()->setStatus( 400 );
            throw new Exception("Bad Request");
        }

        return true;
    }

    private function parseData( $signedRequest )
    {
        list($signatureEncrypted, $payloadEncrypted) = explode( ".", $signedRequest, 2 );
        $data = json_decode( $this->decrypt( $payloadEncrypted ), true );

        fvSite::session()->signedData = $data;

        return $data;
    }

    private function decrypt( $string )
    {
        return base64_decode( strtr( $string, '-_', '+/' ) );
    }
}