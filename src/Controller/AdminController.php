<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Entity\User;

class AdminController extends AbstractController
{
    #[Route(path: 'admin/login2', name: 'app_login2')]
    public function login2(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('dashboard_admin');
        }

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('admin/login2', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
    #[Route('admin', name: 'showAdmin')]
    public function showAdmin(): Response
    {
        // Récupère l'utilisateur connecté
        $user = $this->getUser();

        // Vérifie si l'utilisateur est bien un objet User
        // if ($user instanceof User && $user->getId()) {

        return $this->redirectToRoute('admin');
        // }

        // Gère le cas où l'utilisateur n'existe pas ou n'a pas été trouvé
        // return $this->render('error/user_not_found.html.twig');
    }
}
