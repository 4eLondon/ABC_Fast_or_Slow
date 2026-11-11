<?php

require_once __DIR__ . '/database.php';

function csrf_token(): string
{
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function verify_csrf_token(): bool
{
    $submitted = $_POST['csrf_token'] ?? '';
    return is_string($submitted) && isset($_SESSION['csrf_token'])
        && hash_equals($_SESSION['csrf_token'], $submitted);
}

function current_user(PDO $db): ?array
{
    $userId = $_SESSION['user_id'] ?? null;
    if (!is_int($userId) && !ctype_digit((string) $userId)) {
        return null;
    }

    $statement = $db->prepare("SELECT id, username, email FROM users WHERE id = :id AND status = 'active'");
    $statement->execute(['id' => (int) $userId]);
    $user = $statement->fetch();

    if (!$user) {
        unset($_SESSION['user_id'], $_SESSION['username']);
        return null;
    }

    return $user;
}

function auth_redirect_target(string $target): string
{
    if (preg_match('/^lobby\.php(?:\?[A-Za-z0-9_=&%-]*)?$/', $target)
        || preg_match('/^game\.php\?room=[A-Z]{4}$/', $target)) {
        return $target;
    }

    return '../index.php';
}
