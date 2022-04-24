  // ACCORDEON
  let accordeon = document.querySelector("#faq-accordeon")
  let touche0 = document.querySelector("#touche0")
  let touche1 = document.querySelector("#touche1")
  let touche2 = document.querySelector("#touche2")

  touche1.nextElementSibling.lastElementChild.classList.add("dnone")
  touche2.nextElementSibling.lastElementChild.classList.add("dnone")

  touche0.parentElement.addEventListener("click",function(){
      showReponse(accordeon, touche0)
  })
  touche1.parentElement.addEventListener("click",function(){
      showReponse(accordeon, touche1)
  })
  touche2.parentElement.addEventListener("click",function(){
      showReponse(accordeon, touche2)
  })

  function showReponse(accordeon, selecTouche) {
      let childList = accordeon.childNodes
      for (var i = 0; i < childList.length; i++) {
          if (childList[i].classList) {
              if (childList[i].classList.contains('touche')) {
                  if (childList[i].firstElementChild == selecTouche){
                      childList[i].firstElementChild.classList.replace("fa-plus","fa-minus")
                      childList[i].lastElementChild.lastElementChild.classList.remove("dnone")
                  } else {
                      childList[i].firstElementChild.classList.replace("fa-minus","fa-plus")
                      childList[i].lastElementChild.lastElementChild.classList.add("dnone")
                  }
              }
          }
      }
  }