<template>
    <v-container fluid grid-list-md>
        <v-layout v-if="isLoading" row wrap>
            <v-flex xs12>
                <h1>Loading...</h1>
            </v-flex>
        </v-layout>

        <v-layout v-else-if="hasContainer" row wrap>
            <v-flex xs12>
                <h1>{{ name }}</h1>
            </v-flex>
        </v-layout>

        <v-snackbar v-model="showSnack" color="error" multi-line vertical bottom right>
            {{ error }}
        </v-snackbar>
    </v-container>
</template>

<script>
    export default {
        name: "docker-container-inspect",
        props: {
            id: {
                type: String,
                required: true,
            },
        },
        data () {
            return {
                dialog: false,
                showSnack: false,
            };
        },
        watch: {
            isLoading: function (newValue) {
                this.$eventHub.$emit('top-progress-bar', newValue);
            },
            hasError: function (newValue) {
                this.showSnack = newValue;
            },
        },
        created () {
            this.$store.dispatch('container/inspectContainer', this.id);
        },
        computed: {
            isLoading () {
                return this.$store.getters['container/isLoading'];
            },
            hasError () {
                return this.$store.getters['container/hasError'];
            },
            error () {
                let error = this.$store.getters['container/error'];

                if (error && 404 == error.response.status) {
                    this.$store.dispatch('container/fetchContainers');
                    return '';
                }

                return error ? error.response.data : '';
            },
            hasContainer () {
                return this.$store.getters['container/hasContainers'];
            },
            container () {
                return this.$store.getters['container/containers'][0];
            },
            name () {
                return this.container.name.slice(1);
            },
        },
    }
</script>

<style scoped>

</style>
