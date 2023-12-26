<template>
  <div>
    <h1 class="page-title">{{ $t('app.workflows.cancellation_request.edit.title') }}</h1>
  </div>
  <LoadingScreen :ready="ready">
    <div style="height: 1000px; width: 500px">
      <VueFlow :nodes="flowchartElements" :nodes-draggable="false" fit-view-on-init>
        <Controls />
      </VueFlow>
    </div>
  </LoadingScreen>
</template>

<script>
import axios from "axios";
import {VueFlow, useVueFlow} from "@vue-flow/core";
import {ref} from "vue";
import {Controls} from "@vue-flow/controls";
const {$reset, addEdges} = useVueFlow();

export default {
  name: "CancellationRequestEdit",
  components: {Controls, VueFlow},
  data() {
    return  {
      flowchartElements: [],
      placesRawData: [],
      handlersRawData: [],
      ready: false,
    }
  },
  mounted() {
    axios.get("/app/workflow/cancellation-request/edit").then(request => {
      this.placesRawData = request.data.places;
      this.handlersRawData = request.data.handlers;
      var rawElements = [];
      var y = 0;
      for (var i = 0; i < this.placesRawData.length; i++) {
        const place = this.placesRawData[i];
        rawElements.push({
          id: ''+place.priority,
          label: place.name,
          position: { x: 50, y: y },
        })
        y += 100
      }
      var prevElement = null;
      var edgesElement = [];
      for (var i = 0; i < this.placesRawData.length; i++) {
        const currentElement = this.placesRawData[i];
        if (prevElement !== null) {
          edgesElement.push({
            id: 'e'+prevElement.priority+'-'+currentElement.priority,
            source: ''+prevElement.priority,
            target: ''+currentElement.priority,
          })
        }
        prevElement = currentElement;
      }

      rawElements.push(...edgesElement);

      this.flowchartElements = ref(rawElements);
      this.ready = true;
    })
  }
}
</script>

<style scoped>

</style>