<?php

//function ftp_get_connect() {
//    $file_names = array();
//    //$ftp_server = (FTP_CONNECTION_TYPE == "test") ? FTP_CONNECTION_FTP_SERVER_TEST : FTP_CONNECTION_FTP_SERVER_LIVE;
//
//    $ch = curl_init();
//    curl_setopt($ch, CURLOPT_URL, "ftp://harmoniatancklub.hu/web/resources/");
//    //curl_setopt($ch, CURLOPT_PORT, FTP_CONNECTION_PORT);
//    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//    curl_setopt($ch, CURLOPT_USERPWD, 'szekelyattilaharmoniatancklubhu'.":".'iwxoZ#DC7C');
//    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
//    curl_setopt($ch, CURLOPT_DIRLISTONLY, TRUE);
//    $files_list = curl_exec($ch);
//    curl_close($ch);
//
//    // The list of all files names on folder
//    $file_names_array= explode("\n", $files_list);
//    // Filter and exclude array elements not valid
//    foreach ($file_names_array as $file_name)
//    {
//        $file_names[] = $file_name;
//    }
//    return $file_names;
//
//}
//
//function ftp_upload_data_files($file_names)
//{
////    $ftp_server = (FTP_CONNECTION_TYPE == "test") ? FTP_CONNECTION_FTP_SERVER_TEST : FTP_CONNECTION_FTP_SERVER_LIVE;
//    // for each file xml
//    foreach ($file_names as $file => $value) {
//        $ch = curl_init();
//        $fp = fopen($value, 'r');
////        curl_setopt($ch, CURLOPT_URL, "ftp://$ftp_server/" . FTP_DIR.basename($value));
//        curl_setopt($ch, CURLOPT_URL, "ftp://harmoniatancklub.hu/web/resources/".$value;
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
//        curl_setopt($ch, CURLOPT_USERPWD, 'szekelyattilaharmoniatancklubhu'.":".'iwxoZ#DC7C');
//        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
//        curl_setopt($ch, CURLOPT_UPLOAD, 1);
//        curl_setopt($ch, CURLOPT_INFILE, $fp);
//        curl_setopt($ch, CURLOPT_INFILESIZE, filesize($value));
//        $result = curl_exec($ch);
//        $error_no = curl_errno($ch);
//        curl_close($ch);
//        if ($error_no == 0) {
//            log_tracking('', $file, 'File upload success'.basename($value), 'OK', 'UPLOAD XML', '');
//        } else {
//            log_tracking('', $file, 'File upload error.'.basename($value), 'ERROR', 'UPLOAD XML', '');
//        }
//    }
//}
//
//function ftp_delete_data_files($file_names)
//{
//    $ftp_server = (FTP_CONNECTION_TYPE == "test") ? FTP_CONNECTION_FTP_SERVER_TEST : FTP_CONNECTION_FTP_SERVER_LIVE;
//
//    // for each file xml
//    foreach ($file_names as $file => $value) {
//        // skip if false
//        if($file == false) continue;
//        $ch = curl_init();
//        curl_setopt($ch, CURLOPT_URL, "ftp://$ftp_server/" . FTP_DIR);
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
//        curl_setopt($ch, CURLOPT_USERPWD, FTP_CONNECTION_USERNAME . ":" . FTP_CONNECTION_PASS);
//        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
//        curl_setopt($ch, CURLOPT_QUOTE, array('DELE /' . FTP_DIR."/".$file.'.xml'));
//        $result = curl_exec($ch);
//        $error_no = curl_errno($ch);
//        curl_close($ch);
//        if ($error_no == 0) {
//            log_tracking('', $file, 'DELETE success', 'OK', 'DELETE XML', '');
//        } else {
//            log_tracking('', $file, 'DELETE error', 'ERROR', 'DELETE XML', '');
//        }
//    }
//}
//
//function ftp_save_local_data_files($file_names)
//{
//    $ftp_server = (FTP_CONNECTION_TYPE == "test") ? FTP_CONNECTION_FTP_SERVER_TEST : FTP_CONNECTION_FTP_SERVER_LIVE;
//    $xml_saved = array();
//
//    // Check if folder exist
//    if (!is_dir(DIR_NAME)) {
//        mkdir(DIR_NAME);
//    }
//
//    // for each file xml
//    foreach ($file_names as $file){
//        // Path where to save
//        $fp = fopen(DIR_NAME."/$file", 'w');
//
//        $ch = curl_init();
//        curl_setopt($ch, CURLOPT_URL, "ftp://$ftp_server/".FTP_DIR."/$file");
//        curl_setopt($ch, CURLOPT_PORT, FTP_CONNECTION_PORT);
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//        curl_setopt($ch, CURLOPT_USERPWD, FTP_CONNECTION_USERNAME.":".FTP_CONNECTION_PASS);
//        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
//        $xml = curl_exec($ch);
//        curl_close($ch);
//
//        // save xml content on file
//        if(fwrite($fp,$xml)){
//            $xml_saved[] = $file;
//            log_tracking('', $file, 'Save XML on server after imported', 'OK', 'SAVE XML', '');
//        }
//    }
//
//    return $xml_saved;
//}
