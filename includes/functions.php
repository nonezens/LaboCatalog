<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/db.php';

function isUserLoggedIn(): bool
{
    return isset($_SESSION['guest_logged_in']) || isset($_SESSION['admin_logged_in']);
}

function fetchLatestNews(mysqli $conn, int $limit = 5): array
{
    $query = "SELECT * FROM news_events ORDER BY id DESC LIMIT " . (int)$limit;
    $result = $conn->query($query);
    $items = [];

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $items[] = $row;
        }
    }

    return $items;
}

function fetchLatestExhibits(mysqli $conn, int $limit = 8): array
{
    $query = "SELECT * FROM exhibits ORDER BY id DESC LIMIT " . (int)$limit;
    $result = $conn->query($query);
    $items = [];

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $items[] = $row;
        }
    }

    return $items;
}
