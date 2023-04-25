<template>
  <div>
    <h1 class="page-title">{{ $t('app.settings.template.list.title') }}</h1>

    <LoadingScreen :ready="ready">
      <div class="mt-3">
        <table class="list-table">
          <thead>
          <tr>
            <th>{{ $t('app.settings.template.list.name') }}</th>
            <th></th>
          </tr>
          </thead>
          <tbody>
          <tr v-for="template in templates" class="mt-5 cursor-pointer">
            <td>{{ template.name }}</td>
            <td><router-link :to="{name: 'app.settings.template.update', params: {id: template.id}}" class="list-btn">{{ $t('app.settings.template.list.edit_btn') }}</router-link></td>
          </tr>
          <tr v-if="templates.length === 0">
            <td colspan="2" class="text-center">{{ $t('app.settings.template.list.no_templates') }}</td>
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
  name: "TemplateList",
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