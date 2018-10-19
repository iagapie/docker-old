<template>
    <v-dialog v-model="showForm" fullscreen scrollable hide-overlay transition="dialog-bottom-transition">
        <v-card>
            <v-toolbar dark color="primary">
                <v-btn icon dark @click.native="cancel">
                    <v-icon>close</v-icon>
                </v-btn>
                <v-toolbar-title>New Container</v-toolbar-title>
                <v-spacer></v-spacer>
                <v-toolbar-items>
                    <v-btn dark flat @click.native="nextStep(e1)">Continue</v-btn>
                    <v-btn dark flat @click.native="submit" :disabled="!valid">Save</v-btn>
                </v-toolbar-items>
            </v-toolbar>
            <v-form ref="form" v-model="valid">
                <v-stepper v-model="e1">
                    <v-stepper-header>
                        <v-stepper-step :complete="e1 > 1" key="1-step" step="1">
                            Base
                        </v-stepper-step>
                        <v-divider key="1"></v-divider>
                        <v-stepper-step :complete="e1 > 2" key="2-step" step="2">
                            Host Config
                        </v-stepper-step>
                        <v-divider key="2"></v-divider>
                        <v-stepper-step :complete="e1 > 3" key="3-step" step="3">
                            Networks
                        </v-stepper-step>
                        <v-divider key="3"></v-divider>
                        <v-stepper-step :complete="e1 > 4" key="4-step" step="4">
                            Commands
                        </v-stepper-step>
                        <v-divider key="4"></v-divider>
                        <v-stepper-step :complete="e1 > 5" key="5-step" step="5">
                            ENV
                        </v-stepper-step>
                        <v-divider key="5"></v-divider>
                        <v-stepper-step :complete="e1 > 6" key="6-step" step="6">
                            Labels
                        </v-stepper-step>
                    </v-stepper-header>

                    <v-stepper-items>
                        <v-stepper-content step="1">
                            <v-container fluid grid-list-md>
                                <v-layout row wrap>
                                    <v-flex xs12 sm6>
                                        <v-text-field
                                                ref="name"
                                                v-model="name"
                                                label="Name *"
                                                :rules="[() => !!name || 'This field is required']"
                                                required
                                                box
                                                clearable>
                                        </v-text-field>
                                    </v-flex>

                                    <v-flex xs12 sm6>
                                        <v-autocomplete
                                                ref="image"
                                                v-model="image"
                                                label="Image *"
                                                :rules="[() => !!image || 'This field is required']"
                                                required
                                                :items="images"
                                                box
                                                clearable
                                                hide-no-data>
                                        </v-autocomplete>
                                    </v-flex>

                                    <v-flex xs12 sm6>
                                        <v-text-field
                                                ref="hostname"
                                                v-model="hostname"
                                                label="Hostname"
                                                hint="The hostname to use for the container, as a valid RFC 1123 hostname."
                                                persistent-hint
                                                box
                                                clearable>
                                        </v-text-field>
                                    </v-flex>

                                    <v-flex xs12 sm6>
                                        <v-text-field
                                                ref="domainname"
                                                v-model="domainname"
                                                label="Domain name"
                                                hint="The domain name to use for the container."
                                                persistent-hint
                                                box
                                                clearable>
                                        </v-text-field>
                                    </v-flex>

                                    <v-flex xs12 sm6>
                                        <v-text-field
                                                ref="user"
                                                v-model="user"
                                                label="User"
                                                hint="The user that commands are run as inside the container."
                                                persistent-hint
                                                box
                                                clearable>
                                        </v-text-field>
                                    </v-flex>

                                    <v-flex xs12 sm6>
                                        <v-text-field
                                                ref="workingDir"
                                                v-model="workingDir"
                                                label="Working dir"
                                                hint="The working directory for commands to run in."
                                                persistent-hint
                                                box
                                                clearable>
                                        </v-text-field>
                                    </v-flex>

                                    <v-flex xs12 sm6>
                                        <v-text-field
                                                ref="macAddress"
                                                v-model="macAddress"
                                                label="Mac address"
                                                hint="MAC address of the container."
                                                persistent-hint
                                                box
                                                clearable>
                                        </v-text-field>
                                    </v-flex>

                                    <v-flex xs12 sm6>
                                        <v-text-field
                                                ref="stopSignal"
                                                v-model="stopSignal"
                                                label="Stop signal"
                                                hint="Signal to stop a container as a string or unsigned integer."
                                                persistent-hint
                                                box
                                                clearable>
                                        </v-text-field>
                                    </v-flex>

                                    <v-flex xs12 sm6>
                                        <v-switch
                                                ref="tty"
                                                v-model="tty"
                                                label="TTY"
                                                hint="Attach standard streams to a TTY, including `stdin` if it is not closed."
                                                persistent-hint
                                                color="primary">
                                        </v-switch>
                                    </v-flex>

                                    <v-flex xs12 sm6>
                                        <v-switch
                                                ref="networkDisabled"
                                                v-model="networkDisabled"
                                                label="Network disabled"
                                                hint="Disable networking for the container."
                                                persistent-hint
                                                color="primary">
                                        </v-switch>
                                    </v-flex>
                                </v-layout>
                            </v-container>
                        </v-stepper-content>

                        <v-stepper-content step="2">
                            <v-container fluid grid-list-md>
                                <v-layout row wrap>
                                    <v-flex xs12 sm12>
                                        <h5 class="headline mb-2">A list of volume bindings for this container. Each volume binding is a string in one of these forms:</h5>
                                        <p class="mb-2">
                                            `host-src:container-dest` to bind-mount a host path into the container. Both `host-src`, and `container-dest` must be an _absolute_ path.<br>
                                            `host-src:container-dest:ro` to make the bind mount read-only inside the container. Both `host-src`, and `container-dest` must be an _absolute_ path.<br>
                                            `volume-name:container-dest` to bind-mount a volume managed by a volume driver into the container. `container-dest` must be an _absolute_ path.<br>
                                            `volume-name:container-dest:ro` to mount the volume read-only inside the container.  `container-dest` must be an _absolute_ path.
                                        </p>
                                        <v-text-field
                                                v-for="i in hostConfig.binds.length"
                                                :key="`${i}-bind`"
                                                ref="hostConfig.binds[i-1]"
                                                v-model="hostConfig.binds[i-1]"
                                                :label="`#${i} Bind`"
                                                :rules="[() => !!hostConfig.binds[i-1] || 'This field is required']"
                                                required
                                                box
                                                clearable
                                                append-outer-icon="delete"
                                                @click:append-outer="() => hostConfig.binds.splice(i-1, 1)">
                                        </v-text-field>
                                        <v-btn class="ml-0" color="primary light-1" @click.native="() => hostConfig.binds.push('')">Add Bind Volume</v-btn>
                                    </v-flex>

                                    <v-flex xs12 sm12>
                                        <h5 class="headline mt-4 mb-2">A list of port bindings for this container.</h5>
                                        <p class="mb-2">
                                            If a container's port is mapped for both `tcp` and `udp`, two separate
                                            entries are added to the mapping table.
                                        </p>
                                        <v-layout row wrap>
                                            <template v-for="i in hostConfig.portBindings.length">
                                                <v-flex xs5 sm6>
                                                    <v-text-field
                                                            ref="hostConfig.portBindings[i-1].host"
                                                            v-model="hostConfig.portBindings[i-1].host"
                                                            :label="`#${i} Host Port`"
                                                            :rules="[() => !!hostConfig.portBindings[i-1].host || 'This field is required']"
                                                            required
                                                            box
                                                            clearable>
                                                    </v-text-field>
                                                </v-flex>
                                                <v-flex xs7 sm6>
                                                    <v-text-field
                                                            ref="hostConfig.portBindings[i-1].container"
                                                            v-model="hostConfig.portBindings[i-1].container"
                                                            :label="`#${i} Container Port`"
                                                            :rules="[() => !!hostConfig.portBindings[i-1].container || 'This field is required']"
                                                            hint="Format `port/protocol`, for example: `80/tcp`."
                                                            persistent-hint
                                                            required
                                                            box
                                                            clearable
                                                            append-outer-icon="delete"
                                                            @click:append-outer="() => hostConfig.portBindings.splice(i-1, 1)">
                                                    </v-text-field>
                                                </v-flex>
                                            </template>
                                        </v-layout>
                                        <v-btn class="ml-0 mt-0" color="primary light-1" @click.native="() => hostConfig.portBindings.push({host: '', container: ''})">Add Port Bind</v-btn>
                                    </v-flex>

                                    <v-flex xs12 sm12>
                                        <h5 class="headline mt-4 mb-2">A list of devices to add to the container.</h5>
                                        <v-layout row wrap>
                                            <template v-for="i in hostConfig.devices.length">
                                                <v-flex xs12 sm12 xl4 lg4>
                                                    <v-text-field
                                                            ref="hostConfig.devices[i-1].pathOnHost"
                                                            v-model="hostConfig.devices[i-1].pathOnHost"
                                                            :label="`#${i} Path on host`"
                                                            :rules="[() => !!hostConfig.devices[i-1].pathOnHost || 'This field is required']"
                                                            required
                                                            box
                                                            clearable>
                                                    </v-text-field>
                                                </v-flex>
                                                <v-flex xs12 sm12 xl4 lg4>
                                                    <v-text-field
                                                            ref="hostConfig.devices[i-1].pathInContainer"
                                                            v-model="hostConfig.devices[i-1].pathInContainer"
                                                            :label="`#${i} Path in container`"
                                                            :rules="[() => !!hostConfig.devices[i-1].pathInContainer || 'This field is required']"
                                                            required
                                                            box
                                                            clearable>
                                                    </v-text-field>
                                                </v-flex>
                                                <v-flex xs12 sm12 xl4 lg4>
                                                    <v-text-field
                                                            ref="hostConfig.devices[i-1].cgroupPermissions"
                                                            v-model="hostConfig.devices[i-1].cgroupPermissions"
                                                            :label="`#${i} Container group`"
                                                            box
                                                            clearable
                                                            append-outer-icon="delete"
                                                            @click:append-outer="() => hostConfig.devices.splice(i-1, 1)">
                                                    </v-text-field>
                                                </v-flex>
                                            </template>
                                        </v-layout>
                                        <v-btn class="ml-0 mt-0" color="primary light-1" @click.native="() => hostConfig.devices.push({pathOnHost: '', pathInContainer: '', cgroupPermissions: ''})">Add Device</v-btn>
                                    </v-flex>

                                    <v-flex xs12 sm12>
                                        <v-autocomplete
                                                class="mt-5"
                                                ref="hostConfig.restartPolicy"
                                                v-model="hostConfig.restartPolicy"
                                                label="Restart policy"
                                                :items="restartPolicy"
                                                item-text="name"
                                                item-value="value"
                                                hint="The behavior to apply when the container exits. Empty is not to restart."
                                                persistent-hint
                                                box
                                                clearable
                                                hide-no-data>
                                        </v-autocomplete>
                                    </v-flex>

                                    <v-flex xs12 sm6>
                                        <v-switch
                                                ref="hostConfig.autoRemove"
                                                v-model="hostConfig.autoRemove"
                                                label="Auto remove"
                                                hint="Automatically remove the container when the container's process exits. This has no effect if `Restart policy` is set."
                                                persistent-hint
                                                color="primary">
                                        </v-switch>
                                    </v-flex>

                                    <v-flex xs12 sm6>
                                        <v-switch
                                                ref="hostConfig.privileged"
                                                v-model="hostConfig.privileged"
                                                label="Privileged"
                                                hint="Gives the container full access to the host."
                                                persistent-hint
                                                color="primary">
                                        </v-switch>
                                    </v-flex>
                                </v-layout>
                            </v-container>
                        </v-stepper-content>

                        <v-stepper-content step="3">
                            <v-container fluid grid-list-md>
                                <v-layout row wrap>
                                    <v-flex v-for="i in networks.length" xs12 sm12 :key="`${i}-network`">
                                        <v-autocomplete
                                                ref="networks[i-1]"
                                                v-model="networks[i-1]"
                                                :label="`#${i} Network`"
                                                :rules="[() => !!networks[i-1] || 'This field is required']"
                                                required
                                                :items="networkList"
                                                item-text="name"
                                                item-value="value"
                                                box
                                                clearable
                                                hide-no-data
                                                append-outer-icon="delete"
                                                @click:append-outer="() => networks.splice(i-1, 1)">
                                        </v-autocomplete>
                                    </v-flex>
                                    <v-btn class="ml-1" color="primary light-1" @click.native="() => networks.push('')">Add Network</v-btn>
                                </v-layout>
                            </v-container>
                        </v-stepper-content>

                        <v-stepper-content step="4">
                            <v-container fluid grid-list-md>
                                <v-layout row wrap>
                                    <v-flex xs12 sm12>
                                        <h5 class="headline mb-2">Commands to run specified as a string or an array of strings.</h5>
                                        <v-text-field
                                                v-for="i in cmd.length"
                                                :key="`${i}-cmd`"
                                                ref="cmd[i-1]"
                                                v-model="cmd[i-1]"
                                                :label="`#${i} Command`"
                                                :rules="[() => !!cmd[i-1] || 'This field is required']"
                                                required
                                                box
                                                clearable
                                                append-outer-icon="delete"
                                                @click:append-outer="() => cmd.splice(i-1, 1)">
                                        </v-text-field>
                                        <v-btn class="ml-0" color="primary light-1" @click.native="() => cmd.push('')">Add Command</v-btn>
                                    </v-flex>
                                </v-layout>
                            </v-container>
                        </v-stepper-content>

                        <v-stepper-content step="5">
                            <v-container fluid grid-list-md>
                                <v-layout row wrap>
                                    <v-flex xs12 sm12>
                                        <h5 class="headline mb-2">A list of environment variables to set inside the container:</h5>
                                        <p class="mb-2">
                                            A variable without `=` is removed from the environment, rather than to have an empty value.
                                        </p>
                                        <v-text-field
                                                v-for="i in env.length" :key="`${i}-env`"
                                                ref="env[i-1]"
                                                v-model="env[i-1]"
                                                :label="`#${i} Env`"
                                                :rules="[() => !!env[i-1] || 'This field is required']"
                                                hint="VAR=value"
                                                persistent-hint
                                                required
                                                box
                                                clearable
                                                append-outer-icon="delete"
                                                @click:append-outer="() => env.splice(i-1, 1)">
                                        </v-text-field>
                                        <v-btn class="ml-0" color="primary light-1" @click.native="() => env.push('')">Add Env</v-btn>
                                    </v-flex>
                                </v-layout>
                            </v-container>
                        </v-stepper-content>

                        <v-stepper-content step="6">
                            <v-container fluid grid-list-md>
                                <v-layout row wrap>
                                    <v-flex xs12 sm12>
                                        <h5 class="headline mb-2">User-defined key/value metadata:</h5>
                                        <v-text-field
                                                v-for="i in labels.length"
                                                :key="`${i}-label`"
                                                ref="labels[i-1]"
                                                v-model="labels[i-1]"
                                                :label="`#${i} Label`"
                                                :rules="[() => !!labels[i-1] || 'This field is required']"
                                                hint="key=value"
                                                persistent-hint
                                                required
                                                box
                                                clearable
                                                append-outer-icon="delete"
                                                @click:append-outer="() => labels.splice(i-1, 1)">
                                        </v-text-field>
                                        <v-btn class="ml-0" color="primary light-1" @click.native="() => labels.push('')">Add Label</v-btn>
                                    </v-flex>
                                </v-layout>
                            </v-container>
                        </v-stepper-content>
                    </v-stepper-items>
                </v-stepper>
            </v-form>
        </v-card>
    </v-dialog>
</template>

<script>
    export default {
        name: "docker-container-form",
        data() {
            return {
                name: '',
                hostname: '',
                domainname: '',
                user: '',
                env: [],
                cmd: [],
                tty: false,
                image: '',
                workingDir: '',
                networkDisabled: false,
                macAddress: '',
                labels: [],
                stopSignal: '',
                stopTimeout: 10,
                hostConfig: {
                    binds: [],
                    portBindings: [],
                    restartPolicy: '',
                    autoRemove: false,
                    privileged: false,
                    devices: [],
                },
                networks: [''],
                valid: false,
                e1: 1,
                restartPolicy: [
                    {
                        value: 'always',
                        name: 'Always restart',
                    },
                    {
                        value: 'unless-stopped',
                        name: 'Restart always except when the user has manually stopped the container',
                    },
                    {
                        value: 'on-failure',
                        name: 'Restart only when the container exit code is non-zero',
                    }
                ],
            };
        },
        props: {
            showForm: {
                type: Boolean,
                required: true,
                default: false,
            },
        },
        watch: {
            isLoading: function (newValue, oldValue) {
                if (newValue !== oldValue) {
                    this.cancel();
                }
            }
        },
        created() {
            this.$store.dispatch('image/fetchImages');
            this.$store.dispatch('network/fetchNetworks');
        },
        computed: {
            isLoading () {
                return this.$store.getters['container/isLoading'];
            },
            form() {
                return {
                    name: this.name,
                    hostname: this.hostname,
                    domainname: this.domainname,
                    user: this.user,
                    env: this.env,
                    cmd: this.cmd,
                    tty: this.tty,
                    image: this.image,
                    workingDir: this.workingDir,
                    networkDisabled: this.networkDisabled,
                    macAddress: this.macAddress,
                    labels: this.labels,
                    stopSignal: this.stopSignal,
                    stopTimeout: this.stopTimeout,
                    hostConfig: this.hostConfig,
                    networks: this.networks,
                };
            },
            images() {
                return this.$store.getters['image/images'].map(entry => entry.repoTags[0]);
            },
            networkList () {
                return this.$store.getters['network/networks'].map(entry => {
                    return {
                        name: entry.name,
                        value: entry.id,
                    };
                });
            },
        },
        methods: {
            cancel() {
                this.resetForm();
                this.$emit('update:showForm', false);
            },
            submit() {
                if (this.$refs.form.validate()) {
                    this.$store.dispatch('container/createContainer', this.form);
                }
            },
            resetForm() {
                this.$refs.form.reset();
                Object.assign(this.$data, this.$options.data());
            },
            nextStep (n) {
                this.e1 = n === 6 ? 1 : n + 1;
            },
        },
    }
</script>
