<?php

namespace App\Controller;

use App\Entity\CompteCourant;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;

class CompteCourantController extends AbstractController
{

    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function effectuerVirement(Request $request)
    {
        // Si la requête est une méthode POST
        if ($request->isMethod('GET')) {
            // Récupérer les données du formulaire
            $sourceId = $request->request->get('sourceId');

            $destinataireId = $request->request->get('destinataireId');
            $montant = $request->request->get('montant');

            // Récupérer les instance des comptes depuis la base de données
            $repository = $this->entityManager->getRepository(CompteCourant::class);
            $compteSource = $repository->find($sourceId);
            $compteDestinataire = $repository->find($destinataireId);

            // Si les comptes existent
            if (!$compteSource || !$compteDestinataire) {
                // Gestion d'érreur en cas où l'un des comptes n'éxiste pas
                $this->addFlash('error', 'Comptes introuvables.');
                return $this->redirectToRoute('dashboard_accounts');
            }

            // Effectuer le virement
            if ($compteSource->virementInterne($montant, $compteDestinataire)) {
                $this->addFlash('success', 'Le virement a été effectué avec succès.');
                return $this->redirectToRoute('dashboard_accounts'); // Redirection vers la page de tableau de bord après le virement
            } else {
                $this->addFlash('error', 'Le virement a échoué. Vérifiez vos fonds disponibles.');
                return $this->redirectToRoute('dashboard_accounts'); // Redirection vers la page de tableau de bord en cas d'échec du virement
            }
        }

        // Redirection si le virement échoue
        return $this->redirectToRoute('dashboard_accounts');
    }
}
