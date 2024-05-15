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
    var dataset = this.getDataset();
    this.computedData = dataset;
    console.log(dataset)
    this.map = new Datamap({
      element: document.getElementById('world_map'),
      responsive: true,
      data: dataset,
      fills: {
        defaultFill: 'rgba(169,224,255,0.9)', // Any hex, color name or rgb/rgba value
        geographyConfig: {
          highlightBorderWidth: 2,
          popupTemplate: (geo, data) => {
            if (!data) { return null; }
            const pluralizedLabel = data.numberOfThings === 1 ? label.slice(0, -1) : label
            return ['<div class="hoverinfo dark:bg-gray-800 dark:shadow-gray-850 dark:border-gray-850 dark:text-gray-200">',
              '<strong>', geo.properties.name, ' </strong>',
              '<br><strong class="dark:text-indigo-400">', numberFormatter(data.numberOfThings), '</strong> ', pluralizedLabel,
              '</div>'].join('');
          }
        }
      }
    });
    var map = this.map;
    d3.select(window).on('resize', function() {
      map.resize();
    });
  },
  methods: {
    getDataset: function () {
      const output = {};

      var onlyValues = this.dataset.map(function(obj){ return obj.value });
      var maxValue = Math.max.apply(null, onlyValues);

      const getFillColour = d3.scale.linear()
          .domain([0,maxValue])
          .range([
            "#bf92ff",
            "#6900ff",
          ])

      this.dataset.forEach(function(item){
        output[item.code] = {numberOfThings: item.value, fillColor: getFillColour(item.value)};
      });

      return output
    }
  }
}
</script>

<style scoped>

</style>
