const letters = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";

const speedButton = document.getElementById("speedButton");
const speedIcon = document.getElementById("speedIcon");
const speedText = document.getElementById("speedText");
const cardButton = document.getElementById("cardButton");
const letterText = document.getElementById("letter");

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
    speedIcon.src = "../assets/icons/icon-" + speed + "-yellow.svg";
    fetch("../includes/game.php?speed=" + speed);
});

// Ask PHP for a letter, then shuffle through the alphabet to land on it
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

            // One full lap, then on to the chosen letter
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


// Show or hide the new player form
const addButton = document.getElementById("addButton");
const addForm = document.getElementById("addNewPlayer");

if (addButton) {
    addButton.addEventListener("click", function () {
        addForm.classList.toggle("hidden");
    });
}

// Right click a player to remove them
const playerButtons = document.querySelectorAll(".player-button");

for (let i = 0; i < playerButtons.length; i++) {
    playerButtons[i].addEventListener("contextmenu", function (event) {
        event.preventDefault();
        window.location = "../includes/game.php?remove=" + playerButtons[i].dataset.index;
    });
}
