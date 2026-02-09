<?php
try {
    require __DIR__.'/vendor/autoload.php';
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();
    
    $host = $_ENV['DB_HOST'];
    $db   = $_ENV['DB_DATABASE'];
    $user = $_ENV['DB_USERNAME'];
    $pass = $_ENV['DB_PASSWORD'];
    $port = $_ENV['DB_PORT'];

    echo "Attempting connection to $host:$port ($db)...\n";

    $dsn = "mysql:host=$host;dbname=$db;port=$port;charset=utf8mb4";
    $pdo = new PDO($dsn, $user, $pass);
    echo "Connection successful!";
} catch (\Exception $e) {
    echo "Connection failed: " . $e->getMessage();
}
