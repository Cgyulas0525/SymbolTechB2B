<?php
$payload = json_encode([
    "title" => "Updated title"
]);

$headers = [
    "Content-type: application/json; charset=UTF-8",
    "Accept-language: en",
];
$ch = curl_init();

//curl_setopt($ch, CURLOPT_URL, 'https://jsonplaceholder.typicode.com/albums/1');
//curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt_array($ch, [
    CURLOPT_URL => 'https://jsonplaceholder.typicode.com/albums/1',
    CURLOPT_RETURNTRANSFER => TRUE,
    CURLOPT_CUSTOMREQUEST => "PATCH",
    CURLOPT_POSTFIELDS => $payload,
    CURLOPT_HTTPHEADER => $headers
]);


$data = curl_exec($ch);

curl_close($ch);

var_dump($data);
