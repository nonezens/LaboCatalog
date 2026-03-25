<?php
define('ROOT_PATH', __DIR__);

$page = $_GET['page'] ?? 'home';

$templateRoutes = [
    'home' => ROOT_PATH . '/templates/public/home.php',
    'about' => ROOT_PATH . '/templates/public/about.php',
    'book' => ROOT_PATH . '/templates/public/book.php',
    'categories' => ROOT_PATH . '/templates/public/categories.php',
    'exhibit_detail' => ROOT_PATH . '/templates/public/exhibit_detail.php',
    'exhibits' => ROOT_PATH . '/templates/public/exhibits.php',
    'news' => ROOT_PATH . '/templates/public/news.php',
    'login' => ROOT_PATH . '/templates/auth/login.php',
    'admin_dashboard' => ROOT_PATH . '/templates/admin/admin_dashboard.php',
    'add_category' => ROOT_PATH . '/templates/admin/add_category.php',
    'add_exhibit' => ROOT_PATH . '/templates/admin/add_exhibit.php',
    'add_news' => ROOT_PATH . '/templates/admin/add_news.php',
    'edit_category' => ROOT_PATH . '/templates/admin/edit_category.php',
    'edit_exhibit' => ROOT_PATH . '/templates/admin/edit_exhibit.php',
    'edit_news' => ROOT_PATH . '/templates/admin/edit_news.php',
    'manage_departments' => ROOT_PATH . '/templates/admin/manage_departments.php',
    'manage_exhibits' => ROOT_PATH . '/templates/admin/manage_exhibits.php',
    'manage_news' => ROOT_PATH . '/templates/admin/manage_news.php',
    'manage_visitors' => ROOT_PATH . '/templates/admin/manage_visitors.php',
];

$handlerRoutes = [
    'ai_brain' => ROOT_PATH . '/includes/handlers/ai_brain.php',
    'delete_category' => ROOT_PATH . '/includes/handlers/delete_category.php',
    'delete_exhibit' => ROOT_PATH . '/includes/handlers/delete_exhibit.php',
    'delete_guest' => ROOT_PATH . '/includes/handlers/delete_guest.php',
    'export_visitors' => ROOT_PATH . '/includes/handlers/export_visitors.php',
    'logout' => ROOT_PATH . '/includes/handlers/logout.php',
    'manage_guest' => ROOT_PATH . '/includes/handlers/manage_guest.php',
];

if (isset($handlerRoutes[$page])) {
    require $handlerRoutes[$page];
    exit;
}

if (!isset($templateRoutes[$page])) {
    http_response_code(404);
    $page = 'home';
}

if ($page === 'home') {
    require_once ROOT_PATH . '/includes/functions.php';
    $is_logged_in = isUserLoggedIn();
    $news_items = fetchLatestNews($conn, 5);
    $exhibits_items = fetchLatestExhibits($conn, 8);
}

require $templateRoutes[$page];