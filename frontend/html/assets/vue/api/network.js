import axios from 'axios';

export default {
    getAll () {
        return axios.get('/api/networks/');
    },
    inspect (id) {
        return axios.get('/api/networks/' + id + '/inspect');
    },
}
