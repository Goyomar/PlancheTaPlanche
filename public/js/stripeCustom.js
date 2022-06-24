let stripe = Stripe('pk_test_51LDqZ8LPOjbDcq9QBhSJKYpUFuKpQ2H4AIexHu2LecrdIAdnzCYDnep2OvARmuGidnLfGsStBHlsIXCerr2m5DQO00YVrBRML1')
let elements = stripe.elements()

let card = elements.create("card")
card.mount("#card-element")

card.addEventListener("change", (event) => {
    let displayError = document.getElementById("card-errors")
    if(event.error){
        displayError.textContent = event.error.message;
    }else{
        displayError.textContent = "";
    }
})

var form = document.getElementById('payment-form')
form.addEventListener('submit', function(event) {
    event.preventDefault()

    stripe.createToken(card).then(function(result) {
        if(result.error) {
            var errorElement = document.getElementById('card-errors')
            errorElement.textContent = result.error.message
        } else {
            stripeTokenHandler(result.token)
        }
    })
})

function stripeTokenHandler(token) {
    var form = document.getElementById('payment-form')
    var hiddenInput = document.createElement('input')
    hiddenInput.setAttribute('type', 'hidden')
    hiddenInput.setAttribute('name', 'stripeToken')
    hiddenInput.setAttribute('value', token.id)
    form.appendChild(hiddenInput)
    form.submit()
}