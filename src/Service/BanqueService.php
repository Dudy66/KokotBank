<?PHP

namespace App\Service;

use App\Repository\BanqueRepository;

class BanqueService
{
    private $banqueRepository;

    public function __construct(BanqueRepository $banqueRepository)
    {
        $this->banqueRepository = $banqueRepository;
    }

    public function getBanqueById(int $id)
    {
        return $this->banqueRepository->find($id);
    }
}