<template>
  <div>
    <h1 class="ml-5 mt-5 page-title">{{ $t('app.country.view.title') }}</h1>

    <LoadingScreen :ready="ready">
      <div>
        <div class="card-body">
          <div class="section-body">
            <dl class="detail-list">
              <div>
                <dt>{{ $t('app.country.view.fields.name') }}</dt>
                <dd>{{ country.name }}</dd>
              </div>
              <div>
                <dt>{{ $t('app.country.view.fields.iso_code') }}</dt>
                <dd>{{ country.iso_code }}</dd>
              </div>
              <div>
                <dt>{{ $t('app.country.view.fields.currency') }}</dt>
                <dd>{{ country.currency }}</dd>
              </div>
              <div>
                <dt>{{ $t('app.country.view.fields.threshold') }}</dt>
                <dd>{{ currency(country.threshold) }}</dd>
              </div>
            </dl>
          </div>
        </div>
      </div>
    </LoadingScreen>
  </div>
</template>

<script>
import axios from "axios";
import currency from "currency.js";

export default {
  name: "CountryView",
  data() {
    return {
      ready: false,
      country: {}
    }
  },
  mounted() {
    const id = this.$route.params.id
    axios.get("/app/country/"+id+"/view").then(response => {
      this.country = response.data.country;
      this.ready = true;
    })
  },
  methods: {

    currency: function (value) {
      return currency(value, { fromCents: true });
    },
  }
}
</script>

<style scoped>

</style>