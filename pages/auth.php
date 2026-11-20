<?php
$pageTitle = "Login / Sign up";
?>

<!DOCTYPE HTML>
<html>

<head>
    <title><?= $pageTitle ?></title>
    <link rel="stylesheet" href="../src/css/classes.css">
    <link rel="stylesheet" href="../src/css/master.css">
    <link rel="stylesheet" href="../src/css/auth.css">
</head>

<body>


    <div id="master" class="fullscreen flex-col items-center">

        <!-------------------------------------------------------|
             x x x x x x x x [ Main Section ] x x x x x x x x x 
        |-------------------------------------------------------->

        <section id="auth" class="fill flex-row items-center">
            <form id="login" class="hidden flex-col gap-1 txt-regular-1">
                <h1 class="txt-color auth-title">Welcome Back User</h1>

                <ul class="flex-col gap-1">
                    <li> <input type="text" class="input-text-classic" name="field_username" maxlength="30" placeholder="Username"></li>
                    <li><span class="flex-col">
                            <input type="password" class="input-text-classic" name="field_password" maxlength="50" minlength="4" placeholder="Password">
                            or
                            <input type="email" class="input-text-classic" name="field_email" placeholder="Email" required>
                        </span></li>

                    <li> <button type="submit" class="button-classic-alt accent-4">Login</button></li>
                    <li>
                        <h3 class="form-link">Dont have an account? <a href="#signup" class="accent-4-txt"> Sign up here</a></h3>
                    </li>
                </ul>
            </form>

            <form id="signup" class="visible txt-regular-1">
                <h1 class="txt-color auth-title">Create your account</h1>

                <ul class="flex-col gap-1">
                    <li> <input type="text" class="input-text-classic" name="field_username" maxlength="30" placeholder="Username" required></li>

                    <li> <input type="email" class="input-text-classic" name="field_email" placeholder="Email" required></li>

                    <li> <input type="password" class="input-text-classic" name="field_password" maxlength="50" minlength="4" placeholder="Password" required></li>

                    <li> <input type="password" class="input-text-classic" name="field_confirm_password" maxlength="50" minlength="4" placeholder="Confirm Password" required></li>

                    <li><button type="submit" class="button-classic-alt accent-4">Sign Up</button></li>
                    <li>
                        <h3 class="form-link">Already have an account?<a href="#login" class="accent-4-txt"> Log in</a></h3>
                    </li>
                </ul>
            </form>
        </section>




        <!-------------------------------------------------------|
             x x x x x x x x [ Decals Section ] x x x x x x x x x 
        |-------------------------------------------------------->

        <section id="decals" class="fill flex-col items-center-horizontal">
            <nav class="fullscreen-width film-bar lock-top flex-row items-center">

                <a href="../index.php" id="navi">
                    <div id="naviInner" class="flex-row txt-regular-4 gap-1 items-center-vertical">
                        <img src="../assets/icons/icon-home-dark.svg">
                        <h2>Home</h2>
                    </div>
                </a>

            </nav>

            <footer class="fullscreen-width film-bar lock-bot">

            </footer>
        </section>

    </div>

</body>

</html>
