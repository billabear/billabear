<template>
  <div class="rounded-lg bg-white shadow p-3">
    <table class="w-full">
      <thead>
        <tr class="border-b border-black">
          <th class="text-left pb-2">{{ $t('app.customer.list.email') }}</th>
          <th class="text-left pb-2">{{ $t('app.customer.list.company_name') }}</th>
          <th class="text-left pb-2">{{ $t('app.customer.list.country') }}</th>
          <th class="text-left pb-2">{{ $t('app.customer.list.reference') }}</th>
          <th></th>
        </tr>
      </thead>
      <tbody v-if="loaded">
        <tr 
          v-for="customer in customers" 
          :key="customer.id"
          class="cursor-pointer hover:bg-gray-50" 
          @click="$router.push({name: 'app.customer.view', params: {id: customer.id}})"
        >
          <td class="py-3">{{ customer.email }}</td>
          <td class="py-3">{{ customer.address.company_name }}</td>
          <td class="py-3">{{ customer.address.country }}</td>
          <td class="py-3">{{ customer.reference }}</td>
          <td class="py-3">
            <router-link 
              :to="{name: 'app.customer.view', params: {id: customer.id}}" 
              class="rounded-lg w-full p-2 bg-teal-500 text-white font-bold"
            >
              {{ $t('app.customer.list.view_btn') }}
            </router-link>
          </td>
        </tr>
        <tr v-if="customers.length === 0">
          <td colspan="5" class="text-center">{{ $t('app.customer.list.no_customers') }}</td>
        </tr>
      </tbody>
      <tbody v-else>
        <tr v-for="n in 5" :key="`loading-${n}`">
          <td colspan="5" class="py-3 text-center">
            <LoadingMessage>{{ $t('app.customer.list.loading') }}</LoadingMessage>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
import LoadingMessage from "../../LoadingMessage.vue";

export default {
  name: "CustomerTable",
  components: {
    LoadingMessage
  },
  props: {
    customers: {
      type: Array,
      required: true,
      default: () => []
    },
    loaded: {
      type: Boolean,
      required: true,
      default: false
    }
  }
}
</script>