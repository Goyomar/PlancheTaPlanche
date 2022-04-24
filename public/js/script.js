let chevron = document.querySelector(".fa-chevron-up").addEventListener("click", () => {
    document.querySelector("body").scrollIntoView({behavior: "smooth", block: "start"})
})