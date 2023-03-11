<?php

namespace App\Classes\Api;

class apiUtilityClass
{
    public function fileLoader($url)
    {
        $xmlDataString = file_get_contents($url);
        $xmlDataString = preg_replace('/(<\?xml[^?]+?)utf-16/i', '$1utf-8', $xmlDataString);
        $xmlObject = simplexml_load_string($xmlDataString);
        $json = json_encode($xmlObject);
        return json_decode($json, true);
    }


    public function fileUnlink($file)
    {
        if (!unlink($file)) {
            echo ("$file cannot be deleted due to an error");
        }
        else {
            echo ("$file has been deleted");
        }
    }

    public function httpPost($url, $data){
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query(array('post' => $data)));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);
        echo $response;
    }


    public function unZip($file) {
        $zip = new ZipArchive;
        $res = $zip->open(PATH_INPUT . $file);
        if ($res === TRUE) {
            $zip->extractTo(PATH_INPUT);
            $zip->close();
        } else {
            echo 'Nem sikerült kicsomagolni!';
        }
    }

    public function fileWrite($file, $content) {
        fwrite($file, $content);
    }

}
