<template>
  <div v-if="!loading">

    <form @submit.prevent="send" :disabled="sending">

      <div id="cardInput"></div>

      <div class="mt-3">
        <SubmitButton :in-progress="sending">{{ $t('app.billing.card_form.add_card') }}</SubmitButton>
      </div>
    </form>
  </div>
  <div v-else>
    <LoadingMessage>{{ $t('global.loading_message')}} </LoadingMessage>
  </div>
</template>

<script>
import {stripeservice} from "../../../../services/stripeservice";
import {billingservice} from "../../../../services/billingservice";
import {mapActions} from "vuex";

export default {
  name: "StripeTokenForm",
  data() {
    return {
      loading: true,
      sending: false,
      success: true,
      errors: {

      },
      card: {
      },
      token: '',
      stripe: {}
    }
  },
  mounted() {

    var imported = document.createElement('script');
    imported.src = 'https://js.stripe.com/v3/';
    document.head.appendChild(imported);

    var that = this
    billingservice.getAddCardToken().then(
      tokenResponse => {
        this.stripe = Stripe(tokenResponse.data.api_info);
        this.loading = false
        setTimeout(function () {

          that.card = stripeservice.getCardToken(that.stripe, tokenResponse.data.token)

        }, 100);
        this.token = tokenResponse.data.token
      },
      error => {
        this.sending = false
      }
  )

  },
  methods: {
    ...mapActions('billingStore', ['cardAdded']),
    send: function () {
      this.sending = true;
      var that = this
      stripeservice.sendCard(this.stripe, this.card).then(
        response => {
          var token = response.token.id;
          billingservice.saveToken(token).then(response => {
            var paymentDetails = response.data.payment_details;
            this.cardAdded({paymentDetails});
            that.sending = false
          })
        }
      )

    }
  }
}
</script>

<style>
.result-message {
  line-height: 22px;
  font-size: 16px;
}

.result-message a {
  color: rgb(89, 111, 214);
  font-weight: 600;
  text-decoration: none;
}

.hidden {
  display: none;
}

#card-error {
  color: rgb(105, 115, 134);
  text-align: left;
  font-size: 13px;
  line-height: 17px;
  margin-top: 12px;
}

#card-element {
  border-radius: 4px 4px 0 0 ;
  padding: 12px;
  border: 1px solid rgba(50, 50, 93, 0.1);
  height: 44px;
  width: 100%;
  background: white;
}

#payment-request-button {
  margin-bottom: 32px;
}

#cardInput {
  padding: 5px;
  border: 1px solid silver;
  border-radius: 0.25rem;
}

</style>
