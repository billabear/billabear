<template>
  <div>
    <h1 class="page-title">{{ $t('app.settings.pdf_template.list.title') }}</h1>

    <RoleOnlyView role="ROLE_DEVELOPER">
      <div class="text-end">
        <router-link :to="{'name': 'app.settings.pdf_template.generator'}" class="btn--main">{{ $t('app.settings.pdf_template.list.generator') }}</router-link>
      </div>
    </RoleOnlyView>

    <LoadingScreen :ready="ready">
      <div class="mt-3">
        <table class="list-table">
          <thead>
          <tr>
            <th>{{ $t('app.settings.pdf_template.list.name') }}</th>
            <th></th>
          </tr>
          </thead>
          <tbody>
          <tr v-for="template in templates" class="mt-5 cursor-pointer">
            <td>{{ template.name }}</td>
            <td><router-link :to="{name: 'app.settings.pdf_template.update', params: {id: template.id}}" class="list-btn">{{ $t('app.settings.pdf_template.list.edit_btn') }}</router-link></td>
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