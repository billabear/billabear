<template>
  <input class="form-field-input" ref="inputRef" type="text" />
</template>

<script>
import { useCurrencyInput } from "vue-currency-input";

export default {
  name: "CurrencyInput",
  props: {
    modelValue: Number,
  },
  setup(props) {
    const { inputRef } = useCurrencyInput({ currency: 'USD', valueScaling: 'precision', currencyDisplay: 'hidden', hideCurrencySymbolOnFocus: false, hideGroupingSeparatorOnFocus: false, hideNegligibleDecimalDigitsOnFocus: false });

    return { inputRef };
  },
};
</script>
