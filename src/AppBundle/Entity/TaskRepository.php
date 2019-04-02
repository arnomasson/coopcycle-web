<?php

namespace AppBundle\Entity;

use FOS\UserBundle\Model\UserInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr;

class TaskRepository extends EntityRepository
{
    private function createDateAwareQueryBuilder(\DateTime $date)
    {
        return $this->createQueryBuilder('t')
            ->andWhere(':date >= DATE(t.doneAfter)')
            ->andWhere(':date <= DATE(t.doneBefore)')
            ->setParameter('date', $date->format('Y-m-d'));
    }

    public function findByDate(\DateTime $date)
    {
        return $this->createDateAwareQueryBuilder($date)
            ->getQuery()
            ->getResult();
    }

    public function findUnassigned(\DateTime $date)
    {
        return $this->createDateAwareQueryBuilder($date)
            ->andWhere('t.assignedTo IS NULL')
            ->getQuery()
            ->getResult();
    }

    public function findAssigned(\DateTime $date)
    {
        return $this->createDateAwareQueryBuilder($date)
            ->andWhere('t.assignedTo IS NOT NULL')
            ->getQuery()
            ->getResult();
    }
}
