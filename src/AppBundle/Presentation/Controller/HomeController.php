<?php

namespace AppBundle\Presentation\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class HomeController
 * @package AppBundle\Presentation\Controller
 */
class HomeController extends Controller
{
    /**
     * @Route("/", name="index")
     * @return JsonResponse|Response
     */
    public function indexAction()
    {

        return $this->render('dashboard.html.twig');
    }
}