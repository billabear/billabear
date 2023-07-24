<template>
  <div>
    <h1 class="page-title mb-5">{{ $t('app.quotes.view.title') }}</h1>

    <LoadingScreen :ready="ready">
      <div class="grid grid-cols-2 gap-4">
        <div>

          <h2 class="section-header">{{ $t('app.quotes.view.customer.title') }}</h2>
          <div class="section-body">


            <dl class="detail-list">
              <div>
                <dt>{{ $t('app.quotes.view.customer.email') }}</dt>
                <dd>{{ quote.customer.email }}</dd>
              </div>
              <div>
                <dt>{{ $t('app.quotes.view.customer.address.street_line_one') }}</dt>
                <dd>{{ quote.customer.address.street_line_one }}</dd>
              </div>
              <div>
                <dt>{{ $t('app.quotes.view.customer.address.street_line_two') }}</dt>
                <dd>{{ quote.customer.address.street_line_two }}</dd>
              </div>
              <div>
                <dt>{{ $t('app.quotes.view.customer.address.city') }}</dt>
                <dd>{{ quote.customer.address.city }}</dd>
              </div>
              <div>
                <dt>{{ $t('app.quotes.view.customer.address.region') }}</dt>
                <dd>{{ quote.customer.address.region }}</dd>
              </div>
              <div>
                <dt>{{ $t('app.quotes.view.customer.address.country') }}</dt>
                <dd>{{ quote.customer.address.country }}</dd>
              </div>
              <div>
                <dt>{{ $t('app.quotes.view.customer.address.post_code') }}</dt>
                <dd>{{ quote.customer.address.post_code }}</dd>
              </div>
            </dl>
          </div>
        </div>
        <div class="card-body">
          sds
        </div>
      </div>
    </LoadingScreen>
  </div>
</template>

<script>
import axios from "axios";

export default {
  name: "QuotesRead",
  data() {
    return {
      quote: {},
      ready: false,
    }
  },
  mounted() {
    const id = this.$route.params.id
    axios.get("/app/quotes/"+id+"/view").then(response => {
      this.quote = response.data.quote;
      this.ready = true;
    })
  }
}
</script>

<style scoped>

</style>