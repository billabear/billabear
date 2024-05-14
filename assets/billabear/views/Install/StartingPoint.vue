<template>
  <div class="flex items-center justify-center h-screen login">
    <form @submit.prevent="handleSubmit" v-if="!complete">
      <div class="p-5 public-form-body" :class="{'animate-shake': error_info.has_error}">
        <div class="w-full">
          <PublicLogo />
        </div>
        <h1 class="h1 text-center">{{ $t('install.title') }}</h1>
        <div class="px-5 mt-2 mb-3" v-if="errorMessage">
          <div class="alert-error text-center">{{ errorMessage }}</div>
        </div>

        <div class="text-center mt-5 mb-3">
          <h2>{{  $t('install.settings.title') }}</h2>
        </div>
        <div class="px-5 mb-3">
          <label class="block mb-1">{{ $t('install.settings.default_brand') }}</label>
          <p class="form-field-error" v-if="errors.defaultBrand != undefined">{{ errors.defaultBrand }}</p>
          <input type="text" class="input-field" v-model="default_brand" />
        </div>
        <div class="px-5 mb-3">
          <label class="block mb-1">{{ $t('install.settings.country') }}</label>
          <p class="form-field-error" v-if="errors.country != undefined">{{ errors.country }}</p>
          <CountrySelect v-model="country" />
        </div>
        <div class="px-5 mb-3">
          <label class="block mb-1">{{ $t('install.settings.currency') }}</label>
          <p class="form-field-error" v-if="errors.currency != undefined">{{ errors.currency }}</p>
          <CurrencyRate v-model="currency" />
        </div>
        <div class="px-5 mb-3">
          <label class="block mb-1">{{ $t('install.settings.from_email') }}</label>
          <p class="form-field-error" v-if="errors.fromEmail != undefined">{{ errors.fromEmail }}</p>
          <input type="text" class="input-field" v-model="from_email" />
        </div>
        <div class="px-5 mb-3">
          <label class="block mb-1">{{ $t('install.settings.timezone') }}</label>
          <p class="form-field-error" v-if="errors.timezone != undefined">{{ errors.timezone }}</p>
          <select class="input-field" v-model="timezone">
            <option value="Pacific/Midway">(GMT-11:00) Midway Island, Samoa</option>
            <option value="America/Adak">(GMT-10:00) Hawaii-Aleutian</option>
            <option value="Etc/GMT+10">(GMT-10:00) Hawaii</option>
            <option value="Pacific/Marquesas">(GMT-09:30) Marquesas Islands</option>
            <option value="Pacific/Gambier">(GMT-09:00) Gambier Islands</option>
            <option value="America/Anchorage">(GMT-09:00) Alaska</option>
            <option value="America/Ensenada">(GMT-08:00) Tijuana, Baja California</option>
            <option value="Etc/GMT+8">(GMT-08:00) Pitcairn Islands</option>
            <option value="America/Los_Angeles">(GMT-08:00) Pacific Time (US & Canada)</option>
            <option value="America/Denver">(GMT-07:00) Mountain Time (US & Canada)</option>
            <option value="America/Chihuahua">(GMT-07:00) Chihuahua, La Paz, Mazatlan</option>
            <option value="America/Dawson_Creek">(GMT-07:00) Arizona</option>
            <option value="America/Belize">(GMT-06:00) Saskatchewan, Central America</option>
            <option value="America/Cancun">(GMT-06:00) Guadalajara, Mexico City, Monterrey</option>
            <option value="Chile/EasterIsland">(GMT-06:00) Easter Island</option>
            <option value="America/Chicago">(GMT-06:00) Central Time (US & Canada)</option>
            <option value="America/New_York">(GMT-05:00) Eastern Time (US & Canada)</option>
            <option value="America/Havana">(GMT-05:00) Cuba</option>
            <option value="America/Bogota">(GMT-05:00) Bogota, Lima, Quito, Rio Branco</option>
            <option value="America/Caracas">(GMT-04:30) Caracas</option>
            <option value="America/Santiago">(GMT-04:00) Santiago</option>
            <option value="America/La_Paz">(GMT-04:00) La Paz</option>
            <option value="Atlantic/Stanley">(GMT-04:00) Faukland Islands</option>
            <option value="America/Campo_Grande">(GMT-04:00) Brazil</option>
            <option value="America/Goose_Bay">(GMT-04:00) Atlantic Time (Goose Bay)</option>
            <option value="America/Glace_Bay">(GMT-04:00) Atlantic Time (Canada)</option>
            <option value="America/St_Johns">(GMT-03:30) Newfoundland</option>
            <option value="America/Araguaina">(GMT-03:00) UTC-3</option>
            <option value="America/Montevideo">(GMT-03:00) Montevideo</option>
            <option value="America/Miquelon">(GMT-03:00) Miquelon, St. Pierre</option>
            <option value="America/Godthab">(GMT-03:00) Greenland</option>
            <option value="America/Argentina/Buenos_Aires">(GMT-03:00) Buenos Aires</option>
            <option value="America/Sao_Paulo">(GMT-03:00) Brasilia</option>
            <option value="America/Noronha">(GMT-02:00) Mid-Atlantic</option>
            <option value="Atlantic/Cape_Verde">(GMT-01:00) Cape Verde Is.</option>
            <option value="Atlantic/Azores">(GMT-01:00) Azores</option>
            <option value="Europe/Belfast">(GMT) Greenwich Mean Time : Belfast</option>
            <option value="Europe/Dublin">(GMT) Greenwich Mean Time : Dublin</option>
            <option value="Europe/Lisbon">(GMT) Greenwich Mean Time : Lisbon</option>
            <option value="Europe/London">(GMT) Greenwich Mean Time : London</option>
            <option value="Africa/Abidjan">(GMT) Monrovia, Reykjavik</option>
            <option value="Europe/Amsterdam">(GMT+01:00) Amsterdam, Berlin, Bern, Rome, Stockholm, Vienna</option>
            <option value="Europe/Belgrade">(GMT+01:00) Belgrade, Bratislava, Budapest, Ljubljana, Prague</option>
            <option value="Europe/Brussels">(GMT+01:00) Brussels, Copenhagen, Madrid, Paris</option>
            <option value="Africa/Algiers">(GMT+01:00) West Central Africa</option>
            <option value="Africa/Windhoek">(GMT+01:00) Windhoek</option>
            <option value="Asia/Beirut">(GMT+02:00) Beirut</option>
            <option value="Africa/Cairo">(GMT+02:00) Cairo</option>
            <option value="Asia/Gaza">(GMT+02:00) Gaza</option>
            <option value="Africa/Blantyre">(GMT+02:00) Harare, Pretoria</option>
            <option value="Asia/Jerusalem">(GMT+02:00) Jerusalem</option>
            <option value="Europe/Minsk">(GMT+02:00) Minsk</option>
            <option value="Asia/Damascus">(GMT+02:00) Syria</option>
            <option value="Europe/Moscow">(GMT+03:00) Moscow, St. Petersburg, Volgograd</option>
            <option value="Africa/Addis_Ababa">(GMT+03:00) Nairobi</option>
            <option value="Asia/Tehran">(GMT+03:30) Tehran</option>
            <option value="Asia/Dubai">(GMT+04:00) Abu Dhabi, Muscat</option>
            <option value="Asia/Yerevan">(GMT+04:00) Yerevan</option>
            <option value="Asia/Kabul">(GMT+04:30) Kabul</option>
            <option value="Asia/Yekaterinburg">(GMT+05:00) Ekaterinburg</option>
            <option value="Asia/Tashkent">(GMT+05:00) Tashkent</option>
            <option value="Asia/Kolkata">(GMT+05:30) Chennai, Kolkata, Mumbai, New Delhi</option>
            <option value="Asia/Katmandu">(GMT+05:45) Kathmandu</option>
            <option value="Asia/Dhaka">(GMT+06:00) Astana, Dhaka</option>
            <option value="Asia/Novosibirsk">(GMT+06:00) Novosibirsk</option>
            <option value="Asia/Rangoon">(GMT+06:30) Yangon (Rangoon)</option>
            <option value="Asia/Bangkok">(GMT+07:00) Bangkok, Hanoi, Jakarta</option>
            <option value="Asia/Krasnoyarsk">(GMT+07:00) Krasnoyarsk</option>
            <option value="Asia/Hong_Kong">(GMT+08:00) Beijing, Chongqing, Hong Kong, Urumqi</option>
            <option value="Asia/Irkutsk">(GMT+08:00) Irkutsk, Ulaan Bataar</option>
            <option value="Australia/Perth">(GMT+08:00) Perth</option>
            <option value="Australia/Eucla">(GMT+08:45) Eucla</option>
            <option value="Asia/Tokyo">(GMT+09:00) Osaka, Sapporo, Tokyo</option>
            <option value="Asia/Seoul">(GMT+09:00) Seoul</option>
            <option value="Asia/Yakutsk">(GMT+09:00) Yakutsk</option>
            <option value="Australia/Adelaide">(GMT+09:30) Adelaide</option>
            <option value="Australia/Darwin">(GMT+09:30) Darwin</option>
            <option value="Australia/Brisbane">(GMT+10:00) Brisbane</option>
            <option value="Australia/Hobart">(GMT+10:00) Hobart</option>
            <option value="Asia/Vladivostok">(GMT+10:00) Vladivostok</option>
            <option value="Australia/Lord_Howe">(GMT+10:30) Lord Howe Island</option>
            <option value="Etc/GMT-11">(GMT+11:00) Solomon Is., New Caledonia</option>
            <option value="Asia/Magadan">(GMT+11:00) Magadan</option>
            <option value="Pacific/Norfolk">(GMT+11:30) Norfolk Island</option>
            <option value="Asia/Anadyr">(GMT+12:00) Anadyr, Kamchatka</option>
            <option value="Pacific/Auckland">(GMT+12:00) Auckland, Wellington</option>
            <option value="Etc/GMT-12">(GMT+12:00) Fiji, Kamchatka, Marshall Is.</option>
            <option value="Pacific/Chatham">(GMT+12:45) Chatham Islands</option>
            <option value="Pacific/Tongatapu">(GMT+13:00) Nuku'alofa</option>
            <option value="Pacific/Kiritimati">(GMT+14:00) Kiritimati</option>
          </select>
        </div>
        <div class="px-5 mb-3">
          <label class="block mb-1">{{ $t('install.settings.webhook_url') }}</label>
          <p class="form-field-error" v-if="errors.webhooKurl != undefined">{{ errors.webhooKurl }}</p>
          <input type="text" class="input-field" v-model="webhook_url" />
        </div>

        <div class="text-center mb-3">
          <h2>{{  $t('install.user.title') }}</h2>
        </div>

        <div class="px-5 mb-3">
          <label class="block mb-1">{{ $t('install.user.email') }}</label>
          <p class="form-field-error" v-if="errors.email != undefined">{{ errors.email }}</p>
          <input type="text" class="input-field" v-model="email" />
        </div>
        <div class="px-5 mb-3">
          <label class="block mb-1">{{ $t('install.user.password') }}</label>
          <p class="form-field-error" v-if="errors.password != undefined">{{ errors.password }}</p>
          <input type="password" class="input-field" v-model="password" />
        </div>
        <div class="px-5">
          <SubmitButton :in-progress="sendingInProgress">{{ $t('install.submit_button') }}</SubmitButton>
        </div>
      </div>
    </form>
    <div v-else>
      <p class="text-center">
        {{ $t('install.complete_text') }}
      </p>

      <p class="text-center"><router-link :to="{name: 'public.login'}" class="text-sm font-medium text-primary-600 hover:underline dark:text-primary-500">{{ $t('install.login_link') }}</router-link></p>
    </div>
  </div>
</template>

<script>
import PublicLogo from "../../components/public/PublicLogo.vue";
import axios from "axios";
import CurrencyRate from "../../components/app/Forms/CurrencySelect.vue";
import CountrySelect from "../../components/app/Forms/CountrySelect.vue";

export default {
  name: "StartingPoint",
  components: {CountrySelect, CurrencyRate, PublicLogo},
  data() {
    return {
      error_info: {
        has_error: false,
      },
      errors: {},
      sendingInProgress: null,
      email: null,
      password: null,
      default_brand: null,
      from_email: 'outgoing@example.org',
      timezone: 'Europe/Amsterdam',
      webhook_url: null,
      complete: false,
      errorMessage: null,
      currency: null,
      country: null,
    }
  },
  mounted() {
    this.webhook_url = window.location.origin;
  },
  methods: {
    handleSubmit: function () {
      this.sendingInProgress = true;
       const payload = {
         default_brand: this.default_brand,
         from_email: this.from_email,
         timezone: this.timezone,
         webhook_url: this.webhook_url,
         email: this.email,
         password: this.password,
         currency: this.currency,
         country: this.country,
      };
      axios.post('/install/process', payload).then(
          response => {
            this.sendingInProgress = false;
            this.success = true;
            this.complete = true;
          }
      ).catch(error => {
        if (error.response.data.errors !== undefined && error.response.data.errors !== null) {
          this.errors = error.response.data.errors;
        } else {
          this.errorMessage = this.$t('install.unknown_error')
        }

        this.sendingInProgress = false;
        this.success = false;
      })
    }
  }
}
</script>

<style scoped>

</style>