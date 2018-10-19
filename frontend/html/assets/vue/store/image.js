import ImageAPI from '../api/image';

const FETCHING_IMAGES = 'FETCHING_IMAGES';
const FETCHING_IMAGES_SUCCESS = 'FETCHING_IMAGES_SUCCESS';
const FETCHING_IMAGES_ERROR = 'FETCHING_IMAGES_ERROR';

export default {
    namespaced: true,
    state: {
        isLoading: false,
        error: null,
        images: [],
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
        hasImages (state) {
            return state.images.length > 0;
        },
        images (state) {
            return state.images;
        },
    },
    mutations: {
        [FETCHING_IMAGES](state) {
            state.isLoading = true;
            state.error = null;
            state.images = [];
        },
        [FETCHING_IMAGES_SUCCESS](state, images) {
            state.isLoading = false;
            state.error = null;
            state.images = images;
        },
        [FETCHING_IMAGES_ERROR](state, error) {
            state.isLoading = false;
            state.error = error;
            state.images = [];
        },
    },
    actions: {
        fetchImages ({commit}) {
            commit(FETCHING_IMAGES);
            return ImageAPI.getAll()
                .then(res => commit(FETCHING_IMAGES_SUCCESS, res.data))
                .catch(err => commit(FETCHING_IMAGES_ERROR, err));
        },
    },
}
