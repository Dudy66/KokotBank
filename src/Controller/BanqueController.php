<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Banque;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BanqueController extends AbstractController
{
    #[Route('/createBanque', name: 'createBanque')]
    public function creerBanque(EntityManagerInterface $entityManager): Response
    {
        $banque = new Banque();
        $banque->setNomBanque('Kokotbank');

        $entityManager->persist($banque);
        $entityManager->flush();

        return new Response('Kokotbank crée avec succès !');
    }

    // affilier les clients a kokotBank
    #[Route('/associer-banque-utilisateurs', name: 'associer_banque_utilisateurs')]
    public function associerBanqueUtilisateurs(ManagerRegistry $managerRegistry): Response
    {
        $entityManager = $managerRegistry->getManager();

        // Récuperela banque depuis la base de données
        $banque = $entityManager->getRepository(Banque::class)->findOneBy(['nom_banque' => 'Kokotbank']);

        // Verifie si la banque existe
        if ($banque) {
            // Récupère tous les utilisateurs
            $users = $entityManager->getRepository(User::class)->findAll();

            // Associe la banque à chaque utilisateur
            foreach ($users as $user) {
                $user->setBanque($banque); //setBanque pour définir la banque
                $entityManager->persist($user);
            }

            // Flush dans la base de données
            $entityManager->flush();
        }

        return new Response('La banque a été affiliée à tous les utilisateurs');
    }
}
