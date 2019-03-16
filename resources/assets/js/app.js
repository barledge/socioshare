
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');
import MatchHeight from 'matchheight';

window.Vue = require('vue');
alertify.logPosition("top right");

function show_indicator() {
  $('#busy').show();
}

function hide_indicator() {
  $('#busy').hide();
}

Vue.mixin({
  site_url: function (url) {
    return site_url(url);
  }
})
Vue.filter('site_url', function (value) {
  return site_url(value);
})
Vue.filter('asset', function (value) {
  return asset(value);
})
Vue.mixin({
  methods: {
    show_indicator: () => { $('#busy').show() }
  }
})

//global registration
import VueTabs from 'vue-nav-tabs'
import 'vue-nav-tabs/themes/vue-tabs.css'
import 'element-ui/lib/theme-chalk/index.css'

Vue.use(VueTabs)

import Vue from 'vue'
import ElementUI from 'element-ui'
import locale from 'element-ui/lib/locale/lang/en'
Vue.use(ElementUI, { locale })

import * as uiv from 'uiv'
Vue.use(uiv)

import ToggleButton from 'vue-js-toggle-button'
Vue.use(ToggleButton)
/*
import VueProgressBar from 'vue-progressbar'
Vue.use(VueProgressBar, {
  color: 'rgb(143, 255, 199)',
  failedColor: 'red',
  height: '2px'
})
*/
import axios from 'axios';

var numberOfAjaxCAllPending = 0;
// Add a request interceptor
axios.interceptors.request.use(function (config) {
    numberOfAjaxCAllPending++;
    Pace.restart();
    return config;
}, function (error) {
    return Promise.reject(error);
});

// Add a response interceptor
axios.interceptors.response.use(function (response) {
    numberOfAjaxCAllPending--;
    // Do something with response data
    if (numberOfAjaxCAllPending == 0) {
        Pace.stop()
    }
    return response;
}, function (error) {
    numberOfAjaxCAllPending--;
    if (numberOfAjaxCAllPending == 0) {
         Pace.stop()
    }
    return Promise.reject(error);
});

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

Vue.component('account-selector', require('./components/AccountSelector.vue'));
Vue.component('group', require('./components/Group.vue'));
Vue.component('upload', require('./components/Upload.vue'));
Vue.component('review-post', require('./components/ReviewPost.vue'));
Vue.component('DateTime', require('./components/DateTime.vue'));
Vue.component('queue', require('./components/Queue.vue'));
Vue.component('feeds', require('./components/Feeds.vue'));
Vue.component('Schedule', require('./components/Schedule.vue'));
Vue.component('post', require('./components/Post.vue'));
Vue.component('analytics', require('./components/Analytics.vue'));
Vue.component('TextWidget', require('./components/TextWidget.vue'));
Vue.component('edit-post', require('./components/EditPost.vue'));
Vue.component('edit-post-comments', require('./components/EditPostComments.vue'));

const app = new Vue({
    el: '#app'
});




$(function() {
  $('[data-toggle="tooltip"]').tooltip();
  $( ".delete-confirm" ).click(function() {
    event.preventDefault();

    alertify.confirm($(this).data('text'), () => {
      alertify.success('Deleting...');
      axios.delete($(this).data('url'))
          .then((response) => {
              alertify.success('Deleted!');
              window.location.reload();
          })
          .catch((error) => {
              console.log(error);
              alertify.alert(error);
          });


    }, function(){

    });
  });
});
