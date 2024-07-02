/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './billabear/styles/flowbite.css';

import '@vue-flow/core/dist/style.css';
import '@vue-flow/core/dist/theme-default.css';
import '@vue-flow/controls/dist/style.css'



/* this contains the default theme, these are optional styles */
import 'vue-final-modal/style.css';
import 'flowbite';

import './billabear/styles/app.css';
import './billabear/flowbite/constants';

import App from "./billabear/views/App";

import {router} from "./billabear/helpers/router"
import {store} from './billabear/store'
import { createApp } from "vue";
import { createVfm } from 'vue-final-modal'
import {ENGLISH_TRANSLATIONS} from "./billabear/translations/en";
import {createI18n} from "vue-i18n";
import VueApexCharts from "vue3-apexcharts";

import '@fortawesome/fontawesome-free/css/all.css'
import ParthenonMenu from "@parthenon/vue-menu";
import ParthenonUI from "@parthenon/ui";

import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css'
import RoleOnlyView from "./billabear/components/app/RoleOnlyView.vue";


import { VueFlow } from '@vue-flow/core'
import {GERMAN_TRANSLATIONS} from "./billabear/translations/de";
import {SPANISH_TRANSLATIONS} from "./billabear/translations/es";
import {FRENCH_TRANSLATIONS} from "./billabear/translations/fr";

const TRANSLATIONS = {
    en: ENGLISH_TRANSLATIONS,
    de: GERMAN_TRANSLATIONS,
    es: SPANISH_TRANSLATIONS,
    fr: FRENCH_TRANSLATIONS,
};
function getBrowserLocale(options = {}) {
    const defaultOptions = { countryCodeOnly: false };
    const opt = { ...defaultOptions, ...options };

    const navigatorLocale = navigator.languages !== undefined
        ? navigator.languages[0]
        : navigator.language;

    if (!navigatorLocale) {
        return undefined;
    }

    const trimmedLocale = opt.countryCodeOnly
        ? navigatorLocale.trim().split(/-|_/)[0]
        : navigatorLocale.trim();

    return trimmedLocale;
}

const browserLocale = getBrowserLocale({ countryCodeOnly: true });
console.log(browserLocale)
const i18n = createI18n({
    legacy: false,
    locale: browserLocale || 'en',
    messages: TRANSLATIONS,
});

var billabear = createApp(
    App
);

const vfm = createVfm()

billabear.use(router);
billabear.use(i18n);
billabear.use(store);
billabear.use(ParthenonMenu);
billabear.use(ParthenonUI);
billabear.use(VueFlow);
billabear.use(VueApexCharts)
billabear.use(require('vue-moment-v3'))
billabear.use(vfm)
billabear.component('RoleOnlyView', RoleOnlyView);
billabear.component('VueDatePicker', VueDatePicker);

billabear.mount('#app');
