import VolumeAPI from "../api/volume";
import NetworkAPI from "../api/network";

const FETCHING_VOLUMES = 'FETCHING_VOLUMES';
const FETCHING_VOLUMES_SUCCESS = 'FETCHING_VOLUMES_SUCCESS';
const FETCHING_VOLUMES_ERROR = 'FETCHING_VOLUMES_ERROR';

const INSPECT_VOLUME = 'INSPECT_VOLUME';
const INSPECT_VOLUME_SUCCESS = 'INSPECT_VOLUME_SUCCESS';
const INSPECT_VOLUME_ERROR = 'INSPECT_VOLUME_ERROR';

export default {
    namespaced: true,
    state: {
        isLoading: false,
        error: null,
        volumes: [],
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
        hasVolumes (state) {
            return state.volumes.length > 0;
        },
        volumes (state) {
            return state.volumes;
        },
    },
    mutations: {
        [FETCHING_VOLUMES](state) {
            state.isLoading = true;
            state.error = null;
            state.volumes = [];
        },
        [FETCHING_VOLUMES_SUCCESS](state, volumes) {
            state.isLoading = false;
            state.error = null;
            state.volumes = volumes;
        },
        [FETCHING_VOLUMES_ERROR](state, error) {
            state.isLoading = false;
            state.error = error;
            state.volumes = [];
        },
        [INSPECT_VOLUME](state) {
            state.isLoading = true;
            state.error = null;
            state.volumes = [];
        },
        [INSPECT_VOLUME_SUCCESS](state, volume) {
            state.isLoading = false;
            state.error = null;
            state.volumes = [volume];
        },
        [INSPECT_VOLUME_ERROR](state, error) {
            state.isLoading = false;
            state.error = error;
            state.volumes = [];
        },
    },
    actions: {
        fetchVolumes ({commit}) {
            commit(FETCHING_VOLUMES);
            return VolumeAPI.getAll()
                .then(res => commit(FETCHING_VOLUMES_SUCCESS, res.data))
                .catch(err => commit(FETCHING_VOLUMES_ERROR, err));
        },
        inspectVolume ({commit}, id) {
            commit(INSPECT_VOLUME);
            return VolumeAPI.inspect(id)
                .then(res => commit(INSPECT_VOLUME_SUCCESS, res.data))
                .catch(err => commit(INSPECT_VOLUME_ERROR, err));
        },
    },
}
