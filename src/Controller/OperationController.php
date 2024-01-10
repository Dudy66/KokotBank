<?php

namespace App\Controller;

use App\Entity\CompteCourant;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OperationController extends AbstractController
{
    #[Route('/effectuer-depot/{montant}', name: 'effectuer_depot')]
    public function effectuerDepot(float $montant, EntityManagerInterface $entityManager): Response
    {
        // Récupère le Compte Courant depuis la base de données 
        $compteCourant = $entityManager->getRepository(CompteCourant::class);

        if (!$compteCourant) {
            throw $this->createNotFoundException('Aucun compte courant trouvé pour cet ID');
        }

        // Effectuer un dépôt sur le compte
        $compteCourant->deposer($montant);

        // Enregistrer les modifications en base de données
        $entityManager->flush();

        // Répondre avec un message de réussite
        return new Response('Dépôt effectué avec succès');
    }

    #[Route('/effectuer-retrait/{montant}', name: 'effectuer_retrait')]
    public function effectuerRetrait(float $montant, EntityManagerInterface $entityManager): Response
    {
        // Récupère le CompteCourant depuis la base de données 
        $compteCourant = $entityManager->getRepository(CompteCourant::class);

        if (!$compteCourant) {
            throw $this->createNotFoundException('Aucun compte courant trouvé pour cet ID');
        }

        // Effectue un retrait sur le compte
        $retraitEffectue = $compteCourant->retirer($montant);

        if (!$retraitEffectue) {
            // retrait non autorisé
            return new Response('Le retrait n\'a pas pu être effectué (dépassement du découvert autorisé)');
        }
        $entityManager->flush();

        return new Response('Retrait effectué avec succès');
    }
}