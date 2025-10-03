<?php
namespace App\Repository;

use App\Entity\Chat;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Chat>
 *
 * @method Chat|null find($id, $lockMode = null, $lockVersion = null)
 * @method Chat|null findOneBy(array $criteria, array $orderBy = null)
 * @method Chat[]    findAll()
 * @method Chat[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ChatRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Chat::class);
    }

    /**
     * Sauvegarde un message de chat
     */
    public function save(Chat $chat, bool $flush = false): void
    {
        $this->_em->persist($chat);

        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * Supprime un message de chat
     */
    public function remove(Chat $chat, bool $flush = false): void
    {
        $this->_em->remove($chat);

        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * Récupère les derniers messages
     *
     * @param int $limit Nombre max de messages
     * @return Chat[]
     */
    public function findLatest(int $limit = 50): array
    {
        return $this->createQueryBuilder('c')
            ->orderBy('c.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
