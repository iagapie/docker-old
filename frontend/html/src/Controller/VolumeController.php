<?php

namespace App\Controller;

use App\Docker\Docker;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/volumes")
 */
class VolumeController extends AbstractController
{
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
     * @Route("/", name="api_volume_list")
     */
    public function index(): Response
    {
        $volumeResponse = $this->docker->volumeList();
        return $this->json($volumeResponse? $volumeResponse->getVolumes() ?: [] : []);
    }

    /**
     * @Route("/{id}/inspect", name="api_volume_inspect")
     *
     * @param string $id
     * @return Response
     */
    public function inspect(string $id): Response
    {
        return $this->json($this->docker->volumeInspect($id));
    }
}
