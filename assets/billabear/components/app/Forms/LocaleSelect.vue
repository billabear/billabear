<template>
  <select class="form-field" @change="$emit('update:modelValue', $event.target.value)">
    <option value="en">{{ $t('global.locale.en') }}</option>
    <option value="es">{{ $t('global.locale.es') }}</option>
    <option value="fr">{{ $t('global.locale.fr') }}</option>
    <option value="de">{{ $t('global.locale.de') }}</option>
  </select>
</template>

<script>
export default {
  name: "LocaleSelect"
}
</script>

<style scoped>

</style>
