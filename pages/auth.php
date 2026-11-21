<?php
session_start();
require_once '../includes/app.php';
require_once '../includes/rooms.php';

$db = database_connection();
$currentUser = current_user($db);
$authMode = ($_GET['mode'] ?? 'login') === 'register' ? 'register' : 'login';
$redirectTarget = auth_redirect_target((string) ($_GET['next'] ?? ''));
$formError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $authMode = ($_POST['mode'] ?? 'login') === 'register' ? 'register' : 'login';
    $redirectTarget = auth_redirect_target((string) ($_POST['next'] ?? ''));

    if (!verify_csrf_token()) {
        $formError = 'Your session expired. Refresh the page and try again.';
    } elseif (($_POST['action'] ?? '') === 'logout') {
        $_SESSION = [];
        session_regenerate_id(true);
        header('Location: ../index.php');
        exit;
    } elseif ($authMode === 'register') {
        // AI assisted lines 22-40
        $username = trim((string) ($_POST['username'] ?? ''));
        $email = trim((string) ($_POST['email'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');
        $confirm = (string) ($_POST['confirm_password'] ?? '');

        if (!preg_match('/^[A-Za-z0-9_]{3,20}$/', $username)) {
            $formError = 'Use a username with 3-20 letters, numbers, or underscores.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 100) {
            $formError = 'Enter a valid email address.';
        } elseif (strlen($password) < 8) {
            $formError = 'Password must be at least 8 characters.';
        } elseif ($password !== $confirm) {
            $formError = 'Passwords do not match.';
        } else {
            try {
                // Store a password hash instead of the original password.
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
                header('Location: ' . $redirectTarget);
                exit;
            } catch (PDOException $exception) {
                if ($exception->getCode() === '23000') {
                    $formError = 'That username or email is already registered.';
                } else {
                    throw $exception;
                }
            }
        }
    } else {
        // AI assisted line 63-81
        // The same field can match either the username or email column.
        $usernameOrEmail = trim((string) ($_POST['identity'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');
        $accountLookup = $db->prepare(
            "SELECT id, username, password_hash FROM users
             WHERE (username = :username OR email = :email) AND status = 'active'
             LIMIT 1"
        );
        $accountLookup->execute(['username' => $usernameOrEmail, 'email' => $usernameOrEmail]);
        $account = $accountLookup->fetch();
        if (!$account || !password_verify($password, $account['password_hash'])) {
            $formError = 'The username/email or password is incorrect.';
        } else {
            session_regenerate_id(true);
            $_SESSION['user_id'] = (int) $account['id'];
            $_SESSION['username'] = $account['username'];
            header('Location: ' . $redirectTarget);
            exit;
        }
    }

    $currentUser = current_user($db);
}
?>
<!DOCTYPE html>
<html>

<head>
    <title><?= $authMode === 'register' ? 'Create account' : 'Sign in' ?> - ABC Fast or Slow</title>
    <link rel="stylesheet" href="../src/css/classes.css">
    <link rel="stylesheet" href="../src/css/style.css">
</head>

<body>
    <main class="auth-page">
        <section class="room-entry bg-blue auth-panel">
            <h1><?= $authMode === 'register' ? 'Create your account' : 'Welcome back' ?></h1>
            <?php if ($currentUser) { ?>
                <p>Signed in as <?= escape_html($currentUser['username']) ?>.</p>
                <a class="btn-classic hover-grn" href="<?= escape_html($redirectTarget) ?>">Continue</a>
                <form method="post">
                    <input type="hidden" name="csrf_token" value="<?= escape_html(csrf_token()) ?>">
                    <button class="btn-classic hover-red" type="submit" name="action" value="logout">Sign out</button>
                </form>
            <?php } else { ?>
                <?php if ($formError !== '') { ?>
                    <p class="form-error" role="alert"><?= escape_html($formError) ?></p>
                <?php } ?>
                <form class="flex-col g-1" method="post">
                    <input type="hidden" name="csrf_token" value="<?= escape_html(csrf_token()) ?>">
                    <input type="hidden" name="next" value="<?= escape_html($redirectTarget) ?>">
                    <input type="hidden" name="mode" value="<?= escape_html($authMode) ?>">
                    <?php if ($authMode === 'register') { ?>
                        <label for="username">Username</label>
                        <input class="input-classic" id="username" name="username" minlength="3" maxlength="20" pattern="[A-Za-z0-9_]{3,20}" autocomplete="username" required>
                        <label for="email">Email</label>
                        <input class="input-classic" id="email" name="email" type="email" maxlength="100" autocomplete="email" required>
                    <?php } else { ?>
                        <label for="identity">Username or email</label>
                        <input class="input-classic" id="identity" name="identity" maxlength="100" autocomplete="username" required>
                    <?php } ?>
                    <label for="password">Password</label>
                    <input class="input-classic" id="password" name="password" type="password" minlength="8" autocomplete="<?= $authMode === 'register' ? 'new-password' : 'current-password' ?>" required>
                    <?php if ($authMode === 'register') { ?>
                        <label for="confirm-password">Confirm password</label>
                        <input class="input-classic" id="confirm-password" name="confirm_password" type="password" minlength="8" autocomplete="new-password" required>
                    <?php } ?>
                    <button class="btn-classic hover-grn" type="submit"><?= $authMode === 'register' ? 'Create account' : 'Sign in' ?></button>
                </form>
                <p>
                    <?php if ($authMode === 'register') { ?>
                        Already registered? <a href="auth.php?mode=login&amp;next=<?= rawurlencode($redirectTarget) ?>">Sign in</a>
                    <?php } else { ?>
                        New here? <a href="auth.php?mode=register&amp;next=<?= rawurlencode($redirectTarget) ?>">Create an account</a>
                    <?php } ?>
                </p>
            <?php } ?>
            <a href="../index.php">Back to home</a>
        </section>
    </main>
</body>

</html>
