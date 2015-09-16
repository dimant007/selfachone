<?php
/**
 * Created by cah4a.
 * Time: 16:08
 * Date: 09.10.13
 */

abstract class Component_Mail_Delayed extends Component_Mail {

    final public function send(){
        $this->mailer()->setHtmlBody( (string)$this );

        $job = DelayedJob_Mail::queue( array(
            'mailer' => $this->mailer()
        ) );
        unset($job);
    }

}