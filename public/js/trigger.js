// form APPEAR
let form = document.querySelector("#form")
let trigger = document.querySelector("#form-trigger")

trigger.addEventListener("click", function() {
    form.classList.toggle("dnone")
    trigger.classList.add("dnone")
})

// modal
var modal = document.getElementById("myModal");
var btn = document.getElementById("myBtn");
var span = document.getElementsByClassName("close")[0];

btn.onclick = function() {
  modal.style.display = "block";
}

span.onclick = function() {
  modal.style.display = "none";
}

window.onclick = function(event) {
  if (event.target == modal) {
    modal.style.display = "none";
  }
}