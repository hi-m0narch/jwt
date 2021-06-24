<?php

//import script autoload agar bisa menggunakan library
require_once('vendor/autoload.php');
//import library
use Firebase\JWT\JWT;
use Dotenv\Dotenv;


//load custom environment variabel
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

//atur content type
header('Content-Type: application/json');

//validasi method request apakah POST atau tidak
if ($_SERVER['REQUEST_METHOD'] !== 'POST'){
    http_response_code(405);
    exit();
}

//ambil json yang dikirim user
$json = file_get_contents('php://input');
//decode json agar mudah mengambil nilainya
$input_user = json_decode($json);

//jika tidak ada email atau password
if (!isset($input_user->email) || !isset($input_user->password)){
    http_response_code(400);
    exit();
}

$user = [
    'email' => '187006044@unsil.ac.id',
    'password' => 'siliwangi'
];

// atur content type
header('Content-Type: application/json');

// Jika email atau password tidak sesuai
if ($input_user->email !== $user['email'] || $input_user->password !== $user['password']) {
    echo json_encode([
        'success' => false,
        'data' => null,
        'message' => 'Email atau password tidak sesuai'
    ]);
    exit();
}

//mengatur waktu kadaluarsa acces token, disini akan kadaluarsa dalam 15 menit
$waktu_kadaluarsa = time() + (15 * 60);

//buat payload token
$payload = [
    'email' => $input_user->email,
    'exp' => $waktu_kadaluarsa
];

//mengenerate token dengan library
$access_token = JWT::encode($payload, $_ENV['ACCESS_TOKEN_SECRET']);
echo json_encode([
    'success' => true,
    'data' => [
    'accesToken' => $access_token,
    'expiry' => date(DATE_ISO8601, $waktu_kadaluarsa)
    ],
    'message' => 'Login berhasil!'
]);

//menambah refresh token
$payload['exp'] = time() + (60 * 60);
$refresh_token = JWT::encode($payload,$_ENV['REFRESH_TOKEN_SECRET']);
//simpan refresh token
setcookie('refreshToken', $refresh_token, $payload['exp'], '', '', false, true);

