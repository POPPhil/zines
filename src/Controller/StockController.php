<?php

namespace App\Controller;

use App\Repository\StockRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use DateTimeImmutable;

#[IsGranted('ROLE_ADMIN')]
class StockController extends AbstractController
{
    #[Route('/stock', name: 'app_stock')]
    public function index(StockRepository $stockRepository, PaginatorInterface $paginatorInterface, Request $request): Response
    {

        // Création de la pagination de résultats
        $stocks = $paginatorInterface->paginate(
            $stockRepository->findAll(), // Requête SQL/DQL
            $request->query->getInt('page', 1), // Numérotation des pages
            $request->query->getInt('numbers', 6) // Nombre d'éléments à afficher par page 
        );

        return $this->render('stock/index.html.twig', [
            'stocks' => $stocks,
        ]);
    }
}
