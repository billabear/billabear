<script>
export default {
  name: "LogList",
  props: {
    logs: {
      type: Array,
      required: true
    }
  },
  methods: {
    format(data) {
      return JSON.stringify(data, null, 2);
    }
  }
}
</script>

<template>

  <div class="card-body">
    <div class="section-header grid grid-cols-2 font-bold border-b-2 border-black">
      <div class="text-left">
        {{ $t('app.compliance.audit.all.log') }}
      </div>
      <div class="text-end">
        {{ $t('app.compliance.audit.all.date') }}
      </div>
    </div>

    <div v-for="log in logs">
      <div class="flex hover:bg-gray-100 hover:cursor-pointer"  @click="log.show = !log.show">
        <div class="w-5"><i class="fa-solid fa-chevron-right transition duration-300" :class="{'rotate-90': log.show}"></i></div>
        <div class="grow">{{log.message}}</div>
        <div class="text-end">
          {{ $filters.moment(log.created_at, "LLL") }}
        </div>
      </div>
      <div class="p-2" v-if="log.show">
        <div class="grid grid-cols-2 gap-4">
          <div class="border border-black rounded-lg p-3 overflow-auto">
            <h3 class="text-xl mb-3">
              {{ $t('app.compliance.audit.all.context') }}
            </h3>

            <pre>{{ format(log.context) }}</pre>
          </div>
          <div class="border border-black rounded-lg p-3 overflow-auto">
            <h3 class="text-xl  mb-3">
              {{ $t('app.compliance.audit.all.billing_admin') }}
            </h3>
            <dl v-if="log.billing_admin">
              <div class="grid grid-cols-5">
                <dd class="font-bold">{{ $t('app.compliance.audit.all.display_name') }}</dd>
                <dt><router-link :to="{name: 'app.settings.users.update', params: {id: log.billing_admin.id}}" class="text-blue-500 no-underline hover:underline">{{ log.billing_admin.display_name }}</router-link></dt>
              </div>
            </dl>
            <div class="text-center text-gray-400 italic" v-else>
              {{ $t('app.compliance.audit.all.no_billing_admin') }}
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

</template>

<style scoped>

</style>