<?php
session_start();
require_once '../includes/app.php';
require_once '../includes/rooms.php';

$db = database_connection();
$user = current_user($db);
$mode = ($_GET['mode'] ?? 'login') === 'register' ? 'register' : 'login';
$next = auth_redirect_target((string) ($_GET['next'] ?? ''));
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mode = ($_POST['mode'] ?? 'login') === 'register' ? 'register' : 'login';
    $next = auth_redirect_target((string) ($_POST['next'] ?? ''));

    if (!verify_csrf_token()) {
        $error = 'Your session expired. Refresh the page and try again.';
    } elseif (($_POST['action'] ?? '') === 'logout') {
        $_SESSION = [];
        session_regenerate_id(true);
        header('Location: ../index.php');
        exit;
    } elseif ($mode === 'register') {
        $username = trim((string) ($_POST['username'] ?? ''));
        $email = trim((string) ($_POST['email'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');
        $confirm = (string) ($_POST['confirm_password'] ?? '');

        if (!preg_match('/^[A-Za-z0-9_]{3,20}$/', $username)) {
            $error = 'Use a username with 3-20 letters, numbers, or underscores.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 100) {
            $error = 'Enter a valid email address.';
        } elseif (strlen($password) < 8) {
            $error = 'Password must be at least 8 characters.';
        } elseif ($password !== $confirm) {
            $error = 'Passwords do not match.';
        } else {
            try {
                $insert = $db->prepare(
                    "INSERT INTO users (username, email, password_hash) VALUES (:username, :email, :password_hash)"
                );
                $insert->execute([
                    'username' => $username,
                    'email' => $email,
                    'password_hash' => password_hash($password, PASSWORD_DEFAULT),
                ]);
                session_regenerate_id(true);
                $_SESSION['user_id'] = (int) $db->lastInsertId();
                $_SESSION['username'] = $username;
                header('Location: ' . $next);
                exit;
            } catch (PDOException $exception) {
                if ($exception->getCode() === '23000') {
                    $error = 'That username or email is already registered.';
                } else {
                    throw $exception;
                }
            }
        }
    } else {
        $identity = trim((string) ($_POST['identity'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');
        $lookup = $db->prepare(
            "SELECT id, username, password_hash FROM users
             WHERE (username = :username OR email = :email) AND status = 'active'
             LIMIT 1"
        );
        $lookup->execute(['username' => $identity, 'email' => $identity]);
        $account = $lookup->fetch();
        if (!$account || !password_verify($password, $account['password_hash'])) {
            $error = 'The username/email or password is incorrect.';
        } else {
            session_regenerate_id(true);
            $_SESSION['user_id'] = (int) $account['id'];
            $_SESSION['username'] = $account['username'];
            header('Location: ' . $next);
            exit;
        }
    }

    $user = current_user($db);
}
?>
<!DOCTYPE html>
<html>

<head>
    <title><?= $mode === 'register' ? 'Create account' : 'Sign in' ?> - ABC Fast or Slow</title>
    <link rel="stylesheet" href="../src/css/classes.css">
    <link rel="stylesheet" href="../src/css/style.css">
</head>

<body>
    <main class="auth-page">
        <section class="room-entry bg-blue auth-panel">
            <h1><?= $mode === 'register' ? 'Create your account' : 'Welcome back' ?></h1>
            <?php if ($user) { ?>
                <p>Signed in as <?= escape_html($user['username']) ?>.</p>
                <a class="btn-classic hover-grn" href="<?= escape_html($next) ?>">Continue</a>
                <form method="post">
                    <input type="hidden" name="csrf_token" value="<?= escape_html(csrf_token()) ?>">
                    <button class="btn-classic hover-red" type="submit" name="action" value="logout">Sign out</button>
                </form>
            <?php } else { ?>
                <?php if ($error !== '') { ?>
                    <p class="form-error" role="alert"><?= escape_html($error) ?></p>
                <?php } ?>
                <form class="flex-col g-1" method="post">
                    <input type="hidden" name="csrf_token" value="<?= escape_html(csrf_token()) ?>">
                    <input type="hidden" name="next" value="<?= escape_html($next) ?>">
                    <input type="hidden" name="mode" value="<?= escape_html($mode) ?>">
                    <?php if ($mode === 'register') { ?>
                        <label for="username">Username</label>
                        <input class="input-classic" id="username" name="username" minlength="3" maxlength="20" pattern="[A-Za-z0-9_]{3,20}" autocomplete="username" required>
                        <label for="email">Email</label>
                        <input class="input-classic" id="email" name="email" type="email" maxlength="100" autocomplete="email" required>
                    <?php } else { ?>
                        <label for="identity">Username or email</label>
                        <input class="input-classic" id="identity" name="identity" maxlength="100" autocomplete="username" required>
                    <?php } ?>
                    <label for="password">Password</label>
                    <input class="input-classic" id="password" name="password" type="password" minlength="8" autocomplete="<?= $mode === 'register' ? 'new-password' : 'current-password' ?>" required>
                    <?php if ($mode === 'register') { ?>
                        <label for="confirm-password">Confirm password</label>
                        <input class="input-classic" id="confirm-password" name="confirm_password" type="password" minlength="8" autocomplete="new-password" required>
                    <?php } ?>
                    <button class="btn-classic hover-grn" type="submit"><?= $mode === 'register' ? 'Create account' : 'Sign in' ?></button>
                </form>
                <p>
                    <?php if ($mode === 'register') { ?>
                        Already registered? <a href="auth.php?mode=login&amp;next=<?= rawurlencode($next) ?>">Sign in</a>
                    <?php } else { ?>
                        New here? <a href="auth.php?mode=register&amp;next=<?= rawurlencode($next) ?>">Create an account</a>
                    <?php } ?>
                </p>
            <?php } ?>
            <a href="../index.php">Back to home</a>
        </section>
    </main>
</body>

</html>
