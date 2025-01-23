<template>
  <div class="card-body">
    <h2 class="section-header">{{ $t('app.subscription_plan.create.features_section.title') }}</h2>

    <div class="mt-4">
      <div class="mb-2">
        <ul class="flex gap-2">
          <li class="p-3 border rounded border-gray-300 hover:bg-gray-100 cursor-pointer" :class="{selectedType: add_type === 'existing'}"  @click="add_type = 'existing'"><a>{{ $t('app.subscription_plan.create.features_section.existing') }}</a></li>
          <li class="p-3 border rounded border-gray-300 hover:bg-gray-100 cursor-pointer" :class="{selectedType: add_type === 'new'}" @click="add_type = 'new'"><a>{{ $t('app.subscription_plan.create.features_section.new') }}</a></li>
        </ul>
      </div>
      <div class="flex" v-if="add_type === 'existing'">
        <div class="w-3/4">
          <select class="form-field" v-model="next_feature">
            <option v-for="featureInfo in features" :value="featureInfo">{{ featureInfo.name }}</option>
          </select>
        </div>
        <div>
          <button @click.prevent="addFeatureToSelected({feature: next_feature})"  class="ml-5 btn--main">{{ $t('app.subscription_plan.create.features_section.add_feature') }}</button>
        </div>
      </div>
      <div  v-if="add_type === 'new'">
        <div>
          <div class="">
            <span class="font-bold block">{{ $t('app.subscription_plan.create.features_section.create.name') }}</span>
            <input type="text" class="form-field" v-model="feature.name" />
            <span class="form-field-error" v-if="errors.name != undefined">{{ $t(errors.name) }}</span>
          </div>

          <div class="">
            <span class="font-bold block">{{ $t('app.subscription_plan.create.features_section.create.code_name') }}</span>
            <input type="text" class="form-field" v-model="feature.code" />
            <span class="form-field-error" v-if="errors.code != undefined">{{ $t(errors.code) }}</span>
          </div>
          <div class="">
            <span class="font-bold block">{{ $t('app.subscription_plan.create.features_section.create.description') }}</span>
            <input type="text" class="form-field" v-model="feature.description" />
            <span class="form-field-error" v-if="errors.description != undefined">{{ $t(errors.description) }}</span>
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
        <th>{{ $t('app.subscription_plan.create.features_section.columns.feature') }}</th>
        <th>{{ $t('app.subscription_plan.create.features_section.columns.description') }}</th>
        <th></th>
      </tr>
      </thead>
      <tbody>
      <tr v-for="(feature, key) in selectedFeatures">
        <td>{{ selectedFeatures[key].name }}</td>
        <td>{{ selectedFeatures[key].description }}</td>
        <td><button  @click="removeFeatureFromSelected({key})" class="btn--danger">
          <i class="fa-solid fa-trash cursor-pointer"></i></button>
        </td>
      </tr>
      <tr v-if="selectedFeatures.length === 0">
        <td colspan="3" class="text-center">{{ $t('app.subscription_plan.create.features_section.no_features') }}</td>
      </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
import {mapActions, mapState} from "vuex";

export default {
  name: "SectionFeatures",
  data() {
    return {
      next_feature: {},
      subscription_plan: {},
      add_type: 'existing',
      feature: {},
    }
  },
  computed: {
    ...mapState('planStore', ['features', 'errors', 'selectedFeatures', 'sendingRequest'])
  },
  methods: {
    ...mapActions('planStore', ['addFeatureToSelected', 'createFeature', "removeFeatureFromSelected"]),
    sendCreate: function() {
      this.createFeature({feature: this.feature}).then(response => {
        this.feature = {};
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
