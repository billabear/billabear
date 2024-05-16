<template>
  <div>

    <h1 class="ml-5 mt-5 page-title">{{ $t('app.system.integrations.slack.title') }}</h1>

    <a href="https://slack.com/oauth/v2/authorize?scope=incoming-webhook&client_id=5545930927968.7112179146215"><img alt="Add to Slack" height="40" width="139" src="https://platform.slack-edge.com/img/add_to_slack.png" srcset="https://platform.slack-edge.com/img/add_to_slack.png 1x, https://platform.slack-edge.com/img/add_to_slack@2x.png 2x" /></a>

  </div>

</template>

<script>
export default {
  name: "IntegrationsSlack"
}
</script>

<style scoped>

</style>
