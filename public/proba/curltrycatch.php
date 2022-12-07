<?php


try {
    $ch = curl_init("https://jsonplaceholder.typicode.com/albums/1");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    $response = curl_exec($ch);
    if ($response === false) {
        throw new Exception(curl_error($ch), curl_errno($ch));
    }

    // Check HTTP return code, too; might be something else than 200
    $httpReturnCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    echo $response;

    curl_close($ch);

} catch (Exception $e) {

    trigger_error(sprintf(
        'Curl failed with error #%d: %s',
        $e->getCode(), $e->getMessage()),
        E_USER_ERROR);

} finally {
    // Close curl handle unless it failed to initialize
    if (is_resource($ch)) {
        curl_close($ch);
    }
}
