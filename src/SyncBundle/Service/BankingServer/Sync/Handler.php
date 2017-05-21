<?php
// src/SyncBundle/Service/BankingServer/Sync/Handler.php
namespace SyncBundle\Service\BankingServer\Sync;

use Sinner\Phpseclib\Net\Net_SFTP as SFTP;

class Handler
{
    private $connect;

    public function connect($host, $user, $path)
    {
        $this->connect = new SFTP($host);

        return $this->connect->login($user, $path);
    }

    public function syncronize($dirname, $filename, $content)
    {
        return $this->writeContent($dirname, $filename, $content);
    }

    private function writeContent($dirname, $filename, $content)
    {
        // if( !$this->connect->chdir($dirname) ) {
            $this->createResource($dirname);
        // }

        // getSFTPErrors() - if file exists it won't write.

        # Crystal
        #return ( $this->connect->put($dirname . '/' . $filename, $content) )
        #    ? TRUE
        #    : FALSE
        #;

        # Red
        return ( $this->connect->put($filename, $content) )
            ? TRUE
            : FALSE
        ;
    }

    private function createResource($dirname)
    {
        $this->connect->mkdir($dirname);
    }
}
