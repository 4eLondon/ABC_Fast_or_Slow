<?php
// Spin wheel: one wedge per letter. Colours repeat in this order.
$wc = ['red', 'tang', 'yel', 'grn', 'blue', 'teal'];
$abc = range('A', 'Z');
$step = 360 / 26;
$stops = [];
foreach ($abc as $i => $l) {
    $stops[] = "var(--{$wc[$i % 6]}) " . round($i * $step, 2) . "deg " . round(($i + 1) * $step, 2) . "deg";
}
?>
<div class="wheel-box">
    <div class="wheel" style="background: conic-gradient(<?= implode(', ', $stops) ?>)">
        <?php foreach ($abc as $i => $l) { ?>
            <span style="--i: <?= $i ?>"><?= $l ?></span>
        <?php } ?>
    </div>
    <button class="wheel-btn">Spin</button>
</div>
