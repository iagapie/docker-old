import ContainerAPI from '../api/container';
import NetworkAPI from "../api/network";

const FETCHING_CONTAINERS = 'FETCHING_CONTAINERS';
const FETCHING_CONTAINERS_SUCCESS = 'FETCHING_CONTAINERS_SUCCESS';
const FETCHING_CONTAINERS_ERROR = 'FETCHING_CONTAINERS_ERROR';

const DELETE_CONTAINER = 'DELETE_CONTAINER';
const DELETE_CONTAINER_SUCCESS = 'DELETE_CONTAINER_SUCCESS';
const DELETE_CONTAINER_ERROR = 'DELETE_CONTAINER_ERROR';

const CREATE_CONTAINER = 'CREATE_CONTAINER';
const CREATE_CONTAINER_SUCCESS = 'CREATE_CONTAINER_SUCCESS';
const CREATE_CONTAINER_ERROR = 'CREATE_CONTAINER_ERROR';

const ACTION_CONTAINER = 'ACTION_CONTAINER';
const ACTION_CONTAINER_SUCCESS = 'ACTION_CONTAINER_SUCCESS';
const ACTION_CONTAINER_ERROR = 'ACTION_CONTAINER_ERROR';

const INSPECT_CONTAINER = 'INSPECT_CONTAINER';
const INSPECT_CONTAINER_SUCCESS = 'INSPECT_CONTAINER_SUCCESS';
const INSPECT_CONTAINER_ERROR = 'INSPECT_CONTAINER_ERROR';

export default {
    namespaced: true,
    state: {
        isLoading: false,
        error: null,
        containers: [],
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
        hasContainers (state) {
            return state.containers.length > 0;
        },
        containers (state) {
            return state.containers;
        },
    },
    mutations: {
        [FETCHING_CONTAINERS](state) {
            state.isLoading = true;
            state.error = null;
            state.containers = [];
        },
        [FETCHING_CONTAINERS_SUCCESS](state, containers) {
            state.isLoading = false;
            state.error = null;
            state.containers = containers;
        },
        [FETCHING_CONTAINERS_ERROR](state, error) {
            state.isLoading = false;
            state.error = error;
            state.containers = [];
        },
        [DELETE_CONTAINER](state) {
            state.isLoading = true;
            state.error = null;
        },
        [DELETE_CONTAINER_SUCCESS](state, id) {
            state.isLoading = false;
            state.error = null;

            for (var i = 0; i < state.containers.length; ++i) {
                if (state.containers[i].id === id) {
                    state.containers.splice(i, 1);
                    break;
                }
            }
        },
        [DELETE_CONTAINER_ERROR](state, error) {
            state.isLoading = false;
            state.error = error;
        },
        [ACTION_CONTAINER](state) {
            state.isLoading = true;
            state.error = null;
        },
        [ACTION_CONTAINER_SUCCESS](state, container) {
            state.isLoading = false;
            state.error = null;

            let containers = [];

            for (var i = 0; i < state.containers.length; ++i) {
                if (state.containers[i].id === container.id) {
                    if ('removing' === container.state) {
                        continue;
                    }

                    state.containers[i] = container;
                }

                containers.push(Object.assign({}, state.containers[i]));
            }

            state.containers = containers;
        },
        [ACTION_CONTAINER_ERROR](state, error) {
            state.isLoading = false;
            state.error = error;
        },
        [CREATE_CONTAINER](state) {
            state.isLoading = true;
            state.error = null;
        },
        [CREATE_CONTAINER_SUCCESS](state, container) {
            state.isLoading = false;
            state.error = null;
            state.containers.push(container);
        },
        [CREATE_CONTAINER_ERROR](state, error) {
            state.isLoading = false;
            state.error = error;
        },
        [INSPECT_CONTAINER](state) {
            state.isLoading = true;
            state.error = null;
            state.containers = [];
        },
        [INSPECT_CONTAINER_SUCCESS](state, container) {
            state.isLoading = false;
            state.error = null;
            state.containers = [container];
        },
        [INSPECT_CONTAINER_ERROR](state, error) {
            state.isLoading = false;
            state.error = error;
            state.containers = [];
        },
    },
    actions: {
        fetchContainers ({commit}) {
            commit(FETCHING_CONTAINERS);
            return ContainerAPI.getAll()
                .then(res => commit(FETCHING_CONTAINERS_SUCCESS, res.data))
                .catch(err => commit(FETCHING_CONTAINERS_ERROR, err));
        },
        deleteContainer ({commit}, id) {
            commit(DELETE_CONTAINER);
            return ContainerAPI.delete(id)
                .then(res => commit(DELETE_CONTAINER_SUCCESS, res.data))
                .catch(err => commit(DELETE_CONTAINER_ERROR, err));
        },
        actionContainer ({commit}, data) {
            commit(ACTION_CONTAINER);
            return ContainerAPI.action(data.id, data.action)
                .then(res => commit(ACTION_CONTAINER_SUCCESS, res.data))
                .catch(err => commit(ACTION_CONTAINER_ERROR, err));
        },
        createContainer ({commit}, data) {
            commit(CREATE_CONTAINER);
            return ContainerAPI.create(data)
                .then(res => commit(CREATE_CONTAINER_SUCCESS, res.data))
                .catch(err => commit(CREATE_CONTAINER_ERROR, err));
        },
        inspectContainer ({commit}, id) {
            commit(INSPECT_CONTAINER);
            return ContainerAPI.inspect(id)
                .then(res => commit(INSPECT_CONTAINER_SUCCESS, res.data))
                .catch(err => commit(INSPECT_CONTAINER_ERROR, err));
        },
    },
}
