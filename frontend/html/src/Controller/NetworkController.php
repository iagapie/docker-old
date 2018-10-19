<?php

namespace App\Controller;

use App\Docker\Docker;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/networks")
 */
class NetworkController extends AbstractController
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
     * @Route("/", name="api_network_list")
     * @return Response
     */
    public function index(): Response
    {
        $networks = $this->docker->networkList();
        return $this->json($networks);
    }

    /**
     * @Route("/{id}/inspect", name="api_network_inspect")
     * @param string $id
     * @return Response
     */
    public function inspect(string $id): Response
    {
        return $this->json($this->docker->networkInspect($id, ['verbose' => true]));
    }
}
