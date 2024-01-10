<?PHP

namespace App\Service;

class NumeroCompteGenerator
{
    public static function generate(): string
    {
        $codePays = 'FR'; // Code pays
        $numeroCompte = $codePays;

        $numbers = 12 - strlen($codePays); 
        for ($i = 0; $i < $numbers; $i++) {
            $numeroCompte .= mt_rand(0, 9); // chiffre aléatoire
        }

        return $numeroCompte;
    }
}