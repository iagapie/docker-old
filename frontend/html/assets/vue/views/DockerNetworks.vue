<template>
    <v-container fluid grid-list-md>
        <v-layout row wrap>
            <v-flex xs12>
                <h1 class="mb-3">Networks</h1>
            </v-flex>
        </v-layout>

        <v-data-table :headers="headers" :items="networks" hide-actions class="elevation-1">
            <template slot="items" slot-scope="props">
                <td>{{ shortId(props.item) }}</td>
                <td>{{ props.item.name }}</td>
                <td>{{ props.item.scope }}</td>
                <td>{{ props.item.driver }}</td>
                <td>{{ createdAt(props.item) }}</td>
                <td>
                    <v-list v-if="props.item.iPAM" dense class="transparent">
                        <v-list-tile>
                            <v-list-tile-content>
                                <v-list-tile-title>Driver</v-list-tile-title>
                                <v-list-tile-sub-title>{{ props.item.iPAM.driver }}</v-list-tile-sub-title>
                            </v-list-tile-content>
                        </v-list-tile>

                        <template v-if="props.item.iPAM.config" v-for="config in props.item.iPAM.config">
                            <v-list-tile v-if="config.Subnet">
                                <v-list-tile-content>
                                    <v-list-tile-title>Subnet</v-list-tile-title>
                                    <v-list-tile-sub-title>{{ config.Subnet }}</v-list-tile-sub-title>
                                </v-list-tile-content>
                            </v-list-tile>

                            <v-list-tile v-if="config.Gateway">
                                <v-list-tile-content>
                                    <v-list-tile-title>Gateway</v-list-tile-title>
                                    <v-list-tile-sub-title>{{ config.Gateway }}</v-list-tile-sub-title>
                                </v-list-tile-content>
                            </v-list-tile>
                        </template>
                    </v-list>
                </td>
            </template>
        </v-data-table>
    </v-container>
</template>

<script>
    import moment from 'moment';

    export default {
        name: "docker-networks",
        data () {
            return {
                headers: [
                    { text: 'Id', value: 'id' },
                    { text: 'Name', value: 'name' },
                    { text: 'Scope', value: 'scope' },
                    { text: 'Driver', value: 'driver' },
                    { text: 'Created', value: 'created' },
                    { text: 'IPAM', value: 'iPAM' },
                ],
            };
        },
        watch: {
            isLoading: function (newValue) {
                this.$eventHub.$emit('top-progress-bar', newValue);
            }
        },
        created () {
            this.$store.dispatch('network/fetchNetworks');
        },
        computed: {
            isLoading () {
                return this.$store.getters['network/isLoading'];
            },
            hasError () {
                return this.$store.getters['network/hasError'];
            },
            error () {
                return this.$store.getters['network/error'];
            },
            hasNetworks () {
                return this.$store.getters['network/hasNetworks'];
            },
            networks () {
                return this.$store.getters['network/networks'];
            },
        },
        methods: {
            shortId (item) {
                return item.id.slice(7, 19);
            },
            createdAt (item) {
                return moment(item.created).format('MMMM Do YYYY, hh:mm:ss');
            },
        },
    }
</script>

<style scoped>

</style>
