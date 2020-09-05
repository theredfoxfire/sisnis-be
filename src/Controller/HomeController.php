<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class HomeController
 * @package App\Controller
 *
 * @Route(path="/")
 */
class HomeController extends AbstractController
{
    /**
     * @Route("/", name="welcome_home", methods={"GET"})
     */
    public function index(): Response
    {
        $number = random_int(0, 100);

        return $this->render('home/welcome.html.twig', [
            'number' => $number,
        ]);
    }
}
