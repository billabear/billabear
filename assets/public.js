
import {createI18n} from "vue-i18n";
import {createApp} from "vue";
import App from "./public/views/App.vue";
import {createVfm} from "vue-final-modal";
import {router} from "./public/helper/router";
import ParthenonUI from "@parthenon/ui";
import {getBrowserLocale} from "./shared/utils/locale.js";

import '@fortawesome/fontawesome-free/css/all.css';
import 'vue-final-modal/style.css';
import './public/styles/app.css';

import {ENGLISH_TRANSLATIONS} from "./public/translations/en/index.js";
import GERMAN_TRANSLATIONS from "./public/translations/de.json";
import SPANISH_TRANSLATIONS from "./public/translations/es.json";
import FRENCH_TRANSLATIONS from "./public/translations/fr.json";
import DUTCH_TRANSLATIONS from "./public/translations/nl.json";
import ITALIAN_TRANSLATIONS from "./public/translations/it.json";

const TRANSLATIONS = {
    en: ENGLISH_TRANSLATIONS,
    de: GERMAN_TRANSLATIONS,
    es: SPANISH_TRANSLATIONS,
    fr: FRENCH_TRANSLATIONS,
    nl: DUTCH_TRANSLATIONS,
    it: ITALIAN_TRANSLATIONS,
};

const browserLocale = getBrowserLocale({ countryCodeOnly: true });

const i18n = createI18n({
    legacy: false,
    locale: browserLocale || 'en',
    messages: TRANSLATIONS,
    fallbackLocale: ['en']
});

const app = createApp(
    App
);

const vfm = createVfm()

app.use(router);
app.use(i18n);
app.use(require('vue-moment-v3'))
app.use(vfm)
app.use(ParthenonUI);
app.mount('#app');
