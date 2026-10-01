<?php
$host = 'aws-0-us-east-1.pooler.supabase.com';
$port = '6543';
$db   = 'postgres';
$user = 'postgres.ktlqdjeublwnboqdrgvx';
$pass = getenv('POSTGRES_PASSWORD') !== false ? getenv('POSTGRES_PASSWORD') : 'root'; 

try {
    $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Koneksi database gagal: " . $e->getMessage());
}