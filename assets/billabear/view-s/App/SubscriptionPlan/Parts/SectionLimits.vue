<template>
  <div class="card-body">
    <h2 class="section-header">
      {{ $t('app.subscription_plan.create.limits_section.title') }}
    </h2>
    <div class="grid grid-cols-3 gap-3 w-full">
      <div>
        {{ $t('app.subscription_plan.create.limits_section.fields.limit') }}
      </div>
      <div>
        {{ $t('app.subscription_plan.create.limits_section.fields.feature') }}
      </div>
      <div></div>
      <div>
        <input
          type="number"
          class="form-field w-28"
          size="3"
          v-model="next_limit.limit"
        />
      </div>
      <div>
        <select class="form-field" v-model="next_limit.feature">
          <option v-for="featureInfo in features" :value="featureInfo">
            {{ featureInfo.name }}
          </option>
        </select>
      </div>
      <div class="flex-none">
        <button type="button" class="btn--main" @click="handleAdd">
          {{ $t('app.subscription_plan.create.limits_section.add_limit') }}
        </button>
      </div>
    </div>
  </div>
  <div class="card-body">
    <table class="table w-full">
      <thead>
        <tr>
          <th>
            {{
              $t('app.subscription_plan.create.limits_section.columns.limit')
            }}
          </th>
          <th>
            {{
              $t('app.subscription_plan.create.limits_section.columns.feature')
            }}
          </th>
          <th>
            {{
              $t(
                'app.subscription_plan.create.limits_section.columns.description'
              )
            }}
          </th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(feature, key) in selectedLimits">
          <td>{{ selectedLimits[key].limit }}</td>
          <td>{{ selectedLimits[key].feature.name }}</td>
          <td>{{ selectedLimits[key].feature.description }}</td>
          <td>
            <button
              @click="removeLimitFromSelected({ key })"
              class="btn--danger"
            >
              <i class="fa-solid fa-trash cursor-pointer"></i>
            </button>
          </td>
        </tr>
        <tr v-if="selectedLimits.length === 0">
          <td colspan="4" class="text-center">
            {{ $t('app.subscription_plan.create.limits_section.no_limits') }}
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
import { mapActions, mapState } from 'vuex'

export default {
  name: 'SectionLimits',
  data() {
    return {
      next_limit: {},
    }
  },
  computed: {
    ...mapState('planStore', ['sendingRequest', 'features', 'selectedLimits']),
  },
  methods: {
    ...mapActions('planStore', [
      'removeLimitFromSelected',
      'addLimitToSelected',
    ]),
    handleAdd: function () {
      this.addLimitToSelected({ limit: this.next_limit })
      this.next_limit = {}
    },
  },
}
</script>

<style scoped></style>
