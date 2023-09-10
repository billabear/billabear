<template>
  <div class="mt-5">
    <div class="w-full mb-5 mt-5">
      <img src="/images/app-logo.png" alt="" class="" width="175" />
    </div>
    <div class="grid grid-cols-2 gap-4">

      <div class="mt-5 rounded-xl basket-container">
        <h2 class="text-4xl mb-5">{{ displayCurrency(checkout.total) }} {{ checkout.currency }}</h2>

        <h3 class="text-xl mb-2">{{ $t('portal.checkout.items.title') }}</h3>
        <div v-for="line in checkout.lines" class="item-line">
          <p>{{ displayCurrency(line.total) }} {{ line.currency }} - {{ line.description }}</p>
        </div>

      </div>
      <div class="mt-5">
        <div v-if="stage === 'customer'">
          <h2 class="text-xl mb-5">{{ $t('portal.checkout.customer.title') }}</h2>
          <div class="form-field-ctn">
            <label class="form-field-lbl" for="email">
              {{ $t('portal.checkout.customer.fields.email') }}
            </label>
            <p class="form-field-error" v-if="errors.email != undefined">{{ errors.email }}</p>
            <input type="email" class="form-field-input" id="email" v-model="customer.email" />
          </div>


          <div class="form-field-ctn">
            <label class="form-field-lbl" for="street_line_one">
              {{ $t('portal.checkout.customer.fields.street_line_one') }}
            </label>
            <p class="form-field-error" v-if="errors['address.street_line_one'] != undefined">{{ errors['address.street_line_one'] }}</p>
            <input type="text" class="form-field-input" id="street_line_one"  v-model="customer.address.street_line_one"  />
          </div>

          <div class="form-field-ctn">
            <label class="form-field-lbl" for="street_line_two">
              {{ $t('portal.checkout.customer.fields.street_line_two') }}
            </label>
            <p class="form-field-error" v-if="errors['address.street_line_two'] != undefined">{{ errors['address.street_line_two'] }}</p>
            <input type="text" class="form-field-input" id="street_line_two"  v-model="customer.address.street_line_two"  />
          </div>

          <div class="form-field-ctn">
            <label class="form-field-lbl" for="city">
              {{ $t('portal.checkout.customer.fields.city') }}
            </label>
            <p class="form-field-error" v-if="errors['address.city'] != undefined">{{ errors['address.city'] }}</p>
            <input type="text" class="form-field-input" id="city"  v-model="customer.address.city"  />
          </div>

          <div class="form-field-ctn">
            <label class="form-field-lbl" for="region">
              {{ $t('portal.checkout.customer.fields.region') }}
            </label>
            <p class="form-field-error" v-if="errors['address.region'] != undefined">{{ errors['address.region'] }}</p>
            <input type="text" class="form-field-input" id="region"  v-model="customer.address.region"  />
          </div>

          <div class="form-field-ctn">
            <label class="form-field-lbl" for="country">
              {{ $t('portal.checkout.customer.fields.country') }}
            </label>
            <p class="form-field-error" v-if="errors['address.country'] != undefined">{{ errors['address.country'] }}</p>
            <input type="text" class="form-field-input" id="country"  v-model="customer.address.country"  />
          </div>
          <div class="form-field-ctn">
            <label class="form-field-lbl" for="post_code">
              {{ $t('portal.checkout.customer.fields.post_code') }}
            </label>
            <p class="form-field-error" v-if="errors['address.postcode'] != undefined">{{ errors['address.postcode'] }}</p>
            <input type="text" class="form-field-input" id="post_code"  v-model="customer.address.postcode"  />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import {stripeservice} from "../../../app/services/stripeservice";
import currency from "currency.js";

export default {
  name: "CheckoutView",
  data() {
    return {
      stage: 'customer',
      checkout: {},
      customer: {address: {}},
      ready: false,
      errors: {}
    }
  },
  mounted() {
    const slug = this.$route.params.slug;

    axios.get("/public/checkout/"+slug+"/view").then(response => {
      this.checkout = response.data.checkout;
    }).catch(error => {
      if (error.response !== undefined && error.response.status === 404) {
        this.not_found = true;
        return;
      }
      this.general_error = true;
    })
  },
  methods: {
    displayCurrency: function (value) {
      return currency(value, { fromCents: true }).format({symbol: ''});
    },
    createCustomer: function () {

      var imported = document.createElement('script');
      imported.src = 'https://js.stripe.com/v3/';
      document.head.appendChild(imported);

      axios.post("/public/checkout/"+slug+"/customer").then(response => {

        this.stripeConfig = response.data.stripe;
        this.ready = true;
        this.stripe = Stripe(this.stripeConfig.key);
      })
    }
  }
}
</script>

<style scoped>
.basket-container {
  @apply p-5;
  background: #ffe6bf;
}

.form-field-ctn {
  @apply mb-1;
}

.form-field-lbl {
  @apply block;
}

.form-field-input {
  @apply p-2 border rounded-xl;
}
</style>