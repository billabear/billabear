
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

        var style = {
            base: {
                color: "#32325d",
                fontFamily: 'Arial, sans-serif',
                fontSmoothing: "antialiased",
                fontSize: "16px",
                "::placeholder": {
                    color: "#32325d"
                }
            },
            invalid: {
                fontFamily: 'Arial, sans-serif',
                color: "#fa755a",
                iconColor: "#fa755a"
            }
        };

        var card = elements.create("card", { style: style });
        card.mount("#cardInput");

        card.on("change", function (event) {
            // Disable the Pay button if there are no card details in the Element
            document.querySelector("button").disabled = event.empty;
            document.querySelector("#card-error").textContent = event.error ? event.error.message : "";
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
