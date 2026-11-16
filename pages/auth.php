<?php
$title = "Account"
?>

<!DOCTYPE HTML>
<html>

<head>
    <title><?= $title ?></title>
    <link rel="stylesheet" href="../src/css/master.css">
    <link rel="stylesheet" href="../src/css/auth.css">
    <link rel="stylesheet" href="../src/css/classes.css">
</head>

<body>

    <div id="master" class="flex flex-row fullscreen items-center">

        <section id="auth" class="flex flex-row items-center-y gap-2">

            <section id="auth_panel">
                <img>
            </section>

            <div id="auth_data">

                <section id="signup" class="flex flex-col items-center hidden">
                    <h1>Signup</h1>
                    <form id="" class="flex flex-col items-center gap-1">
                        <input class="input-text-classic" type="text" name="username" maxlength="80" required>
                        <input class="input-text-classic" type="email" name="email">
                        <input class="input-text-classic" type="password" name="password" minlength="8" required>
                        <input class="input-text-classic" type="password" name="confirm-password" minlength="8" required>
                        <button type="submit" class="button-classic">Submit</button>
                        <span>Already have an account? <a>Log in</a></span>

                    </form>
                </section>



                <section id="login" class="flex flex-col items-center">

                    <h1>Login</h1>
                    <form class="flex flex-col items-center gap-1">
                        <div class="flex flex-col gap-1 items-center-y">
                            <input class="input-text-classic" type="text" name="username" maxlength="80">
                            <span>Or</span>
                            <input class="input-text-classic" type="email" name="email">
                        </div>

                        <input class="input-text-classic" type="password" name="password" minlength="8" required>

                        <button type="submit" class="button-classic">Submit</button>

                        <span>Create a new acoount? <a>Here</a></span>
                    </form>

                </section>
            </div>

        </section>

    </div>

</body>

</html>
