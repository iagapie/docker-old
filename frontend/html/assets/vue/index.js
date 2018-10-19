import 'babel-polyfill';
import Vue from 'vue';
import Vuetify from 'vuetify';
import App from './App';
import router from './router';
import store from './store';
import axios from 'axios';

axios.defaults.baseURL = location.protocol + '//' + location.hostname + (location.port ? ':' + location.port : '');

Vue.use(Vuetify);

Vue.prototype.$eventHub = new Vue(); // Global event bus

new Vue({
    template: '<app />',
    components: { App },
    router,
    store,
}).$mount('#app');
