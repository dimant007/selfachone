<?php
/**
 * Created by cah4a.
 * Time: 18:38
 * Date: 08.10.13
 *
 * @property Field_Int $retry
 * @property Field_Int $status
 * @property Field_String $lastError
 * @property Field_Array $data
 */
class DelayedJob extends fvRoot {

    const STATUS_PROCESS = 1;
    const STATUS_SUCCESS = 2;
    const STATUS_FAILED = 3;
    protected $maxRetry = 5;

    static function getEntity(){
        return __CLASS__;
    }

    protected function process(){
        throw new Exception( "Not implemented" );
    }

    final public function run(){
        if( $this->retry->get() >= $this->maxRetry ){
            $this->status = self::STATUS_FAILED;
            $this->save();
            return;
        }

        $this->retry = $this->retry->get() + 1;
        $this->save();

        try{
            $this->process();
            $this->status = self::STATUS_SUCCESS;
        } catch ( Error_DelayedJob $e ){
            $this->status = self::STATUS_FAILED;
            $this->lastError = $e->getMessage();
        } catch ( Exception $e ){
            $this->lastError = $e->getMessage();

            if( $this->retry->get() >= $this->maxRetry ){
                $this->status = self::STATUS_FAILED;
            }
        }

        $this->save();
    }

    static function queue( array $data ){
        return static::make( array(
            "data" => $data
        ) )->save();
    }


}