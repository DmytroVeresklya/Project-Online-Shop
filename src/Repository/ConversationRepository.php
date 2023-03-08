<?php

namespace App\Repository;

use App\Entity\Conversation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Conversation>
 *
 * @method Conversation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Conversation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Conversation[]    findAll()
 * @method Conversation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ConversationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Conversation::class);
    }

    public function save(Conversation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Conversation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findConversationByParticipants(int $otherUserId, int $currentUserId)
    {
        $queryBuilder = $this->createQueryBuilder('c');
        $queryBuilder
            ->select($queryBuilder->expr()->count('p.conversation'))
            ->innerJoin('c.participants', 'p')
            ->where(
                $queryBuilder->expr()->orX(
                    $queryBuilder->expr()->eq('p.user', ':currentUser'),
                    $queryBuilder->expr()->eq('p.user', ':otherUser')
                )
            )
            ->groupBy('p.conversation')
            ->having(
                $queryBuilder->expr()->eq(
                    $queryBuilder->expr()->count('p.conversation'),
                    2
                )
            )
            ->setParameters([
                'currentUser' => $currentUserId,
                'otherUser' => $otherUserId
            ])
        ;

        return $queryBuilder->getQuery()->getResult();
    }
}
