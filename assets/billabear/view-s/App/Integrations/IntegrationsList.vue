<template>
  <div>
    <h1 class="ml-5 mt-5 page-title">
      {{ $t('app.system.integrations.list.title') }}
    </h1>

    <div class="rounded-lg bg-white shadow p-3">
      <table class="w-full">
        <thead>
          <tr class="border-b border-black">
            <th class="text-left pb-2">
              {{ $t('app.system.integrations.list.list.name') }}
            </th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td class="py-3">
              {{ $t('app.system.integrations.list.slack.name') }}
            </td>
            <th>
              <router-link
                class="btn--main"
                :to="{ name: 'app.system.integrations.slack.webhook' }"
                >{{
                  $t('app.system.integrations.list.slack.button')
                }}</router-link
              >
            </th>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script>
export default {
  name: 'IntegrationsList',
}
</script>

<style scoped></style>
