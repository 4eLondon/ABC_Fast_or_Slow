const game = document.querySelector("#master[data-room-code]");

if (game) {
    const timer = document.querySelector("#game-timer");
    const roundCount = document.querySelector("#round-count");
    const inputs = [...document.querySelectorAll("#answers input")];
    const submitButton = document.querySelector("#submit-answers");
    const nextButton = document.querySelector("#next-round");
    const overlayTitle = document.querySelector("#spin h2");
    const wheelBox = document.querySelector("#spin .wheel-box");
    const status = document.querySelector("#game-status");
    const endpoint = "game_action.php";
    const roomCode = game.dataset.roomCode;
    const csrfToken = game.dataset.csrfToken;
    const totalRounds = Number(game.dataset.rounds);
    let roundId = Number(game.dataset.roundId);
    let round = Number(game.dataset.roundNumber);
    let roundEnded = game.dataset.ended === "true";
    let roundStopped = game.dataset.roundStopped === "true";
    let submitted = game.dataset.submitted === "true";
    let finalRound = game.dataset.finalRound === "true";
    let deadline = Date.now() + Number(game.dataset.remaining) * 1000;
    let interval = null;
    let saving = false;
    let syncFailed = false;

    const currentLetter = () => document.querySelector("#game_main .letter.pick").textContent.trim().toUpperCase();

    const validateInput = (input) => {
        const answer = input.value.trim();
        const letter = currentLetter();
        const valid = answer === "" || answer.toLocaleUpperCase().startsWith(letter);
        const message = valid ? "" : `Answer must start with ${letter}.`;
        input.setCustomValidity(message);
        input.closest(".card").classList.toggle("answer-invalid", !valid);
        input.nextElementSibling.textContent = message;
        return valid;
    };

    const setStatus = (message) => {
        status.textContent = message;
        status.hidden = message === "";
    };

    const formatTime = (seconds) => `${Math.floor(seconds / 60)}:${String(seconds % 60).padStart(2, "0")}`;

    const request = async (values) => {
        const body = new URLSearchParams({
            csrf_token: csrfToken,
            room: roomCode,
            ...values,
        });
        const response = await fetch(endpoint, {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded;charset=UTF-8" },
            body,
        });
        const result = await response.json();
        if (!response.ok) throw new Error(result.error || "The game request could not be completed.");
        return result;
    };

    const syncGame = async () => {
        if (saving) return;
        try {
            const response = await fetch(`${endpoint}?action=state&room=${encodeURIComponent(roomCode)}`);
            const state = await response.json();
            if (!response.ok) throw new Error(state.error || "Could not sync the game.");
            if (syncFailed) setStatus("");
            syncFailed = false;
            if (state.status !== "playing") {
                window.location.href = "../index.php";
            } else if (state.roundId !== roundId || (state.roundEnded && !roundEnded)) {
                window.location.reload();
            }
        } catch (error) {
            syncFailed = true;
            setStatus("Connection interrupted. Trying to reconnect...");
        }
    };

    const finishRound = () => {
        roundEnded = true;
        roundStopped = true;
        window.clearInterval(interval);
        inputs.forEach((input) => {
            input.disabled = true;
        });
        submitButton.disabled = true;
        timer.textContent = "0:00";
        overlayTitle.textContent = finalRound ? "Game complete" : "Round complete";
        if (wheelBox) wheelBox.hidden = finalRound;
        nextButton.textContent = finalRound ? "Return home" : "Next round";
        nextButton.disabled = false;
        window.location.hash = "spin";
    };

    const submitRound = async (timedOut) => {
        if (saving || submitted || roundStopped) return;
        saving = true;
        setStatus("");

        if (timedOut) {
            inputs.forEach((input) => {
                if (!validateInput(input)) {
                    input.value = "";
                    validateInput(input);
                }
            });
        } else if (!inputs.map(validateInput).every(Boolean)) {
            inputs.find((input) => !input.checkValidity()).reportValidity();
            saving = false;
            return;
        }

        try {
            const body = {
                action: "submit",
                round_id: String(roundId),
                timed_out: timedOut ? "1" : "0",
            };
            const answerFields = new URLSearchParams();
            inputs.forEach((input) => answerFields.append(input.name, input.value));
            const result = await request({ ...body, ...Object.fromEntries(answerFields) });
            submitted = true;
            if (result.alreadySubmitted) roundStopped = true;
            finishRound();
        } catch (error) {
            setStatus(error.message);
            if (!submitted) submitButton.disabled = false;
        } finally {
            saving = false;
        }
    };

    const startTimer = (seconds) => {
        window.clearInterval(interval);
        roundEnded = false;
        roundStopped = false;
        submitted = false;
        deadline = Date.now() + seconds * 1000;
        timer.textContent = formatTime(seconds);
        submitButton.disabled = false;
        inputs.forEach((input) => {
            input.disabled = false;
            input.value = "";
            validateInput(input);
        });
        interval = window.setInterval(() => {
            const remaining = Math.max(0, Math.ceil((deadline - Date.now()) / 1000));
            timer.textContent = formatTime(remaining);
            if (remaining === 0) {
                window.clearInterval(interval);
                submitRound(true);
            }
        }, 200);
    };

    inputs.forEach((input) => {
        input.addEventListener("input", () => validateInput(input));
    });

    window.setInterval(syncGame, 2000);

    submitButton.addEventListener("click", () => {
        submitRound(Date.now() >= deadline);
    });

    document.addEventListener("game:letter-picked", () => {
        if (roundEnded && !finalRound) nextButton.disabled = false;
    });

    nextButton.addEventListener("click", async () => {
        if (nextButton.disabled) return;

        nextButton.disabled = true;
        setStatus("");
        try {
            const result = await request({ action: "next", letter: currentLetter() });
            if (result.finished) {
                window.location.href = "../index.php";
                return;
            }

            roundId = result.roundId;
            round = result.roundNumber;
            finalRound = round >= totalRounds;
            game.dataset.roundId = String(roundId);
            game.dataset.roundNumber = String(round);
            game.dataset.letter = result.letter;
            game.dataset.finalRound = finalRound ? "true" : "false";
            roundCount.textContent = `Round ${round} of ${totalRounds}`;
            document.querySelectorAll(".pick").forEach((element) => {
                element.textContent = result.letter;
            });
            overlayTitle.textContent = "Round complete";
            if (wheelBox) wheelBox.hidden = finalRound;
            nextButton.textContent = finalRound ? "Finish game" : "Next round";
            window.location.hash = "";
            startTimer(result.remaining);
        } catch (error) {
            setStatus(error.message);
            nextButton.disabled = false;
        }
    });

    if (roundEnded) {
        if (!roundStopped && !submitted) {
            submitRound(true);
        } else {
            overlayTitle.textContent = finalRound ? "Game complete" : "Round complete";
            if (wheelBox) wheelBox.hidden = finalRound;
            nextButton.textContent = finalRound ? "Return home" : "Next round";
            nextButton.disabled = false;
            window.location.hash = "spin";
        }
    } else {
        startTimer(Number(game.dataset.remaining));
    }
}
