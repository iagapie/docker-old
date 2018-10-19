<template>
    <v-app id="inspire">
        <v-toolbar app fixed dark color="primary">
            <v-toolbar-title>Docker Frontend</v-toolbar-title>
            <v-spacer></v-spacer>
            <v-toolbar-items class="hidden-sm-and-down">
                <v-btn flat @click="$router.push({name: 'docker-containers'})">Containers</v-btn>
                <v-btn flat @click="$router.push({name: 'docker-images'})">Images</v-btn>
                <v-btn flat @click="$router.push({name: 'docker-volumes'})">Volumes</v-btn>
                <v-btn flat @click="$router.push({name: 'docker-networks'})">Networks</v-btn>
            </v-toolbar-items>
        </v-toolbar>
        <v-content>
            <v-progress-linear id="top-progress-bar" :active="isLoadingTopProgressBar" :indeterminate="true" color="success" height="4"></v-progress-linear>
            <router-view></router-view>
        </v-content>
        <v-footer class="pa-3" app fixed>
            <v-spacer></v-spacer>
            <div>&copy; {{ new Date().getFullYear() }}</div>
        </v-footer>
    </v-app>
</template>

<script>
    import router from './router';

    export default {
        name: "app",
        data () {
            return {
                isLoadingTopProgressBar: false,
            };
        },
        created () {
            this.$eventHub.$on('top-progress-bar', value => this.isLoadingTopProgressBar = value);
        },
        beforeMount () {
            let vueRouting = this.$parent.$el.attributes['data-vue-routing'].value,
                queryParameters = JSON.parse(this.$parent.$el.attributes['data-query-parameters'].value);
            router.push({path: vueRouting, query: queryParameters});
        },
        beforeDestroy() {
            this.$eventHub.$off('top-progress-bar');
        },
    }
</script>

<style scoped>
    @import "~vuetify/dist/vuetify.min.css";
    @import "~material-design-icons-iconfont/dist/material-design-icons.css";

    #top-progress-bar {
        margin: 0;
    }
</style>
