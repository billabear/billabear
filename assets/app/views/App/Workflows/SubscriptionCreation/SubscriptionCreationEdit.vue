<template>
  <div>
    <h1 class="page-title">{{ $t('app.workflows.subscription_creation.edit.title') }}</h1>
  </div>
  <LoadingScreen :ready="ready">
    <div style="height: 1000px; width: 500px">
      <VueFlow :nodes="flowchartElements" :nodes-draggable="false" fit-view-on-init>
        <Controls />
      </VueFlow>
    </div>

  </LoadingScreen>
  <VueFinalModal
      :modal-id="'template'"
      v-model="options.modelValue"
      :teleport-to="options.teleportTo"
      :display-directive="options.displayDirective"
      :hide-overlay="options.hideOverlay"
      :overlay-transition="options.overlayTransition"
      :content-transition="options.contentTransition"
      :click-to-close="options.clickToClose"
      :esc-to-close="options.escToClose"
      :background="options.background"
      :lock-scroll="options.lockScroll"
      :swipe-to-close="options.swipeToClose"
      class="flex justify-center items-center"
      content-class="max-w-xl mx-4 p-4 bg-white dark:bg-gray-900 border dark:border-gray-700 rounded-lg space-y-2"
  >
    <h3>{{ $t('app.workflows.subscription_creation.edit.add_place_modal.title') }}</h3>

    <dl class="detail-list">
      <div>
        <dt>{{ $t('app.workflows.subscription_creation.edit.add_place_modal.from_place') }}</dt>
        <dd>
          {{ createModalValues.source.node.label }}
        </dd>
      </div>
      <div>
        <dt>{{ $t('app.workflows.subscription_creation.edit.add_place_modal.to_place') }}</dt>
        <dd>
          {{ createModalValues.target.node.label }}
        </dd>
      </div>
      <div>
        <dt>{{ $t('app.workflows.subscription_creation.edit.add_place_modal.name') }}</dt>
        <dd>
          <input type="text" v-model="createModalValues.name" />
        </dd>
      </div>
      <div>
        <dt>{{ $t('app.workflows.subscription_creation.edit.add_place_modal.event_handler') }}</dt>
        <dd>
          <select v-model="createModalValues.handler">
            <option v-for="handler in handlersRawData" :value="handler">{{handler.name}}</option>
          </select>
        </dd>
      </div>
    </dl>

    <div class="mt-5" v-if="createModalValues.handler !== null && createModalValues.handler !== undefined">
      <h2>{{ $t('app.workflows.subscription_creation.edit.add_place_modal.handler_options') }}</h2>
      <dl class="detail-list">
        <div v-for="(option, key) in createModalValues.handler.options">
          <dt>{{ key }}</dt>
          <dd>
            <span class="error-message" v-if="handlerOptionsErrors[key] !== undefined">{{ $t('app.workflows.subscription_creation.edit.add_place_modal.required') }}</span>
            <input type="text" v-if="option.type === 'string'" v-model="option.value" />
          </dd>
        </div>
      </dl>
    </div>

    <div class="mt-5">
      <button class="btn--main" :class="{'btn--disabled' : (createModalValues.name === '' || createModalValues.handler === null)}" @click="sendCreate" :disabled="createModalValues.name === '' || createModalValues.handler === null">{{ $t('app.workflows.subscription_creation.edit.add_place_modal.add') }}</button>
    </div>
  </VueFinalModal>

  <VueFinalModal
      class="flex justify-center items-center"
      content-class="max-w-xl mx-4 p-4 bg-white dark:bg-gray-900 border dark:border-gray-700 rounded-lg space-y-2"
      :modal-id="'place_details'">

    <h3>{{ $t('app.workflows.subscription_creation.edit.edit_place_modal.title') }}</h3>

    <button class="btn--danger" @click="sendDisable(editModalValues.node.data.id)" v-if="editModalValues.node.data.enabled">{{ $t('app.workflows.subscription_creation.edit.edit_place_modal.disable_button') }}</button>
    <button class="btn--main" @click="sendEnable(editModalValues.node.data.id)" v-else>{{ $t('app.workflows.subscription_creation.edit.edit_place_modal.enable_button') }}</button>
  </VueFinalModal>
</template>

<script setup>

import axios from "axios";
import {VueFlow, useVueFlow} from "@vue-flow/core";
import {ref, onMounted} from "vue";
import {Controls} from "@vue-flow/controls";
import { useI18n} from "vue-i18n";
import { NodeToolbar } from '@vue-flow/node-toolbar'
import {VueFinalModal, useVfm} from "vue-final-modal";
import {Button, Input, Select} from "flowbite-vue";
import {useRouter} from "vue-router";

var flowchartElements = ref([]);
var placesRawData = [];
var handlersRawData = [];
var ready = ref(false);
var options = {
  teleportTo: 'body',
  modelValue: false,
  displayDirective: 'if',
  hideOverlay: false,
  overlayTransition: 'vfm-fade',
  contentTransition: 'vfm-fade',
  clickToClose: true,
  escToClose: true,
  background: 'non-interactive',
  lockScroll: true,
  swipeToClose: 'none',
};
const {t} = useI18n();

var createModalValues = {
  target: {
  },
  handler: null,
};
var editModalValues = ref({
  node: null
})
const {
  onEdgeDoubleClick,
  onNodeDoubleClick,
} = useVueFlow()

onNodeDoubleClick(event => {

  if (event.node.data.default) {
    return;
  }
  editModalValues = ref({node: event.node})
  useVfm().open('place_details')
})

onEdgeDoubleClick(edge => {
  useVfm().open('template')
  console.log(edge);

  createModalValues = ref({
    name: '',
    handler: null,
    target: {
      node: edge.edge.targetNode,
    },
    source: {
      node: edge.edge.sourceNode,
    }
  })
})
const sync = () => {
  axios.get("/app/workflow/subscription-creation/edit").then(request => {
    placesRawData = request.data.places;
    handlersRawData = request.data.handlers;
    var rawElements = [];
    var y = 0;
    for (var i = 0; i < placesRawData.length; i++) {
      const place = placesRawData[i];
      var className = 'default-workflow-node'
      if (!place.default) {
        className = 'custom-workflow-node';
      }

      if (!place.enabled) {
        className = className + ' disabled-workflow-node';
      }
      rawElements.push({
        id: ''+place.priority,
        label: place.name,
        position: { x: 50, y: y },
        special: place.default,
        class: className,
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
          label: t('app.workflows.subscription_creation.edit.add_place'),
          animated: false,
        })
      }
      prevElement = currentElement;
    }

    rawElements.push(...edgesElement);

    flowchartElements = ref(rawElements);
    ready.value = true;
  })
};

onMounted(sync)

var handlerOptionsErrors = ref({});
function sendCreate(event) {

  var values = createModalValues.value;
  var priority = parseInt(values.source.node.id) + ((parseInt(values.target.node.id) - parseInt(values.source.node.id)) / 2);

  var payload = {
    workflow: "create_subscription",
    name: values.name,
    priority: priority,
    handler: values.handler.name,
    handler_options: {},
  }

  var errors = {};
  for (const [key, value] of Object.entries(values.handler.options)) {
    if (value.required && (value.value === "" || value.value === undefined || value.value === null)) {
      errors[key] = true;
    } else {
      payload.handler_options[key] = values.handler.options[key].value
    }
  }
  handlerOptionsErrors.value = errors;
  if (Object.entries(errors).length !== 0) {
    return;
  }

  axios.post('/app/workflow/create-transition', payload).then(response => {

    sync();

    useVfm().close('template');
  })
}
function sendEnable(id) {
  axios.post('/app/workflow/transition/'+id+'/enable').then(response => {
    if (response.status == 202) {
      sync();
      useVfm().close('place_details')
    }
  })
}
function sendDisable(id) {
  axios.post('/app/workflow/transition/'+id+'/disable').then(response => {
    if (response.status == 202) {
      sync();
      useVfm().close('place_details')
    }
  })
}
</script>



<style scoped>

</style>