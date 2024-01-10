<?PHP

namespace App\Controller;

use App\Entity\User;
use App\Entity\CompteAction;
use App\Entity\CompteCourant;
use App\Service\BanqueService;
use App\Form\RegistrationFormType;
use App\Service\NumeroCompteGenerator;
use App\Service\UserRegistrationService;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserController extends AbstractController
{
    private $userRegistrationService;
    private $banqueService;
    private $entityManager;
    private $passwordHasher;

    public function __construct(
        UserRegistrationService $userRegistrationService,
        BanqueService $banqueService,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ) {
        $this->userRegistrationService = $userRegistrationService;
        $this->banqueService = $banqueService;
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
        $this->userRegistrationService = $userRegistrationService;
    }
    public function createUser(Request $request, BanqueService $banqueService)
    {
        $user = new User();

        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('password')->getData();

            // Génération des numéros de compte
            $numeroCompteCourant = NumeroCompteGenerator::generate();
            $numeroCompteAction = NumeroCompteGenerator::generate();

            // Creer l'objet Banque avec l'ID 1
            $banqueId = 1;
            $banque = $this->banqueService->getBanqueById($banqueId);

            if ($banque !== null) {
                // Création de l'utilisateur
                $user->setNumeroCompteCourant($numeroCompteCourant);
                $user->setNumeroCompteAction($numeroCompteAction);
                $user->setBanque($banque);

                $existingUser = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $user->getEmail()]);

                if ($existingUser) {
                    return $this->redirectToRoute('user_exists_error_route');
                }

                // Hachage du mot de passe
                $hashedPassword = $this->passwordHasher->hashPassword($user, $plainPassword);
                $user->setPassword($hashedPassword);
                $user->setRoles(['ROLE_USER']);

                // Création du CompteCourant avec 10 000 €
                $compteCourant = new CompteCourant();
                $compteCourant->setNumeroCompte($numeroCompteCourant);
                $compteCourant->setSolde(10000);
                $compteCourant->setDecouvert(-300);
                $compteCourant->setUser($user);
                $compteCourant->setBanque($banque);
                $compteCourant->setDateOuverture(new \DateTime());

                // Création du CompteAction
                $compteAction = new CompteAction();
                $compteAction->setNumeroCompte($numeroCompteAction);
                $compteAction->setUser($user);
                $compteAction->setPlafond(2500);
                $compteAction->setBanque($banque);

                // Enregistre les entités
                $this->entityManager->persist($user);
                $this->entityManager->persist($compteCourant);
                $this->entityManager->persist($compteAction);
                $this->entityManager->flush();

                // Redirection après enregistrement

                return $this->redirectToRoute('home');
            } else {
                // Message d'erreur flash si la banque n'est pas trouvée
                $this->addFlash('error', 'Banque not found.');
                return $this->redirectToRoute('register');
            }
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
