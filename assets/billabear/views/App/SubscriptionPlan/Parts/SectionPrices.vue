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
            <option v-for="priceInfo in prices" :value="priceInfo">{{ priceInfo.display_value }}</option>
          </select>
        </div>
        <div>
          <button @click.prevent="addPriceToSelected({price: next_price})"  class="ml-5 btn--main">{{ $t('app.subscription_plan.create.prices_section.add_price') }}</button>
        </div>
      </div>
      <div  v-if="add_type === 'new'">
        <div class="">

          <div class="form-field-ctn">
            <label class="form-field-lbl" for="amount">
              {{ $t('app.price.create.type') }}
            </label>

            <p class="form-field-error" v-if="errors.type != undefined">{{ errors.type }}</p>
            <select class="form-field" v-model="price.type">
              <option value="fixed_price">{{ $t('app.price.create.types.fixed_price') }}</option>
              <option value="package">{{ $t('app.price.create.types.package') }}</option>
              <option value="per_unit">{{ $t('app.price.create.types.per_unit') }}</option>
              <option value="tiered_volume">{{ $t('app.price.create.types.tiered_volume') }}</option>
              <option value="tiered_graduated">{{ $t('app.price.create.types.tiered_graduated') }}</option>
            </select>
          </div>

          <div class="" v-if="showAmount">
            <span class="font-bold block">{{ $t('app.subscription_plan.create.prices_section.create.amount') }}</span>
            <CurrencyInput v-model="price.amount" />
            <span class="form-field-error block" v-if="errors.amount != undefined">{{ $t(errors.amount) }}</span>

          </div>


          <div class="form-field-ctn" v-if="showUnits">
            <label class="form-field-lbl" for="units">
              {{ $t('app.price.create.units') }}
            </label>
            <p class="form-field-error" v-if="errors.units != undefined">{{ errors.units }}</p>
            <input type="number" v-model="price.units" class="form-field-input" />
          </div>

          <div class="my-3" v-if="showTiers">
            <h3 class="text-xl">
              {{ $t('app.price.create.tiers') }}
            </h3>

            <table class="gap-2">
              <thead>
              <tr>
                <th>{{ $t('app.price.create.tiers_fields.first_unit') }}</th>
                <th>{{ $t('app.price.create.tiers_fields.last_unit') }}</th>
                <th>{{ $t('app.price.create.tiers_fields.unit_price') }}</th>
                <th>{{ $t('app.price.create.tiers_fields.flat_fee') }}</th>
              </tr>
              </thead>
              <tbody>
              <tr v-for="(tier, key) in tiers">
                <td>
                  <input disabled  v-model="tier.first_unit" class="w-24 form-field-input" />
                </td>
                <td>
                  <p class="form-field-error" v-if="errors.tiers != undefined && errors.tiers[key].last_unit != undefined">{{ errors.tiers[key].last_unit }}</p>

                  <input v-model="tier.last_unit" class="w-24 form-field-input" type="number" v-if="key !== (tiers.length-1)" />
                  <input disabled class="form-field-input w-24" value="∞" v-else>
                </td>
                <td>
                  <p class="form-field-error" v-if="errors.tiers != undefined && errors.tiers[key].unit_price != undefined">{{ errors.tiers[key].unit_price }}</p>

                  <CurrencyInput class="w-24" v-model="tier.unit_price" />
                </td>
                <td>
                  <p class="form-field-error" v-if="errors.tiers != undefined && errors.tiers[key].flat_fee != undefined">{{ errors.tiers[key].flat_fee }}</p>

                  <CurrencyInput class="w-24" v-model="tier.flat_fee" />
                </td>
              </tr>
              </tbody>
            </table>

            <button class="btn--main" @click="addTier">Add</button>
          </div>

          <div class="">
            <span class="font-bold block">{{ $t('app.subscription_plan.create.prices_section.create.currency') }}</span>
            <CurrencySelect v-model="price.currency" />
            <span class="form-field-error" v-if="errors.currency != undefined">{{ $t(errors.currency) }}</span>
          </div>

          <div class="form-field-ctn" v-if="showUsage">
            <label class="form-field-lbl" for="usage" >
              {{ $t('app.price.create.usage') }}
            </label>
            <p class="form-field-error" v-if="errors.usage != undefined">{{ errors.usage }}</p>
            <Toggle v-model="price.usage" />
            <p class="form-field-help">{{ $t('app.price.create.help_info.usage') }}</p>
          </div>

          <div class="form-field-ctn" v-if="price.usage">
            <label class="form-field-lbl">{{ $t('app.price.create.metric') }}</label>
            <p class="form-field-error" v-if="errors.metric != undefined">{{ errors.metric }}</p>
            <select class="form-field" v-model="price.metric" v-if="metrics.length > 0">
              <option v-for="metric in metrics" :value="metric.id">{{ metric.name }}</option>
            </select>
            <router-link :to="{name: 'app.metric.create'}" v-else>{{ $t('app.price.create.create_metric') }}</router-link>
          </div>

          <div class="form-field-ctn" v-if="price.usage">
            <label class="form-field-lbl" for="name">
              {{ $t('app.price.create.metric_type') }}
            </label>
            <p class="form-field-error" v-if="errors.metric_type != undefined">{{ errors.metric_type }}</p>
            <select class="form-field" v-model="price.metric_type">
              <option value="resettable">{{ $t('app.price.create.metric_types.resettable') }}</option>
              <option value="continuous">{{ $t('app.price.create.metric_types.continuous') }}</option>
            </select>
            <p class="form-field-help">{{ $t('app.price.create.help_info.metric_type') }}</p>
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
        amount: 0,
        currency: 'EUR',
        recurring: true,
        schedule: null,
        external_reference: null,
        including_tax: false,
        public: true,
        type: 'fixed_price',
        usage: false,
        units: null,
      },
      errors: {},
      rawTiers: [
        {first_unit: 1, last_unit: 1, unit_price: 0, flat_fee: 0}
      ],
    }
  },
  computed: {
    ...mapState('planStore', ['selectedPrices', 'sendingRequest', 'prices', 'metrics', 'errors']),

    showAmount: function() {
      return !(this.price.type === 'tiered_volume' || this.price.type === 'tiered_graduated')
    },
    showTiers: function() {
      return (this.price.type === 'tiered_volume' || this.price.type === 'tiered_graduated')
    },
    showUnits: function () {
      return (this.price.type === 'package')
    },
    showUsage: function () {
      return (this.price.type !== 'fixed_price')
    },
    tiers: function () {
      const output = [];
      let lastUnit = 0;

      this.rawTiers.forEach( tier => {
        tier.first_unit = lastUnit + 1;
        output.push(tier);
        lastUnit = tier.last_unit;
      });

      output.push({first_unit: lastUnit+1, last_unit: null, unit_price: 0, flat_fee: 0});
      return output;
    }
  },
  methods: {
    ...mapActions('planStore', ['addPriceToSelected', 'removePriceFromSelected', 'createPrice']),
    sendCreate: function() {
      var productId = this.$route.params.productId;
      var price = this.price;

      if (this.showTiers) {
        price.tiers = this.rawTiers;
        price.amount = null;
      }

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
