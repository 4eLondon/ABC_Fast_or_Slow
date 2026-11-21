<?php
// AI assisted
$wheelColors = ['red', 'tang', 'yel', 'grn', 'blue', 'teal'];
$letters = range('A', 'Z');
$degreesPerLetter = 360 / count($letters);
$gradientStops = [];
foreach ($letters as $letterIndex => $letter) {
    $startAngle = round($letterIndex * $degreesPerLetter, 2);
    $endAngle = round(($letterIndex + 1) * $degreesPerLetter, 2);
    $color = $wheelColors[$letterIndex % count($wheelColors)];
    $gradientStops[] = "var(--{$color}) {$startAngle}deg {$endAngle}deg";
}
?>
<div class="wheel-box">
    <div class="wheel" style="background: conic-gradient(<?= implode(', ', $gradientStops) ?>)">
        <?php foreach ($letters as $letterIndex => $letter) { ?>
            <span style="--i: <?= $letterIndex ?>"><?= $letter ?></span>
        <?php } ?>
    </div>
    <button class="wheel-btn">Spin</button>
</div>
