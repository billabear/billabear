<template>
  <div class="autocomplete">
    <label  class="form-field-lbl" for="autocomplete-input">Search:</label>
    <input id="autocomplete-input" type="text" class="form-field" v-model="searchTerm" @input="fetchAutocompleteResults" />
    <ul v-if="showAutocompleteResults" class="autocomplete-results">
      <li class="autocomplete-items " v-for="result in autocompleteResults" :key="result.id" @click="selectAutocompleteResult(result)">
        {{ result[displayKey] }}
      </li>
    </ul>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  props: {
    restEndpoint: {
      type: String,
      required: true,
    },
    searchKey: {
      type: String,
      required: true,
    },
    displayKey: {

      type: String,
      required: true,
    }
  },
  data() {
    return {
      searchTerm: '',
      autocompleteResults: [],
      showAutocompleteResults: false,
    };
  },
  methods: {
    fetchAutocompleteResults() {
      if (this.searchTerm.length > 2) {
        axios
            .get(`${this.restEndpoint}?${this.searchKey}=${this.searchTerm}`)
            .then(response => {
              this.autocompleteResults = response.data.data;
              this.showAutocompleteResults = true;
            })
            .catch(error => {
              console.error('Error fetching autocomplete results:', error);
            });
      } else {
        this.autocompleteResults = [];
        this.showAutocompleteResults = false;
      }
    },
    selectAutocompleteResult(result) {
      this.searchTerm = result[this.displayKey];
      this.showAutocompleteResults = false;
      this.$emit('autocomplete-selected', result);
      this.$emit('update:modelValue', result.id)
    },
  },
};
</script>

<style>
.autocomplete {
  position: relative;
}

.autocomplete-results {
  position: absolute;
  z-index: 1;
  background-color: #fff;
  list-style-type: none;
  padding: 0;
  margin: 0;
  border: 1px solid #ccc;
}

.autocomplete-results li {
  padding: 5px;
  cursor: pointer;
}

.autocomplete-results li:hover {
  background-color: #f1f1f1;
}
</style>