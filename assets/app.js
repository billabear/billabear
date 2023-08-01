/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './app/styles/app.css';
import 'vue-final-modal/style.css';


import App from "./app/views/App";

import {router} from "./app/helpers/router"
import {store} from './app/store'
import { createApp } from "vue";
import { createVfm } from 'vue-final-modal'
import {ENGLISH_TRANSLATIONS} from "./app/translations/en";
import {createI18n} from "vue-i18n";
import VueApexCharts from "vue3-apexcharts";

import '@fortawesome/fontawesome-free/css/all.css'
import ParthenonMenu from "@parthenon/vue-menu";
import ParthenonUI from "@parthenon/ui";

import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css'
import RoleOnlyView from "./app/components/app/RoleOnlyView.vue";


const TRANSLATIONS = {
    en: ENGLISH_TRANSLATIONS
};

const i18n = createI18n({
    locale: 'en',
    messages: TRANSLATIONS,
});

var app = createApp(
    App
);

const vfm = createVfm()

app.use(router);
app.use(i18n);
app.use(store);
app.use(ParthenonMenu);
app.use(ParthenonUI);
app.use(VueApexCharts)
app.use(require('vue-moment-v3'))
app.use(vfm)
app.component('RoleOnlyView', RoleOnlyView);
app.component('VueDatePicker', VueDatePicker);

app.mount('#app');