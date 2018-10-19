<template>
    <div id="docker-containers">
        <v-container fluid grid-list-md class="mb-4">
            <v-layout row wrap>
                <v-flex xs12>
                    <h1>Containers</h1>
                </v-flex>
            </v-layout>

            <v-layout row wrap class="mb-5">
                <v-flex v-for="container in containers" v-if="container.names && container.names.length" :key="container.id" xs12 sm6 md4 lg3>
                    <docker-container-item :container="container"></docker-container-item>
                </v-flex>
            </v-layout>

            <v-snackbar v-model="showSnack" color="error" multi-line vertical bottom right>
                {{ error }}
            </v-snackbar>
        </v-container>

        <v-btn dark fab fixed bottom right @click="showForm = true" color="primary accent-2" class="mb-2">
            <v-icon>add</v-icon>
        </v-btn>

        <docker-container-form :showForm.sync="showForm"></docker-container-form>
    </div>
</template>

<script>
    import DockerContainerItem from './DockerContainerItem';
    import DockerContainerForm from './DockerContainerForm';

    export default {
        name: "docker-containers",
        data () {
            return {
                showSnack: false,
                showForm: false,
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
        components: {
            DockerContainerItem,
            DockerContainerForm,
        },
        created () {
            this.$store.dispatch('container/fetchContainers');
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
            hasContainers () {
                return this.$store.getters['container/hasContainers'];
            },
            containers () {
                return this.$store.getters['container/containers'];
            },
        },
    }
</script>

<style>
    #docker-containers .v-btn--floating {
        position: absolute;
    }
</style>
