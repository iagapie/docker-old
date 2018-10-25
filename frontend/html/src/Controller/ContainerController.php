<?php

declare(strict_types=1);

namespace App\Controller;

use Docker\API\Model\ContainersCreatePostBody;
use Docker\API\Model\ContainersCreatePostBodyNetworkingConfig;
use Docker\API\Model\ContainerSummaryItem;
use Docker\API\Model\DeviceMapping;
use Docker\API\Model\EndpointSettings;
use Docker\API\Model\HostConfig;
use Docker\API\Model\NetworksIdDisconnectPostBody;
use App\Docker\Docker;
use Docker\API\Model\PortBinding;
use Docker\API\Model\RestartPolicy;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/containers")
 */
class ContainerController extends AbstractController
{
    const ACTION_START = 'start';
    const ACTION_RESTART = 'restart';
    const ACTION_STOP = 'stop';

    /**
     * @var Docker
     */
    protected $docker;

    /**
     * ContainerController constructor.
     */
    public function __construct()
    {
        $this->docker = Docker::create();
    }

    /**
     * @Route("/", name="api_container_list")
     */
    public function index(): Response
    {
        $containers = $this->docker->containerList(['all' => true]);

        usort($containers, function(ContainerSummaryItem $a, ContainerSummaryItem $b) {
            $data = [
                'running' => 3,
                'restarting' => 2,
                'exited' => 1,
            ];

            $na = $data[$a->getState()] ?? 0;
            $nb = $data[$b->getState()] ?? 0;

            if ($na === $nb) {
                return 0;
            }

            return $na > $nb ? -1 : 1;
        });

        return $this->json($containers);
    }

    /**
     * @Route("/create", name="api_container_create", methods={"POST"})
     *
     * @param Request $request
     * @return Response
     */
    public function create(Request $request): Response
    {
        $data = $request->request->all();

        if (false === isset($data['hostConfig'])) {
            $data = json_decode($request->getContent(), true);
        }

        $data['hostConfig'] = array_filter($data['hostConfig']);
        $data = array_filter($data);

        $name = str_replace(' ', '-', $data['name']);

        $cc = new ContainersCreatePostBody();
        $cc->setImage($data['image']);
        $cc->setDomainname($data['domainname'] ?? null);
        $cc->setHostname($data['hostname'] ?? null);
        $cc->setUser($data['user'] ?? null);
        $cc->setEnv($data['env'] ?? null);
        $cc->setCmd($data['cmd'] ?? null);
        $cc->setTty($data['tty'] ?? false);
        $cc->setWorkingDir($data['workingDir'] ?? null);
        $cc->setNetworkDisabled($data['networkDisabled'] ?? false);
        $cc->setMacAddress($data['macAddress'] ?? null);
        $cc->setStopSignal($data['stopSignal'] ?? null);
        $cc->setStopTimeout(((int) $data['stopTimeout'] ?? 0) ?: 10);

        if (isset($data['labels'])) {
            $ao = new \ArrayObject();

            foreach ($data['labels'] as $item) {
                $kv = explode('=', $item, 2);

                if (count($kv) === 2) {
                    $ao[$kv[0]] = $kv[1];
                }
            }

            $cc->setLabels($ao);
        }

        if (isset($data['networks'])) {
            $es = array_map(function (string $networkId) {
                $es = new EndpointSettings();
                return $es->setNetworkID($networkId);
            }, $data['networks']);

            $nc = new ContainersCreatePostBodyNetworkingConfig();
            $nc->setEndpointsConfig(new \ArrayObject($es));

            $cc->setNetworkingConfig($nc);
        }

        if (isset($data['hostConfig'])) {
            $hc = new HostConfig();
            $hc->setAutoRemove($data['hostConfig']['autoRemove'] ?? false);
            $hc->setPrivileged($data['hostConfig']['privileged'] ?? false);
            $hc->setBinds($data['hostConfig']['binds'] ?? null);

            if (isset($data['hostConfig']['restartPolicy'])) {
                $rp = new RestartPolicy();
                $hc->setRestartPolicy($rp->setName($data['hostConfig']['restartPolicy']));
            }

            if (isset($data['hostConfig']['portBindings'])) {
                $pbs = new \ArrayObject();

                foreach ($data['hostConfig']['portBindings'] as $item) {
                    $pb = new PortBinding();
                    $pbs[$item['container']] = [$pb->setHostPort($item['host'])->setHostIp('0.0.0.0')];
                }

                $hc->setPortBindings($pbs);
            }

            if (isset($data['hostConfig']['devices'])) {
                $hc->setDevices(array_map(function (array $item) {
                    $item = array_filter($item);
                    $dm = new DeviceMapping();

                    return $dm
                        ->setPathOnHost($item['pathOnHost'])
                        ->setPathInContainer($item['pathInContainer'])
                        ->setCgroupPermissions($item['cgroupPermissions'] ?? null);
                }, $data['hostConfig']['devices']));
            }

            $cc->setHostConfig($hc);
        }

        $this->docker->containerCreate($cc, ['name' => $name]);
        $this->docker->containerStart($name);

        $containers = $this->docker->containerList(['all' => true]);

        foreach ($containers as $container) {
            if ($container->getNames() && substr($container->getNames()[0], 1) === $name) {
                return $this->json($container);
            }
        }

        return $this->json(sprintf('Container %s not found.', $name), Response::HTTP_NOT_FOUND);
    }

    /**
     * @Route("/{id}/inspect", name="api_container_inspect")
     *
     * @param string $id
     * @return Response
     */
    public function inspect(string $id): Response
    {
        return $this->json($this->docker->containerInspect($id));
    }

    /**
     * @Route("/{id}/delete", name="api_container_delete")
     *
     * @param string $id
     * @return Response
     */
    public function delete(string $id): Response
    {
        //$this->disconnectFromNetworks($id);
        $this->docker->containerStop($id);
        $this->docker->containerDelete($id);

        return $this->json($id);
    }

    /**
     * @Route("/{id}/action/{action}", name="api_container_action", requirements={"action"="start|stop|restart"})
     *
     * @param string $id
     * @param string $action
     * @return Response
     */
    public function action(string $id, string $action): Response
    {
        $response = null;

        try {
            switch ($action) {
                case self::ACTION_START:
                    $this->disconnectFromNetworks($id);
                    $this->docker->containerStart($id);
                    break;
                case self::ACTION_RESTART:
                    //$this->disconnectFromNetworks($id);
                    $this->docker->containerRestart($id);
                    break;
                case self::ACTION_STOP:
                    //$this->disconnectFromNetworks($id);
                    $this->docker->containerStop($id);
                    break;
            }
        } catch (\Throwable $e) {
            return $this->json($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }

        $containers = $this->docker->containerList(['all' => true]);

        foreach ($containers as $container) {
            if ($container->getId() === $id) {
                return $this->json($container);
            }
        }

        return $this->json(sprintf('Container %s not found.', $id), Response::HTTP_NOT_FOUND);
    }

    /**
     * @param string $id
     */
    protected function disconnectFromNetworks(string $id): void
    {
        $container = $this->docker->containerInspect($id);

        foreach ($container->getNetworkSettings()->getNetworks() as $name => $network) {
            if (!is_numeric($name)) {
                $body = new NetworksIdDisconnectPostBody();
                $body->setContainer($id);
                $body->setForce(true);

                $this->docker->networkDisconnect($name, $body);
            }
        }
    }
}
