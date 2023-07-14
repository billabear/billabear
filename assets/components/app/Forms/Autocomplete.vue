<template>
  <div class="autocomplete">
    <input id="autocomplete-input" type="text" class="form-field" v-model="searchTerm" @input="fetchAutocompleteResults" @blur="innerBlur" />
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
    },
    blurCallback: {
      type: Function,
      default: function () {
        return function (){ } ;
      }
    },
    selectedCallback: {
      type: Function,
      default: function () {
        return function (){ } ;
      }
    }
  },
  data() {
    return {
      searchTerm: '',
      autocompleteResults: [],
      showAutocompleteResults: false,
      hasSeached: false,
      searchedTerm: null,
    };
  },
  methods: {
    innerBlur: function (event) {
        const input = event.target.value;
        console.log(this.autocompleteResults);
        for (var i = 0; i < this.autocompleteResults.length; i++) {
          var result = this.autocompleteResults[i];
          console.log(result)
          if (result[this.displayKey] == input) {
              this.selectAutocompleteResult(result);
              return;
          }
        }

        this.$emit('update:modelValue', null)
        this.blurCallback(event);
        var that = this;
        setTimeout(function (){that.showAutocompleteResults = false;}, 1000);
    },
    fetchAutocompleteResults() {
      if ( (!this.hasSeached || !this.searchTerm.startsWith(this.searchedTerm)  || this.autocompleteResults.length > 1)) {
        if (this.searchTerm.length > 2) {
          this.hasSeached = true;
          this.searchedTerm = this.searchTerm;
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
      }
    },
    selectAutocompleteResult(result) {
      this.searchTerm = result[this.displayKey];
      this.showAutocompleteResults = false;
      this.hasSeached = false;
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