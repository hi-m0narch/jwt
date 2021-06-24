<?php

//import script autoload agar bisa menggunakan library
require_once('./vendor/autoload.php');
//import library
use Firebase\JWT\JWT;
use Dotenv\Dotenv;

//custom environment
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

//atur content type
header('Content-Type: application/json');

//validasi method request
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    exit();
}

//memeriksa	keberadaan	header	authorization
$headers = getallheaders();
if (!isset($headers['Authorization'])) {
    http_response_code(401);
    exit();
}

list(, $token) = explode(' ', $headers['Authorization']);

//verifikasi token
try {
    //mendecode token, juga memverifikasinya
    JWT::decode($token, $_ENV['ACCESS_TOKEN_SECRET'],['HS256']);
    //data game yg akan dikirim
    $games = [
        [
            'title' => 'VALORANT',
            'genre' => 'First Person Shooter'
        ],
        [
            'title' => 'Dota 2',
            'genre' => 'Strategy'
        ],
        [
            'title' => 'Monster Hunter',
            'genre' => 'Role Playing Game'
        ] 
        ];
        echo json_encode($games);
} catch (Exception $e){
    //bagian ini akan jalan jika ada error saat JWT diverifikasi
    http_response_code(401);
    exit();
}