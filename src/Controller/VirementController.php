<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Operation;
use App\Entity\CompteAction;
use App\Entity\CompteCourant;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class VirementController extends AbstractController
{
    #######################     Méthode pour Afficher le formulaire de virement externe    #####################
    public function afficherFormulaireVirement(EntityManagerInterface $entityManager): Response
    {
        // Récupération des comptes courants de l'utilisateur connecté
        $utilisateurConnecte = $this->getUser();
        if ($utilisateurConnecte instanceof User) {
            $comptesUtilisateur = $utilisateurConnecte->getComptesCourants();
        } else {
            $comptesUtilisateur = []; // Initialisation si l'utilisateur n'est pas connecté ou n'a pas de comptes courants
        }

        $nomsUtilisateurs = [];

        // Récupération de tous les utilisateurs de la banque avec leurs comptes courants
        $utilisateurs = $entityManager->getRepository(User::class)->findAll();

        foreach ($utilisateurs as $user) {
            foreach ($user->getComptesCourants() as $compte) {
                $nomsUtilisateurs[$user->getId()] = $user->getNomComplet();
            }
        }

        return $this->render('virement/formulaire_virement.html.twig', [
            'comptesUtilisateur' => $comptesUtilisateur,
            'nomsUtilisateurs' => $nomsUtilisateurs,
        ]);
    }

    #######################     Méthode pour effectuer un Virement externe     #####################
    public function effectuerVirement(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Récupération des données du formulaire
        $compteSourceId = $request->request->get('compteSourceId');
        $compteDestinationId = $request->request->get('compteDestinationId');
        $montant = $request->request->get('montant');
        // Obtention des instances de CompteCourant en utilisant leurs ID
        $compteSource = $entityManager->getRepository(CompteCourant::class)->find($compteSourceId);
        $compteDestination = $entityManager->getRepository(CompteCourant::class)->find($compteDestinationId);
        // Vérification des comptes et solde suffisant pour le virement
        if ($compteSource && $compteDestination && $compteSource !== $compteDestination) {
            if ($compteSource->getSolde() >= $montant) {
                // Déduit le montant du compte source
                $compteSource->retirer($montant);
                // Ajout du montant au compte destination
                $compteDestination->ajouter($montant);
                // Récupération de l'utilisateur associé au compte destination
                $userDestination = $compteDestination->getUser();
                // Enregistrement des modifications des comptes
                $entityManager->persist($compteSource);
                $entityManager->persist($compteDestination);
                $entityManager->flush();
                // Création et enregistrement de l'opération
                $operation = new Operation();
                $operation->setTypeOperation('Virement externe');
                $operation->setMontant($montant);
                $operation->setDate(new \DateTime());
                $operation->setLibelle('Virement de ' . $montant . ' euros depuis ' . $compteSource->getNumeroCompte() . ' vers ' . $userDestination->getNomComplet() . ' ' . $compteDestination->getNumeroCompte());
                $operation->setCompteCourant($compteSource);
                $entityManager->persist($operation);
                $entityManager->flush();
                return $this->redirectToRoute('dashboard_accounts');
            } else {
                return $this->render('error.html.twig', ['message' => 'Solde insuffisant pour effectuer ce virement !']);
            }
        } else {
            return $this->render('error.html.twig', ['message' => 'Les comptes sélectionnés sont invalides !']);
        }
    }
    #######################     Méthode pour effectuer un Virement Interne    ############################
    public function afficherFormulaireVirementInterne(): Response
    {
        $utilisateurConnecte = $this->getUser();
        if ($utilisateurConnecte instanceof User) {
            $comptesCourants = $utilisateurConnecte->getComptesCourants();
            $compteAction = $utilisateurConnecte->getCompteAction(); // Récupération du compte action
            // Autres manipulations nécessaires ici
            return $this->render('virement/formulaire_virement_interne.html.twig', [
                'comptesCourants' => $comptesCourants,
                'compteActions' => [$compteAction], // Passage du compte action dans un tableau
            ]);
        }
        return $this->redirectToRoute('dashboard');
    }
    public function virementCourantVersAction(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Récupération des données du formulaire
        $compteSourceId = $request->request->get('compteSourceId');
        $compteDestinationId = $request->request->get('compteDestinationId');
        $montant = $request->request->get('montant');

        $compteSource = $entityManager->getRepository(CompteCourant::class)->find($compteSourceId);
        $compteDestination = $entityManager->getRepository(CompteAction::class)->find($compteDestinationId);

        if ($compteSource instanceof CompteCourant && $compteDestination instanceof CompteAction) {
            // Vérification du plafond du compte destination
            if ($compteDestination->getSolde() + $montant > $compteDestination->getPlafond()) {
                return $this->render('error.html.twig', ['message' => 'Le plafond du compte destination serait dépassé !']);
            }

            // Vérification du solde du compte source
            if ($compteSource->getSolde() >= $montant) {
                // Virement du compte courant vers le compte action
                if ($compteSource->retirer($montant)) {
                    $compteDestination->ajouter($montant);

                    $entityManager->persist($compteSource);
                    $entityManager->persist($compteDestination);
                    $entityManager->flush();

                    return $this->redirectToRoute('dashboard_accounts');
                } else {
                    return $this->render('error.html.twig', ['message' => 'Erreur lors du virement !']);
                }
            } else {
                return $this->render('error.html.twig', ['message' => 'Solde insuffisant pour effectuer ce virement !']);
            }
        } else {
            return $this->render('error.html.twig', ['message' => 'Les comptes sélectionnés sont invalides !']);
        }
    }
    public function virementActionVersCourant(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Récupération des données du formulaire
        $compteSourceId = $request->request->get('compteSourceId');
        $compteDestinationId = $request->request->get('compteDestinationId');
        $montant = $request->request->get('montant');
        $compteSource = $entityManager->getRepository(CompteAction::class)->find($compteSourceId);
        $compteDestination = $entityManager->getRepository(CompteCourant::class)->find($compteDestinationId);
        if ($compteSource instanceof CompteAction && $compteDestination instanceof CompteCourant) {
            // Vérification du solde du compte source
            if ($compteSource->getSolde() >= $montant) {
                // Virement du compte action vers le compte courant
                if ($compteSource->retirer($montant)) {
                    $compteDestination->ajouter($montant);
                    $entityManager->persist($compteSource);
                    $entityManager->persist($compteDestination);
                    $entityManager->flush();
                    return $this->redirectToRoute('dashboard_accounts');
                } else {
                    return $this->render('error.html.twig', ['message' => 'Erreur lors du virement !']);
                }
            } else {
                return $this->render('error.html.twig', ['message' => 'Solde insuffisant pour effectuer ce virement !']);
            }
        } else {
            return $this->render('error.html.twig', ['message' => 'Les comptes sélectionnés sont invalides !']);
        }
    }
}
