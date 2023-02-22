<?php

class Utility {

    public function fileLoader($url)
    {
        $xmlDataString = file_get_contents($url);
        $xmlDataString = preg_replace('/(<\?xml[^?]+?)utf-16/i', '$1utf-8', $xmlDataString);
        $xmlObject = simplexml_load_string($xmlDataString);
        $json = json_encode($xmlObject);
        return json_decode($json, true);
    }


    public function envLoader($label) {
        $path = dirname(__DIR__,3);

        $current = file($path . "/.env");
        $values = array_values($current);
        for ($i = 0; $i < count($values); $i++) {
            if ( strpos($values[$i], $label) !== false) {
                return substr(substr($values[$i], strlen($label) + 1, strlen($values[$i]) - 2) , 0, strpos(substr($values[$i], strlen($label) + 1, strlen($values[$i]) - 2), "\n"));
            };
        }
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
        $res = $zip->open(PATH_XML . $file);
        if ($res === TRUE) {
            $zip->extractTo(PATH_XML);
            $zip->close();
//            echo 'woot!';
        } else {
            echo 'Nem sikerült kicsomagolni!';
        }
    }
}

