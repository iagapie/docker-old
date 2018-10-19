import NetworkAPI from '../api/network';

const FETCHING_NETWORKS = 'FETCHING_NETWORKS';
const FETCHING_NETWORKS_SUCCESS = 'FETCHING_NETWORKS_SUCCESS';
const FETCHING_NETWORKS_ERROR = 'FETCHING_NETWORKS_ERROR';

const INSPECT_NETWORK = 'INSPECT_NETWORK';
const INSPECT_NETWORK_SUCCESS = 'INSPECT_NETWORK_SUCCESS';
const INSPECT_NETWORK_ERROR = 'INSPECT_NETWORK_ERROR';

export default {
    namespaced: true,
    state: {
        isLoading: false,
        error: null,
        networks: [],
    },
    getters: {
        isLoading (state) {
            return state.isLoading;
        },
        hasError (state) {
            return state.error !== null;
        },
        error (state) {
            return state.error;
        },
        hasNetworks (state) {
            return state.networks.length > 0;
        },
        networks (state) {
            return state.networks;
        },
    },
    mutations: {
        [FETCHING_NETWORKS](state) {
            state.isLoading = true;
            state.error = null;
            state.containers = [];
        },
        [FETCHING_NETWORKS_SUCCESS](state, networks) {
            state.isLoading = false;
            state.error = null;
            state.networks = networks;
        },
        [FETCHING_NETWORKS_ERROR](state, error) {
            state.isLoading = false;
            state.error = error;
            state.networks = [];
        },
        [INSPECT_NETWORK](state) {
            state.isLoading = true;
            state.error = null;
            state.networks = [];
        },
        [INSPECT_NETWORK_SUCCESS](state, network) {
            state.isLoading = false;
            state.error = null;
            state.networks = [network];
        },
        [INSPECT_NETWORK_ERROR](state, error) {
            state.isLoading = false;
            state.error = error;
            state.networks = [];
        },
    },
    actions: {
        fetchNetworks ({commit}) {
            commit(FETCHING_NETWORKS);
            return NetworkAPI.getAll()
                .then(res => commit(FETCHING_NETWORKS_SUCCESS, res.data))
                .catch(err => commit(FETCHING_NETWORKS_ERROR, err));
        },
        inspectNetwork ({commit}, id) {
            commit(INSPECT_NETWORK);
            return NetworkAPI.inspect(id)
                .then(res => commit(INSPECT_NETWORK_SUCCESS, res.data))
                .catch(err => commit(INSPECT_NETWORK_ERROR, err));
        },
    },
}
