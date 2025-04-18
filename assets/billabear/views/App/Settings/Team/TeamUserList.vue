<template>
  <div v-if="!has_error">
    <div class="grid grid-cols-2">
      <h1 class="page-title">{{ $t('app.settings.user.list.title') }}</h1>


      <div class="text-end mt-5 top-button-container">
        <router-link :to="{name: 'app.user.invite'}" class="btn--main ml-4"><i class="fa-solid fa-user-plus"></i> {{ $t('app.settings.user.list.invite') }}</router-link>
      </div>

    </div>

    <LoadingScreen :ready="ready">
      <div class="rounded-lg bg-white shadow p-3">
        <table class="w-full">
          <thead>
          <tr class="border-b border-black">
            <th class="text-left pb-2">{{ $t('app.settings.user.list.list.email')}}</th>
            <th class="text-left pb-2">{{ $t('app.settings.user.list.list.role') }}</th>
            <th></th>
          </tr>
          </thead>
          <tbody v-if="loaded">
          <tr v-for="user in users" class="mt-5 cursor-pointer">
            <td class="py-3">{{ user.email }}</td>
            <td class="py-3">
              <span v-for="role in user.roles" class="badge--green mr-1">{{ role }}</span>
            </td>
            <td class="text-end">
              <router-link :to="{name: 'app.compliance.audit.billing_admin', params: {id: user.id}}" class="btn--secondary mr-3">{{ $t('app.settings.user.list.audit_log') }}</router-link>
              <router-link :to="{name: 'app.settings.users.update', params: {id: user.id}}" class="list-btn ">{{ $t('app.settings.user.list.view_btn') }}</router-link>
            </td>
          </tr>
          <tr v-if="users.length === 0">
            <td colspan="4" class="py-3 text-center">{{ $t('app.settings.user.list.no_users') }}</td>
          </tr>
          </tbody>
          <tbody v-else>
          <tr>
            <td colspan="4" class="py-3 text-center">
              <LoadingMessage>{{ $t('app.settings.user.list.loading') }}</LoadingMessage>
            </td>
          </tr>
          </tbody>
        </table>
      </div>
      <div class="sm:grid sm:grid-cols-2">

        <div class="mt-4">
          <button @click="prevPage" v-if="show_back" class="btn--main mr-3" >{{ $t('app.settings.user.list.prev') }}</button>
          <button @click="nextPage" v-if="has_more" class="btn--main" >{{ $t('app.settings.user.list.next') }}</button>
        </div>
        <div class="mt-4 text-end">
          <select class="rounded-lg border border-gray-300" @change="changePerPage" v-model="per_page">
            <option value="10">10</option>
            <option value="25">25</option>
            <option value="50">50</option>
            <option value="100">100</option>
          </select>
        </div>
      </div>

      <div class="" v-if="invites.length > 0">
        <h3 class="text-3xl mb-3">{{ $t('app.settings.user.list.invite_title') }}</h3>
        <div class="rounded-lg bg-white shadow p-3">
          <table class="list-table">
            <thead>
            <tr>
              <th>{{ $t('app.settings.user.list.invite_list.email')}}</th>
              <th>{{ $t('app.settings.user.list.invite_list.role') }}</th>
              <th></th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="(invite, key) in invites">
              <td>{{ invite.email }}</td>
              <td><span class="badge--green">{{ invite.role }}</span></td>
              <td>
                <button class="btn--main" v-if="copied === key" disabled><i class="fa-solid fa-copy"></i> {{ $t('app.settings.user.list.invite_list.copied_link') }}</button>
                <button class="btn--main" @click="copyInviteToClipboard(invite, key)" v-else><i class="fa-solid fa-copy"></i> {{ $t('app.settings.user.list.invite_list.copy_link') }}</button>

              </td>

            </tr>
            </tbody>
          </table>
        </div>
      </div>
    </LoadingScreen>
  </div>
  <div v-else class="error-page">
    {{ $t('app.settings.user.list.error_message') }}
  </div>
</template>

<script>
import axios from "axios";

export default {
  name: "CustomerList.vue",
  data() {
    return {
      ready: false,
      users: [],
      copied: false,
      invites: [],
      has_more: false,
      has_error: false,
      loaded: true,
      last_key: null,
      first_key: null,
      previous_last_key: null,
      next_page_in_progress: false,
      show_back: false,
      show_filter_menu: false,
      active_filters: [],
      per_page: "10",
      filters: {
        email: {
          label: 'app.settings.user.list.filter.email',
          type: 'text',
          value: null
        },
      }
    }
  },
  mounted() {
    this.doStuff();

  },
  watch: {
    '$route.query': function (id) {
      this.doStuff()
    }
  },
  methods: {
    copyInviteToClipboard: function (invite, key) {
      var that = this;
      that.copied = key;
      setTimeout( function () {
        that.copied = false;
      }, 1000);

      navigator.clipboard.writeText(window.location.origin+"/signup/"+invite.code);
    },
    syncQueryToFilters: function () {
      Object.keys(this.filters).forEach(key => {
        if (this.$route.query[key] !== undefined) {
          this.filters[key].value = this.$route.query[key];
          if (!this.isActive(key)) {
            this.active_filters.push(key);
          }
        } else {
          this.filters[key].value = null;
          if (this.active_filters.indexOf(key) !== -1) {
            this.active_filters.splice( this.active_filters.indexOf(key) , 1) ;
          }
        }
      });
    },
    doSearch: function () {
      var queryVals = this.buildFilterQuery();
      this.$router.push({query: queryVals})
    },
    buildFilterQuery: function () {
      var queryVals = {};
      for (var i = 0; i < this.active_filters.length; i++) {
        var filter = this.active_filters[i];
        if (this.filters[filter].value !== null && this.filters[filter].value !== undefined) {

          queryVals[filter] = this.filters[filter].value;
        }
      }

      if (this.$route.query.per_page !== undefined) {
        queryVals.per_page = this.$route.query.per_page;
        this.per_page=this.$route.query.per_page;
      }

      return queryVals;
    },
    nextPage: function () {

      var queryVals = this.buildFilterQuery();
      queryVals.last_key = this.last_key;
      this.$router.push({query: queryVals})
    },
    prevPage: function () {
      var queryVals = this.buildFilterQuery();
      queryVals.first_key = this.first_key;
      this.$router.push({query: queryVals})
    },
    changePerPage: function ($event) {
      var queryVals = this.buildFilterQuery();
      queryVals.per_page = $event.target.value;
      this.per_page=queryVals.per_page;

      if (this.$route.query.last_key !== undefined) {
        queryVals.last_key = this.$route.query.last_key;
      } else if (this.$route.query.first_key !== undefined) {
        queryVals.first_key = this.$route.query.first_key;
      }

      this.$router.push({query: queryVals});
    },
    doStuff: function ()
    {
      this.syncQueryToFilters();
      var mode = 'normal';
      let urlString = '/app/settings/user?';
      if (this.$route.query.last_key !== undefined) {
        urlString = urlString + '&last_key=' + this.$route.query.last_key;
        this.show_back = true;
        mode = 'normal';
      } else if (this.$route.query.first_key !== undefined) {
        urlString = urlString + '&first_key=' + this.$route.query.first_key;
        this.has_more = true;
        mode = 'first_key';
      }

      if (this.$route.query.per_page !== undefined) {
        urlString = urlString + '&per_page=' + this.$route.query.per_page;
      }

      Object.keys(this.filters).forEach(key => {
        if (this.$route.query[key] !== undefined) {
          urlString = urlString + '&'+key+'=' + this.$route.query[key];
        }
      });

      this.loaded = false;
      axios.get(urlString).then(response => {
        this.invites = response.data.invites;
        this.users = response.data.users;
        if (mode === 'normal') {
          this.has_more = response.data.has_more;
        } else {
          this.show_back = response.data.has_more;
          this.has_more = true;
        }
        this.last_key = response.data.last_key;
        this.first_key = response.data.first_key;
        this.ready = true;
        this.loaded = true;
      }).catch(error => {
        this.has_error = true;
      })

    },
    toogle: function (key) {
      var newFilters = [];
      var found = false;
      for (var i = 0; i < this.active_filters.length; i++) {
        if (this.active_filters[i] !== key) {

          newFilters.push(this.active_filters[i]);
        } else {
          found = true;
        }
      }
      if (!found) {
        newFilters.push(key);
      }
      this.active_filters = newFilters;
    },
    isActive: function (key) {
      for (var i = 0; i < this.active_filters.length; i++) {
        if (this.active_filters[i] === key) {
          return true;
        }
      }
      return false;
    }
  }
}
</script>

<style scoped>

</style>
