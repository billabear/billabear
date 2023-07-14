<template>
  <div>
    <h1 class="page-title">{{ $t('app.invoices.create.title') }}</h1>

    <div class="card-body">

      <div class="form-field-ctn">
        <label class="form-field-lbl" for="name">
          {{ $t('app.invoices.create.customer.fields.customer') }}
        </label>
        <p class="form-field-error" v-if="errors.customer != undefined">{{ errors.customer }}</p>
        <Autocomplete
            display-key="email"
            search-key="email"
            rest-endpoint="/app/customer"
            v-model="quote.customer"
            :blur-callback="blurCallback" />
        <SubmitButton :in-progress="send_create_customer" @click="createCustomer" v-if="create_customer">{{ $t('app.invoices.create.customer.create_customer') }}</SubmitButton>
        <p class="form-field-help">{{ $t('app.invoices.create.customer.help_info.customer') }}</p>
      </div>
    </div>
  </div>
</template>

<script>
import Autocomplete from "../../../components/app/Forms/Autocomplete.vue";
import axios from "axios";

export default {
  name: "InvoiceCreate",
  components: {Autocomplete},
  data() {
    return {
      errors: {},
      quote: {
        customer: null,
      },
      create_customer: false,
      create_customer_email: "",
      send_create_customer: false,
    }
  },
  watch: {
    'quote.customer': function (){
      if (this.quote.customer !== null) {
        this.create_customer = false;
      }
    }
  },
  methods: {
    selectedCallback: function () {
      this.create_customer = false;
    },
    blurCallback: function (event) {
      this.create_customer_email = event.target.value;

      var validRegex = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/;

      if (!this.create_customer_email.match(validRegex)) {
        return;
      }

      if (!this.quote.customer) {
        this.create_customer = true;
      }
    },
    createCustomer: function () {

      this.send_create_customer = true;
      axios.post("/app/customer", {email: this.create_customer_email}).then(response => {
        this.quote.customer = response.data.id;
        this.create_customer = false;
        this.send_create_customer = false;
      })

    }
  }
}
</script>

<style scoped>

</style>