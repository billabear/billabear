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

<script setup>

import axios from "axios";
import {VueFlow, useVueFlow} from "@vue-flow/core";
import {ref, onMounted} from "vue";
import {Controls} from "@vue-flow/controls";
import { useI18n} from "vue-i18n";
import { NodeToolbar } from '@vue-flow/node-toolbar'

var flowchartElements = ref([]);
var placesRawData = [];
var handlersRawData = [];
var ready = ref(false);
const {t} = useI18n();

const {
  onEdgeClick,
  onEdgeDoubleClick,
  onEdgeContextMenu,
  onEdgeMouseEnter,
  onEdgeMouseLeave,
  onEdgeMouseMove,
  onEdgeUpdateStart,
  onEdgeUpdate,
  onEdgeUpdateEnd,
} = useVueFlow()

onEdgeDoubleClick((event, edge) => {
  console.log('edge clicked', edge, event)
})

onMounted(() => {
  axios.get("/app/workflow/cancellation-request/edit").then(request => {
    placesRawData = request.data.places;
    handlersRawData = request.data.handlers;
    var rawElements = [];
    var y = 0;
    for (var i = 0; i < placesRawData.length; i++) {
      const place = placesRawData[i];
      rawElements.push({
        id: ''+place.priority,
        label: place.name,
        position: { x: 50, y: y },
        special: place.default,
        data: place,
      })
      y += 100
    }
    var prevElement = null;
    var edgesElement = [];
    for (var i = 0; i < placesRawData.length; i++) {
      const currentElement = placesRawData[i];
      if (prevElement !== null) {
        edgesElement.push({
          id: 'e'+prevElement.priority+'-'+currentElement.priority,
          source: ''+prevElement.priority,
          target: ''+currentElement.priority,
          label: t('app.workflows.cancellation_request.edit.add_place'),
          animated: false,
        })
      }
      prevElement = currentElement;
    }

    rawElements.push(...edgesElement);

    flowchartElements = ref(rawElements);
    ready.value = true;
  })
})

</script>



<style scoped>

</style>