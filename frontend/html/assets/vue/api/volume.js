import axios from 'axios';

export default {
    getAll () {
        return axios.get('/api/volumes/');
    },
    inspect (id) {
        return axios.get('/api/volumes/' + id + '/inspect');
    },
}
