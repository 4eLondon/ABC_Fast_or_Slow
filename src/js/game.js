const turnScreen = document.getElementById("turnScreen");
const gameScreen = document.getElementById("gameScreen");
const countdown = document.getElementById("countdown");
const timeLeft = document.getElementById("timeLeft");
const form = document.getElementById("answerForm");
const inputs = document.querySelectorAll(".answer-input");
const errors = document.querySelectorAll(".answer-error");
const roundIcon = document.getElementById("roundIcon");
const totalSeconds = Number(gameScreen.dataset.seconds);

roundIcon.style.transition = "transform 1s linear";

const letter = gameScreen.dataset.letter;
let seconds = Number(gameScreen.dataset.seconds);
let count = 3;
let submitted = false;

// Send the answers to PHP (only once)
function submitAnswers() {
    if (submitted) return;
    submitted = true;
    form.submit();
}

// Answer timer, sends the answers when time runs out
function startTimer() {
    const timer = setInterval(function () {
        seconds = seconds - 1;
        timeLeft.textContent = "Time " + seconds;
        roundIcon.style.transform = "rotate(" + (360 * (totalSeconds - seconds) / totalSeconds) + "deg)";

        if (seconds <= 0) {
            clearInterval(timer);
            submitAnswers();
        }
    }, 1000);
}

// Count down, then swap from the turn screen to the game screen
const countTimer = setInterval(function () {
    count = count - 1;

    if (count > 0) {
        countdown.textContent = count;
    } else {
        clearInterval(countTimer);
        turnScreen.classList.add("hidden");
        gameScreen.classList.remove("hidden");
        document.getElementById("master").style.backgroundImage = "url(../assets/backgrounds/screen1.svg)";
        inputs[0].focus();
        startTimer();
    }
}, 1000);

for (let i = 0; i < inputs.length; i++) {

    // Say so if the word starts with the wrong letter
    inputs[i].addEventListener("input", function () {
        const word = inputs[i].value.trim();

        if (word !== "" && word[0].toLowerCase() !== letter.toLowerCase()) {
            errors[i].textContent = "Must start with " + letter;
        } else {
            errors[i].textContent = "";
        }
    });

    // Enter skips to the next card instead of submitting
    inputs[i].addEventListener("keydown", function (event) {
        if (event.key === "Enter") {
            event.preventDefault();
            if (i < inputs.length - 1) {
                inputs[i + 1].focus();
            }
        }
    });
}
