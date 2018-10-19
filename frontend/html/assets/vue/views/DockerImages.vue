<template>
    <v-container fluid grid-list-md>
        <v-layout row wrap>
            <v-flex xs12>
                <h1 class="mb-3">Images</h1>
            </v-flex>
        </v-layout>

        <v-data-table :headers="headers" :items="images" hide-actions class="elevation-1">
            <template slot="items" slot-scope="props">
                <td>{{ shortId(props.item) }}</td>
                <td>{{ shortParentId(props.item) }}</td>
                <td>{{ repoTag(props.item) }}</td>
                <td>{{ createdAt(props.item) }}</td>
                <td>{{ size(props.item) }}</td>
            </template>
        </v-data-table>
    </v-container>
</template>

<script>
    import moment from 'moment';

    export default {
        name: "docker-images",
        data () {
            return {
                headers: [
                    { text: 'Id', value: 'id' },
                    { text: 'Parent Id', value: 'parentId' },
                    { text: 'Repository', value: 'repoTags' },
                    { text: 'Created', value: 'created' },
                    { text: 'Size', value: 'size' },
                ],
            };
        },
        watch: {
            isLoading: function (newValue) {
                this.$eventHub.$emit('top-progress-bar', newValue);
            }
        },
        created () {
            this.$store.dispatch('image/fetchImages');
        },
        computed: {
            isLoading () {
                return this.$store.getters['image/isLoading'];
            },
            hasError () {
                return this.$store.getters['image/hasError'];
            },
            error () {
                return this.$store.getters['image/error'];
            },
            hasImages () {
                return this.$store.getters['image/hasImages'];
            },
            images () {
                return this.$store.getters['image/images'];
            },
        },
        methods: {
            shortId (item) {
                return item.id.slice(7, 19);
            },
            shortParentId (item) {
                return item.parentId.slice(7, 19);
            },
            repoTag (item) {
                if (item.repoTags.length) {
                    return item.repoTags[0];
                }

                return '';
            },
            createdAt (item) {
                return moment.unix(item.created).fromNow();
            },
            size (item) {
                let size = ['B','kB','MB','GB','TB','PB','EB','ZB','YB'];
                let factor = Math.floor(Math.log(item.size) / Math.log(1024));
                return parseFloat((item.size / Math.pow(1024, factor)).toFixed(2)) + " " + size[factor];
            },
        },
    }
</script>
