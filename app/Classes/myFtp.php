<?php

namespace App\Classes;

use Flash;

class myFtp
{
    public $file;
    public $fname;

    function __construct($file, $fname) {
        $this->file = $file;
        $this->fname = $fname;
    }

    public function uploadFile() {
        $ch = curl_init();
        $fp = fopen($this->file, 'r');
        curl_setopt($ch, CURLOPT_URL, 'ftp://' .getenv('FTP_USERNAME').':'. getenv('FTP_PASSWORD'). '@' . getenv('FTP_HOST') . '/public_ftp/incoming/'.$this->fname);
        curl_setopt($ch, CURLOPT_UPLOAD, 1);
        curl_setopt($ch, CURLOPT_INFILE, $fp);
        curl_setopt($ch, CURLOPT_INFILESIZE, filesize($this->file));

        curl_exec($ch);
        $err_no = curl_errno($ch);
        curl_close($ch);
        fclose($fp);

        if ($err_no == 0) {
            Flash::success('FTP Transfer complete..')->important();
        } else {
            Flash::error('FTP Transfer error: ' . $err_no)->important();
        }
    }
}
