<?php
// Start session at beginning
session_start();

// Set timezone
date_default_timezone_set('Asia/Ho_Chi_Minh');

// Enable error reporting for development
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Site configuration
$config = [
    'site_name' => 'TechShop',
    'site_url' => $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'],
    'admin_email' => 'admin@techshop.com',
    'currency' => '₫',
    'currency_name' => 'VND',
    'default_lang' => 'vi',
    'debug' => true
];

// Database configuration
$db_config = [
    'host' => 'localhost',
    'name' => 'techshop',
    'user' => 'your_username',
    'pass' => 'your_password'
];

// Establish database connection
try {
    $db = new mysqli(
        $db_config['host'],
        $db_config['user'],
        $db_config['pass'],
        $db_config['name']
    );

    if ($db->connect_error) {
        throw new Exception("Lỗi kết nối CSDL: " . $db->connect_error);
    }

    // Set charset
    $db->set_charset("utf8");

} catch (Exception $e) {
    die($e->getMessage());
}

// Helper functions
function format_currency($amount) {
    global $config;
    return number_format($amount, 0, ',', '.') . ' ' . $config['currency'];
}

function h($text) {
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

function generate_random_string($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $random_string = '';
    for ($i = 0; $i < $length; $i++) {
        $random_string .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $random_string;
}

function get_cart_session_id() {
    if (!isset($_SESSION['cart_session_id'])) {
        $_SESSION['cart_session_id'] = generate_random_string(32);
    }
    return $_SESSION['cart_session_id'];
}

function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function is_admin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}
?> 