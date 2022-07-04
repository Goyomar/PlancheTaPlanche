let form = document.querySelector("#form")
let trigger = document.querySelector("#form-trigger")

trigger.addEventListener("click", function() {
    form.classList.toggle("dnone")
    trigger.classList.add("dnone")
})