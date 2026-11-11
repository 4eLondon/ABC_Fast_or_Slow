// Spin wheel: turn a random amount, then show the letter under the pointer
const abc = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
let turn = 0;

document.querySelectorAll(".wheel-btn").forEach((btn) => {
    const wheel = btn.parentElement.querySelector(".wheel");

    btn.onclick = () => {
        turn += 1440 + Math.random() * 360;
        wheel.style.transform = `rotate(${turn}deg)`;
    };

    wheel.ontransitionend = () => {
        const l = abc[Math.floor(((360 - (turn % 360)) % 360) / (360 / 26))];
        document.querySelectorAll(".pick").forEach((e) => (e.textContent = l));
        const go = document.querySelector("#go");
        if (go) go.href = go.href.split("&l=")[0] + "&l=" + l;
    };
});
