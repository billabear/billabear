<template>
  <div id="world_map">
  </div>
</template>

<script>
import "d3";
import Datamap from 'datamaps'

export default {
  name: "WorldMap",
  props: {
    dataset: {
      type: Array,
      required: true,
    },
  },
  data() {
    return {
      map: null,
      computedData: null,
    }
  },
  mounted() {
    const dataset = this.getDataset();
    this.computedData = dataset;

    this.map = new Datamap({
      element: document.getElementById('world_map'),
      responsive: true,
      data: dataset,
      fills: {
        defaultFill: 'rgba(226,244,255,0.9)', // Any hex, color name or rgb/rgba value
      },
      geographyConfig: {
        highlightBorderWidth: 2,
        popupTemplate: function(geo, data) {
          return ['<div class="hoverinfo dark:bg-gray-800 dark:shadow-gray-850 dark:border-gray-850 dark:text-gray-200">',
            '<strong>', geo.properties.name, ' </strong>',
            '<br><strong class="dark:text-indigo-400">', data.formatted, ' ' , data.label, '</strong> ',
            '</div>'].join('');
        }
      },
    });
    const map = this.map;
    d3.select(window).on('resize', function() {
      map.resize();
    });
  },
  methods: {
    getDataset: function () {
      const output = {};

      const onlyValues = this.dataset.map(function(obj){ return obj.value });
      const maxValue = Math.max.apply(null, onlyValues);

      const getFillColour = d3.scale.linear()
          .domain([0,maxValue])
          .range([
            "#bf92ff",
            "#6900ff",
          ])

      this.dataset.forEach(function(item){
        output[item.code] = {numberOfThings: item.value, fillColor: getFillColour(item.value), label: item.label, formatted: item.formatted_value};
      });

      return output
    }
  }
}
</script>

<style scoped>

</style>
