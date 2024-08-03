<template>
  <div class="card-body">
    <h2 class="section-header">{{ $t('app.subscription_plan.create.prices_section.title') }}</h2>

    <div class="mt-4">
      <div class="mb-2">
        <ul class="flex gap-2">
          <li class="p-3 border rounded border-gray-300 hover:bg-gray-100 cursor-pointer" :class="{selectedType: add_type === 'existing'}"  @click="add_type = 'existing'"><a>{{ $t('app.subscription_plan.create.prices_section.existing') }}</a></li>
          <li class="p-3 border rounded border-gray-300 hover:bg-gray-100 cursor-pointer" :class="{selectedType: add_type === 'new'}" @click="add_type = 'new'"><a>{{ $t('app.subscription_plan.create.prices_section.new') }}</a></li>
        </ul>
      </div>
      <div class="flex" v-if="add_type === 'existing'">
        <div class="w-3/4">
          <select class="form-field" v-model="next_price">
            <option v-for="priceInfo in prices" :value="priceInfo">{{ priceInfo.display_value }} - {{
                priceInfo.schedule
              }}
            </option>
          </select>
        </div>
        <div>
          <button @click.prevent="addPriceToSelected({price: next_price})"  class="ml-5 btn--main">{{ $t('app.subscription_plan.create.prices_section.add_price') }}</button>
        </div>
      </div>
      <div  v-if="add_type === 'new'">
        <div class="">
          <div class="">
            <span class="font-bold block">{{ $t('app.subscription_plan.create.prices_section.create.amount') }}</span>
            <CurrencyInput v-model="price.amount" />
            <span class="form-field-error block" v-if="errors.amount != undefined">{{ $t(errors.amount) }}</span>

          </div>
          <div class="">
            <span class="font-bold block">{{ $t('app.subscription_plan.create.prices_section.create.currency') }}</span>
            <CurrencySelect v-model="price.currency" />
            <span class="form-field-error" v-if="errors.currency != undefined">{{ $t(errors.currency) }}</span>
          </div>
          <div class="">
            <span class="font-bold block">{{ $t('app.subscription_plan.create.prices_section.create.recurring') }}</span>
            <Toggle v-model="price.recurring" />
            <span class="form-field-error" v-if="errors.recurring != undefined">{{ $t(errors.recurring) }}</span>
          </div>
          <div class="">
            <span class="font-bold block">{{ $t('app.subscription_plan.create.prices_section.create.schedule') }}</span>
            <select class="form-field-input" id="name" v-model="price.schedule">
              <option :value="null"> </option>
              <option value="week">{{ $t('app.price.create.schedule.week') }}</option>
              <option value="month">{{ $t('app.price.create.schedule.month') }}</option>
              <option value="year">{{ $t('app.price.create.schedule.year') }}</option>
            </select>
            <span class="form-field-error" v-if="errors.schedule != undefined">{{ $t(errors.schedule) }}</span>
          </div>
          <div class="">
            <span class="font-bold block">{{ $t('app.subscription_plan.create.prices_section.create.including_tax') }}</span>
            <Toggle v-model="price.including_tax" />
            <span class="form-field-error" v-if="errors.includingTax != undefined">{{ $t(errors.includingTax) }}</span>
          </div>
          <div class="">
            <span class="font-bold block">{{ $t('app.subscription_plan.create.prices_section.create.public') }}</span>
            <Toggle v-model="price.public" />
            <span class="form-field-error" v-if="errors.public != undefined">{{ $t(errors.public) }}</span>
          </div>
        </div>
        <div class="mt-3">
          <SubmitButton :in-progress="sendingRequest" @click="sendCreate">{{ $t('app.subscription_plan.create.features_section.create.button') }}</SubmitButton>
        </div>
      </div>
    </div>
  </div>
  <div class="card-body">

    <table class="table w-full">
      <thead>
      <tr>
        <th>{{ $t('app.subscription_plan.create.prices_section.columns.amount') }}</th>
        <th>{{ $t('app.subscription_plan.create.prices_section.columns.schedule') }}</th>
        <th></th>
      </tr>
      </thead>
      <tbody>
      <tr v-for="(feature, key) in selectedPrices">
        <td>{{ selectedPrices[key].display_value }}</td>
        <td>{{ selectedPrices[key].schedule }}</td>
        <td><button  @click="removePriceFromSelected({key})" class="btn--danger">
          <i class="fa-solid fa-trash cursor-pointer"></i></button>
        </td>
      </tr>
      <tr v-if="selectedPrices.length === 0">
        <td colspan="3" class="text-center">{{ $t('app.subscription_plan.create.prices_section.no_prices') }}</td>
      </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
import {mapActions, mapState} from "vuex";
import CurrencyInput from "../../../../components/app/Forms/CurrencyInput.vue";
import CurrencySelect from "../../../../components/app/Forms/CurrencySelect.vue";
import {Toggle} from "flowbite-vue";

export default {
  name: "SectionPrices",
  components: {Toggle, CurrencySelect, CurrencyInput},
  data() {
    return {
      add_type: 'existing',
      next_price: {},
      price: {

      }
    }
  },
  computed: {
    ...mapState('planStore', ['selectedPrices', 'sendingRequest', 'prices', 'errors'])
  },
  methods: {
    ...mapActions('planStore', ['addPriceToSelected', 'removePriceFromSelected', 'createPrice']),
    sendCreate: function() {
      var productId = this.$route.params.productId;
      var price = this.price;
      this.createPrice({productId, price}).then(response => {
        this.price = {};
      })
    }
  }
}
</script>

<style scoped>

.selectedType {
  @apply bg-blue-100;
}
</style>
