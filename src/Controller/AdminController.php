<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\AdminEditUserFormType;
use App\Repository\UserRepository;
use App\Service\EmailService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Address;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;

#[IsGranted('ROLE_MODERATOR')]
class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function index(): Response
    {
        // $this->denyAccessUnlessGranted('ROLE_MODERATOR', null, 'Cette page est accessible aux modérateurs');

        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    #[Route('/admin/users', name: 'app_admin_users')]
    public function usersList(UserRepository $userRepository, PaginatorInterface $paginatorInterface, Request $request)
    {

        // Création de la pagination de résultats
        $users = $paginatorInterface->paginate(
            $userRepository->findAll(), // Requête SQL/DQL
            $request->query->getInt('page', 1), // Numérotation des pages
            $request->query->getInt('numbers', 6) // Nombre d'éléments à afficher par page 
        );

        return $this->render('admin/users.html.twig', [
            'users' => $users,
        ]);
    }

    // Modification du role en live avec AJAX (js)
    #[Route('/admin/users/{id}/roles/{role}', name: 'app_admin_role', methods: ['POST'])]
    public function roles(User $user, string $role, UserRepository $userRepository, /* MailerInterface $mailerInterface */ EmailService $emailService): JsonResponse
    {
        $user->setRoles([$role]);
        $userRepository->add($user, true);

        // ------------------------------------------------------------------------------------------ //
        /* // Envoi un mail à l'utilisateur pour le prévenir d'un changement de rôle
        // Email sans template
        // $email = new Email();

        // Email avec template HTML
        $email = new TemplatedEmail();

        // Expéditeur
        $email->from(new Address('no-reply@zines.com'));

        // Destinataire
        $email->to(new Address($user->getEmail(), $user->getLastName(). ' ' .$user->getFirstName()));

        // Objet
        $email->subject('Zines - Changement de rôle utilisateur');

        // Message
        // Contenu sans template
        // $email->text('Bonjour, votre rôle vient d\'être modifié, vous êtes mainetant : '.$role);

        // Contenu avec template. Récupère un fichier template en Twig pour le contenu de l'email
        $email->htmlTemplate('emails/change_role.html.twig');

        // Transfère du contenu venant de la conftion dans le template
        $email->context([
            'username' => $user->getLastName(). ' ' .$user->getFirstName(),
            'role' => $role
        ]);

        // Envoie de l'email 
        $mailerInterface->send($email); */
        // ------------------------------------------------------------------------------------------ //

        // Envoie d'un email en utilisant notre service créé précédement
        $emailService->sendEmail(
            $user->getEmail(), // Destinataire (email)
            'Zines - Changement de rôle', // Sujet de l'email
            [
                'template' => 'emails/change_role.html.twig', // Template du mail à utiliser
                'context' => [ // Les variables à transmettre dans le template
                    'username' => $user->getLastName(). ' ' .$user->getFirstName(),
                    'role' => $role
                ]
            ]
        ); 

        return $this->json(['role' => $role]);
    }

    #[Route('/admin/users/edit/{id}', name: 'app_admin_user_edit', requirements: ['id'=> '\d+'])]
    public function editUser(Request $request, UserRepository $userRepository, User $user)
    {
        $form = $this->createForm(AdminEditUserFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userRepository->add($user, true);

            $this->addFlash('success', 'L\'utilisateur a bien été modifié !');

            return $this->redirectToRoute('app_admin_users');

            $user = new User();
            $form = $this->createForm(AdminEditUserFormType::class, $user);
        }
        
        return $this->render('admin/admin_edit_user.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }
}

