<template>
  <div>
    <h1 class="page-title mt-5 ml-5">{{ $t('app.quotes.create.title') }}</h1>

    <div class="p-5">
      <div class="card-body">

        <div class="form-field-ctn">
          <label class="form-field-lbl" for="name">
            {{ $t('app.quotes.create.customer.fields.customer') }}
          </label>
          <p class="form-field-error" v-if="errors.customer != undefined">{{ errors.customer }}</p>
          <Autocomplete
              display-key="email"
              search-key="email"
              rest-endpoint="/app/customer"
              v-model="quote.customer"
              :blur-callback="blurCallback" />
          <SubmitButton :in-progress="send_create_customer" @click="createCustomer" v-if="create_customer">{{ $t('app.invoices.create.customer.create_customer') }}</SubmitButton>
          <p class="form-field-help">{{ $t('app.quotes.create.customer.help_info.customer') }}</p>
        </div>

        <div class="form-field-ctn">
          <label class="form-field-lbl" for="name">
            {{ $t('app.quotes.create.customer.fields.currency') }}
          </label>
          <p class="form-field-error" v-if="errors.currency != undefined">{{ errors.currency }}</p>
          <CurrencySelect v-model="quote.currency" />
          <p class="form-field-help">{{ $t('app.quotes.create.customer.help_info.currency') }}</p>
        </div>
        <div class="form-field-ctn">
          <label class="form-field-lbl" for="name">
            {{ $t('app.quotes.create.customer.fields.expires_at') }}
          </label>
          <p class="form-field-error" v-if="errors.expiresAt != undefined">{{ errors.expiresAt }}</p>
          <VueDatePicker  class="mt-2" v-model="quote.expires_at" :enable-time-picker="true"></VueDatePicker>
          <p class="form-field-help">{{ $t('app.quotes.create.customer.help_info.expires_at') }}</p>
        </div>
      </div>

      <div class="card-body mt-5">
        <div class="grid grid-cols-2">
          <div><h2 class="mb-3">{{ $t('app.quotes.create.subscriptions.title') }}</h2></div>
          <div class="text-right"><button class="btn--main" @click="addSubscriptionPlan">{{ $t('app.quotes.create.subscriptions.add_subscription') }}</button></div>
        </div>
        <table class="list-table">
          <thead>
          <tr>
            <th>{{ $t('app.quotes.create.subscriptions.list.subscription_plan') }}</th>
            <th>{{ $t('app.quotes.create.subscriptions.list.per_seat')}}</th>
            <th>{{ $t('app.quotes.create.subscriptions.list.price')}}</th>
            <th></th>
          </tr>
          </thead>
          <tbody v-if="quote.subscription_plans.length === 0">
          <tr>
            <td colspan="3" class="text-center">{{ $t('app.quotes.create.subscriptions.no_subscriptions') }}</td>
          </tr>
          </tbody>
          <tbody v-else>
          <tr v-for="(plan, key) in quote.subscription_plans">
            <td>
              <select class="form-field" v-model="plan.plan">
                <option v-for="subscriptionPlan in this.plans" :value="subscriptionPlan">{{ subscriptionPlan.name }}</option>
              </select>
            </td>
            <td>
              <input type="number" class="form-field" :disabled="plan.plan.per_seat === undefined ||  plan.plan.per_seat !== true" v-model="plan.seat_number" />
            </td>
            <td>

              <select class="form-field" v-model="plan.price">
                <option v-for="price in getPrices(plan.plan.prices)" :value="price">{{ displayCurrency(price.amount) }}/{{ price.schedule }}</option>
              </select>
            </td>
            <td><button class="btn--danger" @click="deleteSubscription(key)"><i class="fa-solid fa-trash"></i></button> </td>
          </tr>
          </tbody>
        </table>
      </div>

      <div class="card-body mt-5">
        <div class="grid grid-cols-2">
          <div><h2 class="mb-3">{{ $t('app.quotes.create.items.title') }}</h2></div>
          <div class="text-right"><button class="btn--main" @click="addItem">{{ $t('app.quotes.create.items.add_item') }}</button></div>
        </div>

        <table class="list-table">
          <thead>
          <tr>
            <th>{{ $t('app.quotes.create.items.list.description') }}</th>
            <th>{{ $t('app.quotes.create.items.list.amount') }}</th>
            <th>{{ $t('app.quotes.create.items.list.tax_included') }}</th>
            <th>{{ $t('app.quotes.create.items.list.tax_type') }}</th>
            <th></th>
          </tr>
          </thead>
          <tbody v-if="quote.items.length === 0">
          <tr>
            <td colspan="5" class="text-center">{{ $t('app.quotes.create.items.no_items') }}</td>
          </tr>
          </tbody>
          <tbody v-else>
          <tr v-for="(item, key) in quote.items">
            <td>

              <p class="form-field-error" v-if="errors.items != undefined && errors.items[key] !== undefined && errors.items[key].description !== undefined">{{ errors.items[key].description }}</p>
              <input type="text" class="form-field" v-model="item.description" />
            </td>
            <td>
              <p class="form-field-error" v-if="errors.items != undefined && errors.items[key] !== undefined && errors.items[key].amount !== undefined">{{ errors.items[key].amount }}</p>
              <input type="number" class="form-field" v-model="item.amount" >
            </td>
            <td><input type="checkbox" class="form-field" v-model="item.tax_included" /></td>
            <td>
              <p class="form-field-error" v-if="errors.items != undefined && errors.items[key] !== undefined && errors.items[key].taxType != undefined">{{ errors.items[key].taxType }}</p>
              <select class="form-field" id="name" v-model="item.tax_type">
                <option value="digital_goods">{{ $t('app.quotes.create.items.tax_types.digital_goods') }}</option>
                <option value="digital_services">{{ $t('app.quotes.create.items.tax_types.digital_services') }}</option>
                <option value="physical">{{ $t('app.quotes.create.items.tax_types.physical') }}</option>
              </select>
            </td>
            <td><button class="btn--danger" @click="deleteItem(key)"><i class="fa-solid fa-trash"></i></button> </td>
          </tr>
          </tbody>
        </table>
      </div>

      <div class="alert-error mt-5" v-if="errors.main_error">
        {{ errors.main_error }}
      </div>

      <div class="mt-5">
        <SubmitButton :in-progress="send_quote" class="btn--main" @click="createInvoice('quotes')">{{ $t('app.quotes.create.create_quote') }}</SubmitButton>
      </div>
      <div class="mt-1" v-if="success">
        {{ $t('app.quotes.create.success_message') }}
      </div>
    </div>

  </div>
</template>

<script>
import Autocomplete from "../../../components/app/Forms/Autocomplete.vue";
import axios from "axios";
import currency from "currency.js";
import CurrencySelect from "../../../components/app/Forms/CurrencySelect.vue";

export default {
  name: "QuoteCreate",
  components: {CurrencySelect, Autocomplete},
  data() {
    return {
      errors: {items: []},
      quote: {
        customer: null,
        subscription_plans: [],
        items: [],
        currency: null,
        expires_at: null,
      },
      send_quote: false,
      create_customer: false,
      create_customer_email: "",
      send_create_customer: false,
      plans: [],
      success: false,
    }
  },
  mounted() {
    axios.get("/app/quotes/create").then(response => {
      this.plans = response.data.subscription_plans;
    })
  },
  watch: {
    'quote.customer': function (){
      if (this.quote.customer !== null) {
        this.create_customer = false;
      }
    }
  },
  methods: {
    getPrices: function (prices) {
      var that = this;
      return prices.filter(item => item.currency === that.quote.currency);
    },
    addSubscriptionPlan: function (){
      this.quote.subscription_plans.push({
        plan: {prices: []},
        price: null,
        seat_number: null,
      })
    },
    deleteSubscription: function (key) {
      this.quote.subscription_plans.splice(key, 1);
    },
    addItem: function () {
      this.quote.items.push({
        description: null,
        amount: null,
        include_tax: false,
      })
    },
    deleteItem: function (key) {
      this.quote.items.splice(key, 1);
    },
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
      axios.post("/app/customer", {email: this.create_customer_email, billing_type: 'invoice'}).then(response => {
        this.quote.customer = response.data.id;
        this.create_customer = false;
        this.send_create_customer = false;
      })

    },
    displayCurrency: function (value) {
      return currency(value, { fromCents: true }).format({symbol: ''});
    },
    createInvoice: function (type = 'invoices') {
      this.errors = {};
      this.send_quote = true;
      if (!this.quote.customer) {
         this.errors.customer = this.$t('app.quotes.create.errors.no_customer')
      }

      if (this.quote.subscription_plans.length === 0 && this.quote.items.length === 0) {
        this.errors.main_error = this.$t('app.quotes.create.errors.nothing_to_invoice');
      }

      if (!this.quote.currency) {
        this.errors.currency = this.$t('app.quotes.create.errors.currency');
      }
      var subscriptions = [];
      var sameCurrency = true;
      var sameSchedule = true;
      var lastCurrency = null;
      var lastSchedule = null;
      for (var key in this.quote.subscription_plans) {
        var plan = this.quote.subscription_plans[key];
        if (lastCurrency === null) {
          lastCurrency = plan.price.currency;
        }

        if (lastSchedule === null) {
          lastSchedule = plan.price.schedule;
        }

        if (lastSchedule !== plan.price.schedule) {
          sameSchedule = false;
        }

        if (lastCurrency !== plan.price.currency) {
          sameCurrency = false;
        }

        subscriptions.push({
          plan: plan.plan.id,
          price: plan.price.id,
          seat_number: plan.seat_number,
        })
      }

      if (!sameSchedule || !sameCurrency) {
        this.errors.main_error = this.$t('app.quotes.create.errors.same_currency_and_schedule');
      }

      var items = [];
      var errors =  [];
      var hasErrors = false;
      for (var key in this.quote.items) {
        var item = this.quote.items[key];
        errors[key] = {};
        if (!item.description) {
          hasErrors = true;
          errors[key].description = this.$t('app.quotes.create.errors.need_description');
        }

        if (!item.amount) {
          hasErrors = true;
          errors[key].amount = this.$t('app.quotes.create.errors.need_amount');
        }
        if (!item.tax_type) {
          hasErrors = true;
          errors[key].taxType = this.$t('app.quotes.create.errors.need_tax_type');
        }
        items.push(
            {
              description: item.description,
              amount: item.amount,
              currency: this.quote.currency,
              include_tax: item.include_tax,
              tax_type: item.tax_type,
            }
        )
      }

      if (hasErrors) {
        this.errors.items = errors;
      }

      if (Object.keys(this.errors).length > 0) {
        this.send_quote = false;
        return;
      }

      const payload = {
        customer: this.quote.customer,
        subscriptions: subscriptions,
        items: items,
        expires_at: this.quote.expires_at,
      }

      axios.post("/app/"+type+"/create", payload).then(response => {
        this.send_quote = false;
        this.success = true;
        this.$router.push({'name': 'app.'+type+'.view', params: {id: response.data.id}})
      }).catch(error => {
        this.errors = error.response.data.errors;
        this.send_quote = false;
      })
    },
  }
}
</script>

<style scoped>

</style>