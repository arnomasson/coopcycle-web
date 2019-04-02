<?php

namespace AppBundle\Api\Filter;

use AppBundle\Entity\Task;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\AbstractContextAwareFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;
use Psr\Log\LoggerInterface;

final class TaskDateFilter extends AbstractContextAwareFilter
{
    // private $property;

    // public function __construct(
    //     ManagerRegistry $managerRegistry,
    //     $requestStack = null,
    //     LoggerInterface $logger = null,
    //     array $properties = null,
    //     string $property = null)
    // {
    //     parent::__construct($managerRegistry, $requestStack, $logger, $properties);

    //     // $this->tokenStorage = $tokenStorage;
    //     $this->property = $property;
    // }

    protected function filterProperty(string $property, $value, QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, string $operationName = null)
    {
        // Only works on Task class
        if ($resourceClass !== Task::class) {
            return;
        }

        // var_dump($property);
        // var_dump($this->isPropertyEnabled($property, $resourceClass));
        // var_dump($this->isPropertyMapped($property, $resourceClass));
        // // var_dump($this->property);
        // var_dump($resourceClass);

        // otherwise filter is applied to order and page as well
        if (!$this->isPropertyEnabled($property, $resourceClass)) {
            return;
        }



        // if (!$this->isPropertyMapped($property, $resourceClass) && $this->property) {

        //     $property = $this->property;
        // }

        // ->andWhere(':date >= DATE(t.doneAfter)')
        // ->andWhere(':date <= DATE(t.doneBefore)')

        $afterParameterName = $queryNameGenerator->generateParameterName('doneAfter');
        $beforeParameterName = $queryNameGenerator->generateParameterName('doneBefore');

        $queryBuilder
            ->andWhere(sprintf(':%s >= DATE(o.%s)', $afterParameterName, 'doneAfter'))
            ->andWhere(sprintf(':%s <= DATE(o.%s)', $beforeParameterName, 'doneBefore'))
            ->setParameter($afterParameterName, $value)
            ->setParameter($beforeParameterName, $value);

        // var_dump($queryBuilder->getQuery()->getSQL());
    }

    public function getDescription(string $resourceClass): array
    {
        if (!$this->properties) {
            return [];
        }

        $description = [];
        foreach ($this->properties as $property => $strategy) {
            $description[$property] = [
                'property' => $property,
                'type' => 'string',
                'required' => false,
            ];
        }

        return $description;
    }
}
