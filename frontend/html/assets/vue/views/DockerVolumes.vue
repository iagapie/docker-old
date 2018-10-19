<template>
    <v-container fluid grid-list-md>
        <v-layout row wrap>
            <v-flex xs12>
                <h1 class="mb-3">Volumes</h1>
            </v-flex>
        </v-layout>

        <v-data-table :headers="headers" :items="volumes" hide-actions class="elevation-1">
            <template slot="items" slot-scope="props">
                <td>{{ props.item.name }}</td>
                <td>{{ props.item.driver }}</td>
                <td>{{ props.item.scope }}</td>
                <td>{{ createdAt(props.item) }}</td>
                <td>{{ props.item.mountpoint }}</td>
            </template>
        </v-data-table>
    </v-container>
</template>

<script>
    import moment from 'moment';

    export default {
        name: "docker-volumes",
        data () {
            return {
                headers: [
                    { text: 'Name', value: 'name' },
                    { text: 'Driver', value: 'driver' },
                    { text: 'Scope', value: 'scope' },
                    { text: 'Created', value: 'createdAt' },
                    { text: 'Mount Point', value: 'mountpoint' },
                ],
            };
        },
        watch: {
            isLoading: function (newValue) {
                this.$eventHub.$emit('top-progress-bar', newValue);
            }
        },
        created () {
            this.$store.dispatch('volume/fetchVolumes');
        },
        computed: {
            isLoading () {
                return this.$store.getters['volume/isLoading'];
            },
            hasError () {
                return this.$store.getters['volume/hasError'];
            },
            error () {
                return this.$store.getters['volume/error'];
            },
            hasVolumes () {
                return this.$store.getters['volume/hasVolumes'];
            },
            volumes () {
                return this.$store.getters['volume/volumes'];
            },
        },
        methods: {
            createdAt (item) {
                return moment(item.createdAt).format('MMMM Do YYYY, hh:mm:ss');
            },
        },
    }
</script>
