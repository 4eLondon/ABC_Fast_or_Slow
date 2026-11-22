const letters = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";

const speedButton = document.getElementById("speedButton");
const speedIcon = document.getElementById("speedIcon");
const speedText = document.getElementById("speedText");
const cardButton = document.getElementById("cardButton");
const letterText = document.getElementById("letter");
const countdown = document.getElementById("countdown");
const nextForm = document.getElementById("nextForm");

let speed = speedText.textContent;
let busy = false;

// Switch between fast and slow, then tell PHP
speedButton.addEventListener("click", function () {
    if (speed === "fast") {
        speed = "slow";
    } else {
        speed = "fast";
    }

    speedText.textContent = speed;
    speedIcon.src = "../assets/icons/icon-" + speed + "-red.svg";
    fetch("../includes/game.php?speed=" + speed);
});

// Ask PHP for a new letter, then shuffle through the alphabet to land on it
cardButton.addEventListener("click", function () {
    if (busy) return;
    busy = true;

    fetch("../includes/game.php?reroll=1&speed=" + speed)
        .then(function (response) {
            return response.text();
        })
        .then(function (chosen) {
            let position = Math.floor(Math.random() * 26);
            let target = letters.indexOf(chosen.trim());
            let steps = 26 + ((target - position + 26) % 26);

            let delay = 50;
            if (speed === "slow") {
                delay = 90;
            }

            function nextLetter() {
                position = (position + 1) % 26;
                letterText.textContent = letters[position];
                steps = steps - 1;

                if (steps > 0) {
                    setTimeout(nextLetter, delay);
                } else {
                    busy = false;
                }
            }

            nextLetter();
        });
});

// Start the next round automatically when the countdown reaches 0
let count = Number(countdown.textContent);
const timer = setInterval(function () {
    count = count - 1;
    countdown.textContent = count;

    if (count <= 0) {
        clearInterval(timer);
        nextForm.submit();
    }
}, 1000);

// Click a word to disqualify it, then reload to show the new scores
const results = document.querySelectorAll(".result");

for (let i = 0; i < results.length; i++) {
    results[i].addEventListener("click", function () {
        fetch("../includes/game.php?disqualify=" + results[i].dataset.key)
            .then(function () {
                location.reload();
            });
    });
}
