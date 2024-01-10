<?PHP

namespace App\Service;

use App\Entity\User;
use App\Entity\CompteAction;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserRegistrationService
{
    private $entityManager;
    private $passwordHasher;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher)
    {
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
    }
   
    public function registerUser(User $user, string $plainPassword): void
    {
        $hashedPassword = $this->passwordHasher->hashPassword($user, $plainPassword);
        $user->setPassword($hashedPassword); 
        $user->setRoles(['ROLE_USER']);
        
        $compteAction = new CompteAction(); 
        $user->setCompteAction($compteAction); 
    
        $this->entityManager->persist($compteAction);
        $this->entityManager->persist($user);

        $this->entityManager->flush();
    }
}