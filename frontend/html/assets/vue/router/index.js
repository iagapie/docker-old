import Vue from 'vue';
import VueRouter from 'vue-router';
import DockerContainers from '../views/DockerContainers';
import DockerContainerInspect from '../views/DockerContainerInspect';
import DockerImages from '../views/DockerImages';
import DockerVolumes from '../views/DockerVolumes';
import DockerVolumeInspect from '../views/DockerVolumeInspect';
import DockerNetworks from '../views/DockerNetworks';
import DockerNetworkInspect from '../views/DockerNetworkInspect';

Vue.use(VueRouter);

export default new VueRouter({
    mode: 'history',
    routes: [
        { path: '/docker-containers', name: 'docker-containers', component: DockerContainers },
        { path: '/docker-containers/:id', name: 'docker-container-inspect', component: DockerContainerInspect, props: true },
        { path: '/docker-images', name: 'docker-images', component: DockerImages },
        { path: '/docker-volumes', name: 'docker-volumes', component: DockerVolumes },
        { path: '/docker-volumes/:id', name: 'docker-volume-inspect', component: DockerVolumeInspect, props: true },
        { path: '/docker-networks', name: 'docker-networks', component: DockerNetworks },
        { path: '/docker-networks/:id', name: 'docker-network-inspect', component: DockerNetworkInspect, props: true },
        { path: '*', redirect: '/docker-containers' },
    ],
});
