<?php

class Controller_Fv extends fvController
{

    /**
     * @route /fv/upload
     * @option security on
     */
    function uploadAction()
    {
        $this->useLayout( false );
        try{
            return json_encode( [
                "success" => true,
                "file" => $this->upload()
            ] );
        } catch( Exception $e ){
            return json_encode( [
                "success" => false,
                "error" => $e->getMessage()
            ] );
        }
    }

    function upload( $uploadSubPath = '/upload/temp/' )
    {
        if( ! empty($_FILES) ){
            $tempFile = $_FILES['file']['tmp_name'];
            $targetPath = $_SERVER['DOCUMENT_ROOT'] . $uploadSubPath;
            $r = pathinfo( $_FILES['file']['name'] );
            $trans = new Translit();

            $fileName = $trans->Transliterate( $_FILES['file']['name'] ) . date( "dmYHis" ) . "." . $r['extension'];
            $targetFile = str_replace( '//', '/', $targetPath ) . $fileName;

            if( @move_uploaded_file( $tempFile, $targetFile ) ){
                return $uploadSubPath . $fileName;
            }
        }

        throw new Exception("File not uploaded");
    }

    /**
     * @route /fv/uploadbylink
     * @option security on
     */
    function uploadbylinkAction()
    {
        $this->useLayout( false );
        $link = $this->getRequest()->link;

        $pathInfo = pathinfo( $link );

        $uploadSubPath = '/upload/temp/';
        $targetPath = $_SERVER['DOCUMENT_ROOT'] . $uploadSubPath;

        $fileContent = file_get_contents( $link );

        $trans = new Translit;
        $fileName = $trans->Transliterate( $pathInfo["filename"] ) . date( "dmYHis" ) . "." . $pathInfo['extension'];
        $targetFile = $targetPath . $fileName;

        file_put_contents( $targetFile, $fileContent );

        return json_encode( array(
                "success" => true,
                "path" => $uploadSubPath . $fileName
            ) );
    }

    /**
     * @route /fv/persist
     */
    function persistAction()
    {
        $this->useLayout( false );
        return json_encode( array( "filelink" => $this->upload( "/upload/redactor/" ) ) );
    }
}