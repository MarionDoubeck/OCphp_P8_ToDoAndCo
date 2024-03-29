<?php

namespace App\Repository;

use App\Entity\Task;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Task>
 *
 * @method Task|null find($id, $lockMode = null, $lockVersion = null)
 * @method Task|null findOneBy(array $criteria, array $orderBy = null)
 * @method Task[]    findAll()
 * @method Task[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaskRepository extends ServiceEntityRepository
{


    /**
     * Constructor.
     *
     * Initializes a new instance of the TaskRepository class.
     *
     * @param ManagerRegistry $registry The registry that holds the entity managers.
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Task::class);

    }//end _construct


}//end class
