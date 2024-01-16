<?php

namespace App\Repository;

use App\Entity\Product;
use App\Model\Paginator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 *
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    private ManagerRegistry $registry;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
        $this->registry = $registry;
    }

    public function findAllWithPagination(int $page): Paginator
    {
        $query = $this->createQueryBuilder('p')->orderBy('p.id', 'DESC');

        return new Paginator($query, $page);
    }

    public function save(Product $product): void
    {
        $em = $this->registry->getManager();
        $em->persist($product);
        $em->flush();
    }

    public function update(): void
    {
        $this->registry->getManager()->flush();
    }

    public function delete(Product $product): void
    {
        $em = $this->registry->getManager();
        $em->remove($product);
        $em->flush();
    }
}
