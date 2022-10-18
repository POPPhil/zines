<?php

namespace App\Controller;

use App\Entity\Magazine;
use App\Form\MagazineFormType;
use App\Repository\MagazineRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use DateTimeImmutable;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class MagazineController extends AbstractController
{
    #[Route('/magazine/{id}', name: 'app_magazine', requirements: ['id'=> '\d+'])]
    public function index(Magazine $magazine): Response
    {
        return $this->render('magazine/index.html.twig', [
            'magazinebyid' => $magazine,
        ]);
    }

    #[Route('/magazine/new', name: 'app_add_magazine')]
    #[IsGranted('ROLE_MODERATOR')]
    public function magazineAdd(Request $request, MagazineRepository $magazineRepository): Response
    {
        $magazine = new Magazine();

        // Doc pour les formulaires : https://symfony.com/doc/current/forms.html#usage
        // Créer le formulaire
        $form = $this->createForm(MagazineFormType::class, $magazine);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $magazine->setCreatedAt(new DateTimeImmutable());
            $magazineRepository->add($magazine, true);

            $this->addFlash('success', 'Votre magazine a bien été enregistré !');

            return $this->redirectToRoute('app_home');

            $magazine = new Magazine();
            $form = $this->createForm(MagazineFormType::class, $magazine);
        }

        return $this->render('magazine/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/magazine/edit/{id}', name: 'app_edit_magazine', requirements: ['id'=> '\d+'])]
    #[IsGranted('ROLE_MODERATOR')]
    public function magazineEdit(Request $request, MagazineRepository $magazineRepository, Magazine $magazine): Response
    {

        $form = $this->createForm(MagazineFormType::class, $magazine);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $magazineRepository->add($magazine, true);

            $this->addFlash('success', 'Votre magazine a bien été édité !');

            return $this->redirectToRoute('app_home');

            $magazine = new Magazine();
            $form = $this->createForm(MagazineFormType::class, $magazine);
        }

        return $this->render('magazine/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/magazine/suppress/{id}', name: 'app_magazine_suppress', requirements: ['id'=> '\d+'], methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function magazineSuppress(Magazine $magazine, Request $request, MagazineRepository $magazineRepository): RedirectResponse
    {
        $tokenCsrf = $request->request->get('token');
        if ($this->isCsrfTokenValid('delete-magazine-'. $magazine->getId(), $tokenCsrf)){

            $magazineRepository->remove($magazine, true);
            $this->addFlash('suppress', 'Votre magazine a bien été supprimée !');

        }

        return $this->redirectToRoute('app_home');
    }

}
