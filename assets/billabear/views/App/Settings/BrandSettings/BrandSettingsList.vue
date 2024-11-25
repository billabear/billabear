<template>
  <div>
    <div class="grid grid-cols-2">
      <h1 class="page-title">{{ $t('app.settings.brand_settings.list.title') }}</h1>
      <div class="top-button-container text-end pt-6">
        <router-link :to="{name: 'app.settings.brand_settings.create'}" class="btn--main ml-4"><i class="fa-solid fa-user-plus"></i> {{ $t('app.settings.brand_settings.list.create_new') }}</router-link>
      </div>

    </div>

    <LoadingScreen :ready="ready">
      <div class="mt-3 rounded-lg bg-white shadow p-3">
        <table class="w-full">
          <thead>
          <tr class="border-b border-black">
            <th class="text-left pb-2">{{ $t('app.settings.brand_settings.list.name') }}</th>
            <th></th>
          </tr>
          </thead>
          <tbody>
          <tr v-for="brand in brands" class="mt-5 cursor-pointer">
            <td  class="py-3">{{ brand.name }}</td>
            <td class="py-3"><router-link :to="{name: 'app.settings.brand_settings.update', params: {id: brand.id}}" class="list-btn">{{ $t('app.settings.brand_settings.list.edit_btn') }}</router-link></td>
          </tr>
          <tr v-if="brands.length === 0">
            <td colspan="2" class="py-3 text-center">{{ $t('app.settings.brand_settings.list.no_brands') }}</td>
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
