<template>
    <v-card>
        <v-progress-linear :indeterminate="loadingId !== null" :color="cardAccent" height="2" value="100"></v-progress-linear>
        <v-card-actions class="m-0 p-0">
            <span class="grey--text p-0 mx-2">{{ createdAt }}</span>
            <v-spacer></v-spacer>
            <v-btn @click="showList = !showList" icon>
                <v-icon>{{ showList ? 'keyboard_arrow_down' : 'keyboard_arrow_up' }}</v-icon>
            </v-btn>
        </v-card-actions>
        <v-card-title class="pt-0">
            <h3 class="headline m-0 font-weight-medium">{{ name }}</h3>
        </v-card-title>
        <v-divider></v-divider>
        <v-slide-y-transition>
            <v-list v-show="showList">
                <v-list-tile>
                    <v-list-tile-content>
                        <v-tooltip top>
                            <v-list-tile-title slot="activator">{{ shortId }}</v-list-tile-title>
                            <span>ID</span>
                        </v-tooltip>
                    </v-list-tile-content>
                </v-list-tile>
                <v-list-tile>
                    <v-list-tile-content>
                        <v-list-tile-title>{{ container.image }}</v-list-tile-title>
                        <v-list-tile-sub-title>Image</v-list-tile-sub-title>
                    </v-list-tile-content>
                </v-list-tile>
                <v-list-tile>
                    <v-list-tile-content>
                        <v-list-tile-title>{{ container.status }}</v-list-tile-title>
                        <v-list-tile-sub-title>Status</v-list-tile-sub-title>
                    </v-list-tile-content>
                </v-list-tile>
                <v-list-tile v-if="ports.length > 0">
                    <v-list-tile-content>
                        <v-list-tile-title>{{ ports }}</v-list-tile-title>
                        <v-list-tile-sub-title>Ports</v-list-tile-sub-title>
                    </v-list-tile-content>
                </v-list-tile>
                <template v-if="container.labels">
                    <v-subheader v-if="Object.keys(container.labels).length">Labels</v-subheader>
                    <v-list-tile v-for="(lv, lk) in container.labels" :key="lk">
                        <v-list-tile-content>
                            <v-list-tile-title>{{ lv }}</v-list-tile-title>
                            <v-list-tile-sub-title>{{ lk }}</v-list-tile-sub-title>
                        </v-list-tile-content>
                    </v-list-tile>
                </template>
            </v-list>
        </v-slide-y-transition>
        <v-divider></v-divider>
        <v-card-actions>
            <v-dialog v-model="showDialogDelete" v-if="showDelete" max-width="490">
                <v-tooltip slot="activator" top>
                    <v-btn slot="activator" icon class="mr-3">
                        <v-icon color="red lighten-2">delete_forever</v-icon>
                    </v-btn>
                    <span>Delete</span>
                </v-tooltip>
                <v-card>
                    <v-card-title class="headline">Delete container <strong> {{ name }}</strong>?</v-card-title>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="grey darken-1" flat @click.native="showDialogDelete = false">Close</v-btn>
                        <v-btn color="red lighten-2" flat @click.native="onDelete">Delete</v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>

            <v-tooltip v-if="showRestart" top>
                <v-btn slot="activator" @click="onAction('restart')" icon class="mr-2">
                    <v-icon color="yellow darken-2">replay</v-icon>
                </v-btn>
                <span>Restart</span>
            </v-tooltip>

            <v-tooltip v-if="showStart" top>
                <v-btn slot="activator" @click="onAction('start')" icon class="mr-2">
                    <v-icon color="green lighten-2">play_arrow</v-icon>
                </v-btn>
                <span>Start</span>
            </v-tooltip>

            <v-tooltip v-if="showStop" top>
                <v-btn slot="activator" @click="onAction('stop')" icon class="mr-2">
                    <v-icon color="grey darken-2">stop</v-icon>
                </v-btn>
                <span>Stop</span>
            </v-tooltip>
            <v-tooltip v-if="showInspect" top>
                <v-btn slot="activator" @click="$router.push({name: 'docker-container-inspect', params: {id: container.id}})" icon class="mr-2">
                    <v-icon color="blue-grey lighten-2">visibility</v-icon>
                </v-btn>
                <span>Inspect</span>
            </v-tooltip>
            <v-spacer></v-spacer>
            <v-btn icon @click="showCommand = !showCommand">
                <v-icon>{{ showCommand ? 'keyboard_arrow_down' : 'keyboard_arrow_up' }}</v-icon>
            </v-btn>
        </v-card-actions>
        <v-divider></v-divider>
        <v-slide-y-transition>
            <v-tooltip top>
                <v-card-text v-show="showCommand" slot="activator">
                    {{ container.command }}
                </v-card-text>
                <span>Command</span>
            </v-tooltip>
        </v-slide-y-transition>
    </v-card>
</template>

<script>
    import moment from 'moment';

    export default {
        name: "docker-container-item",
        data () {
            return {
                loadingId: null,
                showCommand: false,
                showList: false,
                showDialogDelete: false,
            };
        },
        props: {
            container: {
                type: Object,
                required: true,
                default: function () {
                    return {
                        id: '',
                        created: 0,
                        names: [],
                        image: '',
                        state: '',
                        status: '',
                        labels: {},
                        ports: [],
                        command: '',
                    }
                }
            }
        },
        watch: {
            isLoading: function (newValue) {
                if (this.container.id === this.loadingId && !newValue) {
                    this.loadingId = null;
                }
            },
        },
        computed: {
            isLoading () {
                return this.$store.getters['container/isLoading'];
            },
            hasError () {
                return this.$store.getters['container/hasError'];
            },
            error () {
                return this.$store.getters['container/error'];
            },
            hasContainers () {
                return this.$store.getters['container/hasContainers'];
            },
            containers () {
                return this.$store.getters['container/containers'];
            },
            name () {
                return this.container.names[0].slice(1);
            },
            shortId () {
                return this.container.id.slice(0, 12);
            },
            cardAccent () {
                let states = {'running':'success', 'exited':'error', 'restarting':'warning'};
                return states[this.container.state] || 'secondary';
            },
            createdAt () {
                return moment.unix(this.container.created).fromNow();
            },
            ports () {
                if (!this.container.ports) {
                    return '';
                }

                let ports = this.container.ports, data = [];

                for (var i = 0; i < ports.length; ++i) {
                    let port = ports[i], item = [];

                    if (port.iP && port.publicPort) {
                        item.push(port.iP + ':' + port.publicPort);
                    }

                    if (port.privatePort && port.type) {
                        item.push(port.privatePort + '/' + port.type);
                    }

                    data.push(item.join('->'));
                }

                return data.join(', ');
            },
            showStart () {
                return ['running', 'restarting', 'removing', 'dead'].indexOf(this.container.state) === -1;
            },
            showStop () {
                return ['created', 'exited', 'paused', 'removing', 'dead'].indexOf(this.container.state) === -1;
            },
            showRestart () {
                return ['created', 'restarting', 'exited', 'paused', 'removing', 'dead'].indexOf(this.container.state) === -1;
            },
            showDelete () {
                return ['created', 'removing'].indexOf(this.container.state) === -1;
            },
            showInspect () {
                return false; //['removing'].indexOf(this.container.state) === -1;
            },
        },
        methods: {
            onAction (action) {
                this.loadingId = this.container.id;
                this.$store.dispatch('container/actionContainer', {id: this.container.id, action: action})
            },
            onDelete () {
                this.showDialogDelete = false;
                this.loadingId = this.container.id;
                this.$store.dispatch('container/deleteContainer', this.container.id)
            },
        },
    }
</script>
