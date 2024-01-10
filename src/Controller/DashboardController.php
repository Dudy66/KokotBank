<?php

namespace App\Controller;


use App\Entity\User;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DashboardController extends AbstractController
{
    public function showAccounts(): Response
    {
        // Récupère l'utilisateur connecté
        $user = $this->getUser();

        // Vérifie si l'utilisateur est bien un objet User
        if ($user instanceof User && $user->getId()) {
            // Récupère la collection de comptes courants
            $comptesCourants = $user->getComptesCourants();
            $compteAction = $user->getCompteAction();

            return $this->render('dashboard/accounts.html.twig', [
                'comptesCourants' => $comptesCourants,
                'compteAction' => $compteAction,
                'comptes' => $comptesCourants,

            ]);
        }

        // Gère le cas où l'utilisateur n'existe pas ou n'a pas été trouvé
        return $this->render('error/user_not_found.html.twig');
    }
}
