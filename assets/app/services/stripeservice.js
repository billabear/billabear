function redirectToCheckout(apiKey, sessionId) {
    addJs();
    setTimeout(function () {

        var stripe = Stripe(apiKey);
        return stripe.redirectToCheckout({sessionId: sessionId});
    }, 500);
}

function getCardToken(stripe, client_secret) {
    var elements = stripe.elements({
        clientSecret: client_secret,
    });

    var card = elements.create('card', {
        iconStyle: 'solid',
        style: {
            base: {
                color: 'black',
                fontWeight: 500,
                fontFamily: 'Roboto, Open Sans, Segoe UI, sans-serif',
                fontSize: '16px',
                fontSmoothing: 'antialiased',

                ':-webkit-autofill': {
                    color: '#fce883',
                },
                '::placeholder': {
                    color: 'black',
                },
            },
            invalid: {
                iconColor: 'red',
                color: 'red',
            },
        },
    });

    card.mount("#cardInput");

    const cardElement = document.querySelector('.StripeElement');
    card.on("change", function (event) {
        // Disable the Pay button if there are no card details in the Element
        document.querySelector(".btn--main").disabled = event.empty;
        document.querySelector("#cardError").textContent = event.error ? event.error.message : "";
    });

    return card;


}

function sendCard(stripe, card) {


    return stripe.createToken(card);
}

function addJs() {

}




export const stripeservice = {
    redirectToCheckout,
    getCardToken,
    sendCard,
}
