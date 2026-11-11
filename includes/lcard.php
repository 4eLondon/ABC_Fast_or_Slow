<?php // Letter card: set $lw (width) and $let (letter) before including ?>
<div class="lcard" style="--lw: <?= $lw ?>">
    <span class="corner pick"><?= $let ?></span>
    <div class="lband"><span class="letter pick"><?= $let ?></span></div>
    <span class="corner corner-b pick"><?= $let ?></span>
</div>
