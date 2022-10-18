<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryFormType;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

#[IsGranted('ROLE_ADMIN')]
class CategoryController extends AbstractController
{
    #[Route('/category', name: 'app_category')]
    public function index(CategoryRepository $categoryRepository, PaginatorInterface $paginatorInterface, Request $request): Response
    {

        // Création de la pagination de résultats
        $categories = $paginatorInterface->paginate(
            $categoryRepository->findAll(), // Requête SQL/DQL
            $request->query->getInt('page', 1), // Numérotation des pages
            $request->query->getInt('numbers', 5) // Nombre d'éléments à afficher par page 
        );

        return $this->render('category/index.html.twig', [
            'categories' => $categories,
        ]);
    }

    #[Route('/category/new', name: 'app_add_category')]
    public function categoryAdd(Request $request, CategoryRepository $categoryRepository): Response
    {
        $category = new Category();

        // Doc pour les formulaires : https://symfony.com/doc/current/forms.html#usage
        // Créer le formulaire
        $form = $this->createForm(CategoryFormType::class, $category);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $categoryRepository->add($category, true);

            $this->addFlash('success', 'Votre catégorie a bien été enregistrée !');

            $category = new Category();
            $form = $this->createForm(CategoryFormType::class, $category);
        }

        return $this->render('category/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/category/edit/{id}', name: 'app_category_edit', requirements: ['id'=> '\d+'])]
    public function categoryEdit(Request $request, CategoryRepository $categoryRepository, Category $category): Response
    {
        $form = $this->createForm(CategoryFormType::class, $category);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $categoryRepository->add($category, true);

            $this->addFlash('success', 'Votre catégorie a bien été modifiée !');

            return $this->redirectToRoute('app_category');

            $category = new Category();
            $form = $this->createForm(CategoryFormType::class, $category);
        }

        return $this->render('category/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/category/suppress/{id}', name: 'app_category_suppress', requirements: ['id'=> '\d+'], methods: ['POST'])]
    public function categorySuppress(Category $category, Request $request, CategoryRepository $categoryRepository): RedirectResponse
    {
        $tokenCsrf = $request->request->get('token');
        if ($this->isCsrfTokenValid('delete-category-'. $category->getId(), $tokenCsrf)){
            
            $categoryRepository->remove($category, true);
            $this->addFlash('suppress', 'Votre catégorie a bien été supprimée !');

        }

        return $this->redirectToRoute('app_category');
    }
}
