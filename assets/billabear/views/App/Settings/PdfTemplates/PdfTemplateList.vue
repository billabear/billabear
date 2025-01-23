<template>
  <div>
    <div class="grid grid-cols-2">

      <h1 class="page-title">{{ $t('app.settings.pdf_template.list.title') }}</h1>

      <div class="text-end mt-5">
        <RoleOnlyView role="ROLE_DEVELOPER">
          <router-link :to="{'name': 'app.settings.pdf_template.create'}" class="btn--secondary mr-2">{{ $t('app.settings.pdf_template.list.create_btn') }}</router-link>
          <router-link :to="{'name': 'app.settings.pdf_template.generator'}" class="btn--main">{{ $t('app.settings.pdf_template.list.generator') }}</router-link>
        </RoleOnlyView>
      </div>
    </div>

    <LoadingScreen :ready="ready">
      <div class="rounded-lg bg-white shadow p-3">
        <table class="w-full">
          <thead>
          <tr class="border-b border-black">
            <th class="text-left pb-2">{{ $t('app.settings.pdf_template.list.name') }}</th>
            <th class="text-left pb-2">{{ $t('app.settings.pdf_template.list.brand') }}</th>
            <th class="text-left pb-2">{{ $t('app.settings.pdf_template.list.locale') }}</th>
            <th></th>
          </tr>
          </thead>
          <tbody>
          <tr v-for="template in templates" class="mt-5 cursor-pointer">
            <td class="py-3">{{ template.name }}</td>
            <td class="py-3">{{ template.brand }}</td>
            <td class="py-3">{{ template.locale }}</td>
            <td class="py-3"><router-link :to="{name: 'app.settings.pdf_template.update', params: {id: template.id}}" class="list-btn">{{ $t('app.settings.pdf_template.list.edit_btn') }}</router-link></td>
          </tr>
          <tr v-if="templates.length === 0">
            <td colspan="2" class="text-center">{{ $t('app.settings.pdf_template.list.no_templates') }}</td>
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
  name: "PdfTemplateList",
  data() {
    return {
      ready: false,
      templates: []
    }
  },
  mounted() {
    axios.get('/app/settings/template').then(response => {
      this.templates = response.data.data;
      this.ready = true;
    })
  }
}
</script>

<style scoped>

</style>
