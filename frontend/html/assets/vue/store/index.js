import Vue from 'vue';
import Vuex from 'vuex';
import ContainerModule from './container';
import NetworkModule from './network';
import ImageModule from './image';
import VolumeModule from './volume';

Vue.use(Vuex);

export default new Vuex.Store({
    modules: {
        container: ContainerModule,
        network: NetworkModule,
        image: ImageModule,
        volume: VolumeModule,
    }
});
