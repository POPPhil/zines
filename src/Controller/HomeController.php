<?php

namespace App\Controller;

use App\Repository\MagazineRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(MagazineRepository $magazineRepository, PaginatorInterface $paginatorInterface, Request $request): Response
    {

        // Création de la pagination de résultats
        $magazines = $paginatorInterface->paginate(
            $magazineRepository->findAll(), // Requête SQL/DQL
            $request->query->getInt('page', 1), // Numérotation des pages
            $request->query->getInt('numbers', 5) // Nombre d'éléments à afficher par page 
        );
   
        return $this->render('home/index.html.twig', [
            'magazines' => $magazines,
        ]);
    }
}
