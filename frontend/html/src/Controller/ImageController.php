<?php

namespace App\Controller;

use App\Docker\Docker;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/images")
 */
class ImageController extends AbstractController
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
     * @Route("/", name="api_image_list")
     */
    public function index(): Response
    {
        $images = $this->docker->imageList(['all' => false]);
        return $this->json($images);
    }
}
