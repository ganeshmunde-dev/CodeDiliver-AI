// ======================
// TYPING EFFECT
// ======================

const texts = [
    "Analyze Projects",
    "Detect Bugs",
    "Improve Code",
    "Find Security Issues",
    "Boost Performance"
];

let index = 0;
let charIndex = 0;

const typingElement = document.getElementById("typing-text");

function typeEffect() {

    if (!typingElement) return;

    if (charIndex < texts[index].length) {

        typingElement.textContent += texts[index].charAt(charIndex);

        charIndex++;

        setTimeout(typeEffect, 100);

    } else {

        setTimeout(eraseEffect, 1500);
    }
}

function eraseEffect() {

    if (charIndex > 0) {

        typingElement.textContent =
            texts[index].substring(0, charIndex - 1);

        charIndex--;

        setTimeout(eraseEffect, 50);

    } else {

        index++;

        if (index >= texts.length) {
            index = 0;
        }

        setTimeout(typeEffect, 300);
    }
}

document.addEventListener("DOMContentLoaded", () => {
    typeEffect();
});


// ======================
// NAVBAR SCROLL EFFECT
// ======================

window.addEventListener("scroll", () => {

    const nav = document.querySelector("nav");

    if (!nav) return;

    if (window.scrollY > 50) {

        nav.style.background =
            "rgba(15,23,42,0.95)";

    } else {

        nav.style.background =
            "rgba(255,255,255,0.05)";
    }
});


// ======================
// REVEAL ANIMATION
// ======================

const reveals =
document.querySelectorAll(".feature-box");

window.addEventListener("scroll", () => {

    reveals.forEach(box => {

        const windowHeight =
            window.innerHeight;

        const revealTop =
            box.getBoundingClientRect().top;

        if (revealTop < windowHeight - 100) {

            box.style.opacity = "1";
            box.style.transform = "translateY(0)";
        }
    });

});


// ======================
// COUNTER EFFECT
// ======================

const counters =
document.querySelectorAll(".counter");

counters.forEach(counter => {

    let start = 0;

    const end =
        parseInt(counter.getAttribute("data-target"));

    const duration = 2000;

    const increment =
        end / (duration / 20);

    function updateCounter() {

        start += increment;

        if (start < end) {

            counter.innerText =
                Math.floor(start);

            setTimeout(updateCounter, 20);

        } else {

            counter.innerText = end;
        }
    }

    updateCounter();

});