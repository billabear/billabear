<template>
  <div>
    <h1 class="page-title">{{ $t('app.settings.brand_settings.list.title') }}</h1>

    <LoadingScreen :ready="ready">
      <div class="top-button-container">
        <router-link :to="{name: 'app.settings.brand_settings.create'}" class="btn--main ml-4"><i class="fa-solid fa-user-plus"></i> {{ $t('app.settings.brand_settings.list.create_new') }}</router-link>
      </div>
      <div class="mt-3">
        <table class="list-table">
          <thead>
          <tr>
            <th>{{ $t('app.settings.brand_settings.list.name') }}</th>
            <th></th>
          </tr>
          </thead>
          <tbody>
          <tr v-for="brand in brands" class="mt-5 cursor-pointer">
            <td>{{ brand.name }}</td>
            <td><router-link :to="{name: 'app.settings.brand_settings.update', params: {id: brand.id}}" class="list-btn">{{ $t('app.settings.brand_settings.list.edit_btn') }}</router-link></td>
          </tr>
          <tr v-if="brands.length === 0">
            <td colspan="2" class="text-center">{{ $t('app.settings.brand_settings.list.no_brands') }}</td>
          </tr>
          </tbody>
        </table>
      </div>
    </LoadingScreen>
  </div>
</template>

<script>
import axios from "axios";

export default {
  name: "BrandSettingsList",
  data() {
    return {
      ready: false,
      brands: []
    }
  },
  mounted() {
    axios.get('/app/settings/brand').then(response => {
      this.brands = response.data.data;
      this.ready = true;
    })
  }
}
</script>

<style scoped>

</style>