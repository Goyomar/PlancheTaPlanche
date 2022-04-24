// SELECTION ETOILE POUR MODULE NON VOTE
let etoile1 = document.querySelector("#star1")
let etoile2 = document.querySelector("#star2")
let etoile3 = document.querySelector("#star3")
let etoile4 = document.querySelector("#star4")
let etoile5 = document.querySelector("#star5")
// SELECTION DES ETOILES POUR DEJA VOTE
let etoilesNodes = document.querySelectorAll(".fa-star.star-gray")
let etoiles = Array.from(etoilesNodes)

etoile1.addEventListener("mouseenter", function() {
    etoile2.classList.toggle("star")
    etoile3.classList.toggle("star")
    etoile4.classList.toggle("star")
    etoile5.classList.toggle("star")
})
etoile2.addEventListener("mouseenter", function() {
    etoile3.classList.toggle("star")
    etoile4.classList.toggle("star")
    etoile5.classList.toggle("star")
    if (etoiles.includes(etoile2)) {
        etoile2.classList.replace("star-gray", "star-bis")
    }
})
etoile3.addEventListener("mouseenter", function() {
    etoile4.classList.toggle("star")
    etoile5.classList.toggle("star")
    if (etoiles.includes(etoile3)) {
        etoile2.classList.replace("star-gray", "star-bis")
        etoile3.classList.replace("star-gray", "star-bis")
    }
})
etoile4.addEventListener("mouseenter", function() {
    etoile5.classList.toggle("star")
    if (etoiles.includes(etoile4)) {
        etoile2.classList.replace("star-gray", "star-bis")
        etoile3.classList.replace("star-gray", "star-bis")
        etoile4.classList.replace("star-gray", "star-bis")
    }
})
etoile5.addEventListener("mouseenter", function() {
    if (etoiles.includes(etoile5)) {
        etoile2.classList.replace("star-gray", "star-bis")
        etoile3.classList.replace("star-gray", "star-bis")
        etoile4.classList.replace("star-gray", "star-bis")
        etoile5.classList.replace("star-gray", "star-bis")
    }
})

etoile1.addEventListener("mouseout", function() {
    etoile2.classList.toggle("star")
    etoile3.classList.toggle("star")
    etoile4.classList.toggle("star")
    etoile5.classList.toggle("star")
})
etoile2.addEventListener("mouseout", function() {
    etoile3.classList.toggle("star")
    etoile4.classList.toggle("star")
    etoile5.classList.toggle("star")
    if (etoiles.includes(etoile2)) {
        etoile2.classList.replace("star-bis", "star-gray")
    }
})
etoile3.addEventListener("mouseout", function() {
    etoile4.classList.toggle("star")
    etoile5.classList.toggle("star")
    if (etoiles.includes(etoile3)) {
        etoile2.classList.replace("star-bis", "star-gray")
        etoile3.classList.replace("star-bis", "star-gray")
    }
})
etoile4.addEventListener("mouseout", function() {
    etoile5.classList.toggle("star")
    if (etoiles.includes(etoile4)) {
        etoile2.classList.replace("star-bis", "star-gray")
        etoile3.classList.replace("star-bis", "star-gray")
        etoile4.classList.replace("star-bis", "star-gray")
    }
})
etoile5.addEventListener("mouseout", function() {
    if (etoiles.includes(etoile5)) {
        etoile2.classList.replace("star-bis", "star-gray")
        etoile3.classList.replace("star-bis", "star-gray")
        etoile4.classList.replace("star-bis", "star-gray")
        etoile5.classList.replace("star-bis", "star-gray")
    }
})