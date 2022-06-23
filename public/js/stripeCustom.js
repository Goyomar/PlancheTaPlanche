wiwnfow.onLoad = function() {
    let stripe = Stripe('pk_test_51LDqZ8LPOjbDcq9QBhSJKYpUFuKpQ2H4AIexHu2LecrdIAdnzCYDnep2OvARmuGidnLfGsStBHlsIXCerr2m5DQO00YVrBRML1')
    let elements = stripe.elements()
    let redirect = "/"

    let cardHolderName = "" // récup le nom de l'utilisateur en JS
    let carButton = // recup info de carte dans formulaire
    let clientSecret = cardButton.dataset.secret // la ou j'ai mis la clé secrete

    let card = elements.create("card")
    card.mount("#card-element")

    // génére le bon message d'erreur a voir si ca marche
    card.addEventListener("change", (event) => {
        let displayError = document.getElementById("card-errors")
        if(event.error){
            displayError.textContent = event.error.message;
        }else{
            displayError.textContent = "";
        }
    })

        // On gère le paiement
        cardButton.addEventListener("click", () => {
            stripe.handleCardPayment(
                clientSecret, card, {
                    payment_method_data: {
                        billing_details: {name: cardHolderName.value}
                    }
                }
            ).then((result) => {
                if(result.error){
                    document.getElementById("errors").innerText = result.error.message
                }else{
                    document.location.href = redirect
                }
            })
        })
}