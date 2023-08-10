import {ENGLISH_TRANSLATIONS} from "./public/translations/en";
import {createI18n} from "vue-i18n";
import {createApp} from "vue";
import App from "./public/views/App.vue";
import {createVfm} from "vue-final-modal";
import {router} from "./public/helper/router";
import ParthenonUI from "@parthenon/ui";

import '@fortawesome/fontawesome-free/css/all.css';
import 'vue-final-modal/style.css';
import './public/styles/app.css';

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
app.use(require('vue-moment-v3'))
app.use(vfm)
app.use(ParthenonUI);
app.mount('#app');