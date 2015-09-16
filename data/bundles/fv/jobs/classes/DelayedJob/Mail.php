<?php
/**
 * Created by cah4a.
 * Time: 18:52
 * Date: 08.10.13
 */

class DelayedJob_Mail extends DelayedJob {

    protected function process(){
        $mailer = $this->data['mailer'];
        if( ! $mailer instanceof fvMailer ){
            throw new Error_DelayedJob("mail parameter must be instance of fvMailer");
        }
        $mailer->send();
    }


}