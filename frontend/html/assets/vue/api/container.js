import axios from 'axios';

export default {
    getAll () {
        return axios.get('/api/containers/');
    },
    delete (id) {
        return axios.get('/api/containers/' + id + '/delete');
    },
    action (id, action) {
        return axios.get('/api/containers/' + id + '/action/' + action);
    },
    create (data) {
        return axios.post('/api/containers/create', data);
    },
    inspect (id) {
        return axios.get('/api/containers/' + id + '/inspect');
    },
}
