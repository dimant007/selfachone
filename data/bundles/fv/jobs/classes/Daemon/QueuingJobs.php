<?php
/**
 * Created by cah4a.
 * Time: 16:14
 * Date: 23.09.13
 */

class Daemon_QueuingJobs extends fvDaemon {

    protected $tickSleepTime = .1;
    protected $lifetime = 0; // turn of after start. Use cron bitches.

    protected function tick(){
        /** @var DelayedJob[] $jobs */
        $jobs = DelayedJob::select()
               ->where(array("status" => DelayedJob::STATUS_PROCESS))
               ->orderBy("mtime ASC")
               ->limit(30)
               ->fetchAll();

        foreach( $jobs as $job ){
            $job->run();
        }
    }


}