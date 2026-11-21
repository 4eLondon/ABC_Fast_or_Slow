<?php
require __DIR__ . '/../includes/components.php';
$pageTitle = "Summary";
?>

<!DOCTYPE HTML>
<html>

<head>
    <title><?= $pageTitle ?></title>
    <link rel="stylesheet" href="../src/css/master.css">
    <link rel="stylesheet" href="../src/css/classes.css:">
    <?php drawFavicon() ?>
</head>

<body>


    <div id="master" class="fullscreen flex-col items-center">

        <!-------------------------------------------------------|
             x x x x x x x x [ Main Section ] x x x x x x x x x 
        |-------------------------------------------------------->

        <section id="" class="fill flex-row items-center"></section>



        <!-------------------------------------------------------|
             x x x x x x x x [ Decals Section ] x x x x x x x x x 
        |-------------------------------------------------------->
        <?php drawDecals(1); ?>

    </div>

</body>

</html>
