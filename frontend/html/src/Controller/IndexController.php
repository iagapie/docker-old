<?php

declare(strict_types=1);

namespace App\Controller;

use Docker\API\Model\ContainersCreatePostBody;
use Docker\API\Model\ContainersCreatePostBodyNetworkingConfig;
use Docker\API\Model\EndpointSettings;
use Docker\API\Model\HostConfig;
use Docker\API\Model\PortBinding;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use App\Docker\Docker;

class IndexController extends AbstractController
{
    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * IndexController constructor.
     * @param SerializerInterface $serializer
     */
    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * @Route("/{vueRouting}", requirements={"vueRouting"="^(?!api|_(profiler|wdt)).+"}, name="homepage")
     *
     * @param Request $request
     * @param null|string $vueRouting
     * @return Response
     */
    public function index(Request $request, ?string $vueRouting = null): Response
    {
        $json = $this->serializer->serialize($request->query->all(), 'json');

        return $this->render('base.html.twig', [
            'vueRouting' => \sprintf('/%s', $vueRouting ?? ''),
            'queryParameters' => $json,
        ]);
    }

    public function foo(): Response
    {
        $info = '';

        //$info = '<h1>' . ($this->isPortFree('8084') ? 'TRUE' : 'FALSE') . '</h1>';

    	$docker = Docker::create();

        // $contextBuilder = new ContextBuilder();
        // $contextBuilder->from('ubuntu:latest');
        // $contextBuilder->run('apt-get update');

        // $buildStream = $docker->imageBuild($contextBuilder->getContext()->toStream());
        // $buildStream->onFrame(function (BuildInfo $buildInfo) use(&$info) {
        //     $info .= $buildInfo->getStream() . "<br>";
        // });

        // $buildStream->wait();

        $network = $this->getNetworkId($docker, 'traefik');

        $cammilliName = 'cammilli';
        $lampickName = 'lampick';
        $mariadbName = 'mariadb';
        $phpMyAdminName = 'phpmyadmin';

        $cammilli = $this->getContainerBodyMagento2($cammilliName, $network, '2201');
        $lampick = $this->getContainerBodyMagento2($lampickName, $network, '2202');
        $mariadb = $this->getContainerBodyMariadb($network);
        $phpMyAdmin = $this->getContainerBodyPhpMyAdmin($phpMyAdminName, $network, $mariadbName);

//    	$docker->containerCreate($lampick, ['name' => $lampickName]);
//
//        $docker->containerStart($lampickName);

        $docker->containerCreate($cammilli, ['name' => $cammilliName]);

        $docker->containerStart($cammilliName);

        $json = $this->serializer->serialize($request->query->all(), 'json');

        return $this->render('base.html.twig', [
            'vueRouting' => \sprintf('/%s', $vueRouting ?? ''),
            'queryParameters' => $json,
        ]);

        //$execConfig = new ContainersIdExecPostBody();
        //$docker->containerExec('nginx', $execConfig);

        //$docker->containerRestart('nginx');

        //$docker->containerDelete('test.localhost', ['force' => true]);

//        $networks = $docker->networkList();
//
//        foreach ($networks as $network) {
//            $info .= print_r($network->getId(), true) . '<br>';
//            $info .= print_r($network->getName(), true) . '<br>';
//            foreach ($network->getContainers() as $container) {
//                $info .= print_r($container->getName(), true) . '<br>';
//                $info .= print_r($container->getEndpointID(), true) . '<br>';
//                $info .= '<br><br>';
//            }
//            $info .= '<br><br>';
//        }
//
//        $info .= '<br><br><br><br>';
//
//    	$volumes = $docker->volumeList();
//
//    	foreach ($volumes->getVolumes() as $volume) {
//            $info .= print_r($volume->getName(), true) . '<br>';
//            $info .= print_r($volume->getLabels(), true) . '<br>';
//            $info .= print_r($volume->getMountpoint(), true) . '<br>';
//            $info .= '<br><br>';
//        }
//
//        $info .= '<br><br><br><br>';
//
//		$containers = $docker->containerList(['all' => true]);
//
//		foreach ($containers as $container) {
//		    $info .= print_r($container->getNames(), true) . '<br>';
//		}
//
//        $info .= '<br><br><br><br>';
//
//		$images = $docker->imageList();
//
//		foreach ($images as $image) {
//            $info .= print_r($image->getID(), true) . '<br>';
//            $info .= print_r($image->getLabels(), true) . '<br>';
//            $info .= print_r((array) $image->getContainers(), true) . '<br>';
//            $info .= '<br><br>';
//        }

        return $this->render('index/index.html.twig', [
            'controller_name' => 'IndexController',
            'info' => $info,
        ]);
    }

    protected function isPortFree($port)
    {
        if ($fp = @fsockopen('0.0.0.0', $port, $errno, $errstr, 0.1)) {
            fclose($fp);
            return true;
        }

        return false;
    }

    /**
     * @param string $name
     * @param string $network
     * @param string $sshPort
     * @return ContainersCreatePostBody
     */
    protected function getContainerBodyMagento2(string $name, string $network, string $sshPort): ContainersCreatePostBody
    {
        $pb = new PortBinding();
        $pb->setHostPort($sshPort)->setHostIp('0.0.0.0');

        $hc = new HostConfig();
        $hc->setPortBindings(new \ArrayObject([
            '22/tcp' => [$pb],
        ]));
        $hc->setBinds([
            '/home/iagapie/Workspace/lamp/workspace/' . $name . '.localhost:/var/www/html:rw'
        ]);

        $es = new EndpointSettings();
        $es->setNetworkID($network);

        $nc = new ContainersCreatePostBodyNetworkingConfig();
        $nc->setEndpointsConfig(new \ArrayObject([$es]));

        $cc = new ContainersCreatePostBody();
        $cc
            ->setImage('iagapie/magento2-php70-apache:0.1')
            //->setTty(true)
            //->setExposedPorts(new \ArrayObject(['80/tcp' => new \stdClass]))
            ->setHostConfig($hc)
            ->setNetworkingConfig($nc)
            ->setLabels(new \ArrayObject([
                'traefik.enable' => 'true',
                'traefik.backend' => $name,
                'traefik.port' => '80',
                //'traefik.frontend.rule' => 'Host: ' . $name . '.localhost',
            ]))
        ;

        return $cc;
    }

    /**
     * @param string $name
     * @param string $network
     * @param string $mariadbHost
     * @return ContainersCreatePostBody
     */
    protected function getContainerBodyPhpMyAdmin(string $name, string $network, string $mariadbHost): ContainersCreatePostBody
    {
        $es = new EndpointSettings();
        $es->setNetworkID($network);

        $nc = new ContainersCreatePostBodyNetworkingConfig();
        $nc->setEndpointsConfig(new \ArrayObject([$es]));

        $cc = new ContainersCreatePostBody();
        $cc
            ->setImage('phpmyadmin/phpmyadmin')
            ->setNetworkingConfig($nc)
            ->setEnv([
                'PMA_HOST=' . $mariadbHost,
            ])
            ->setLabels(new \ArrayObject([
                'traefik.enable' => 'true',
                'traefik.backend' => $name,
                'traefik.port' => '80',
            ]))
        ;

        return $cc;
    }

    /**
     * @param string $network
     * @return ContainersCreatePostBody
     */
    protected function getContainerBodyMariadb(string $network): ContainersCreatePostBody
    {
        $es = new EndpointSettings();
        $es->setNetworkID($network);

        $nc = new ContainersCreatePostBodyNetworkingConfig();
        $nc->setEndpointsConfig(new \ArrayObject([$es]));

        $cc = new ContainersCreatePostBody();
        $cc
            ->setImage('mariadb:latest')
            ->setNetworkingConfig($nc)
            ->setEnv([
                'MYSQL_ROOT_PASSWORD=root'
            ])
        ;

        return $cc;
    }

    /**
     * @param Docker $docker
     * @param string $name
     * @return string
     */
    protected function getNetworkId(Docker $docker, string $name): string
    {
        return $docker->networkList(['filters' => json_encode(['name' => [$name]])])[0]->getId();
    }
}
