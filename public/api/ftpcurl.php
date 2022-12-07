<?php

require_once 'Database.php';

$sql = 'SELECT * FROM users';
$smtp = DB::run($sql);
if ($smtp) {
    $records = $smtp->fetchAll();
    if (count($records) > 0) {
        $fname = 'users.json';
        $file = dirname(__DIR__, 1).'/output/users.json';
        $records = json_encode($records);
        file_put_contents($file, $records);
        $ch = curl_init();
        $fp = fopen($file, 'r');
        curl_setopt($ch, CURLOPT_URL, 'ftp://' .getenv('FTP_USERNAME').':'. getenv('FTP_PASSWORD'). '@' . getenv('FTP_HOST') . '/public_ftp/incoming/'.$fname);
        curl_setopt($ch, CURLOPT_UPLOAD, 1);
        curl_setopt($ch, CURLOPT_INFILE, $fp);
        curl_setopt($ch, CURLOPT_INFILESIZE, filesize($file));
    }
}

curl_exec($ch);
$err_no = curl_errno($ch);
curl_close($ch);
fclose($fp);

if($err_no == 0){
    echo 'FTP Transfer complete.';
}else{
    echo 'FTP Transfer error: '.$err_no;
    echo '. See http://curl.haxx.se/libcurl/c/libcurl-errors.html';
}
