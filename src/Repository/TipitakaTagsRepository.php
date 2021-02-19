<?php
namespace App\Repository;

use App\Entity\TipitakaTags;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\TipitakaTagNames;

class TipitakaTagsRepository  extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TipitakaTags::class);
    }
    
    public function listRussianTagNames()
    {
        $entityManager = $this->getEntityManager();
        
        $query = $entityManager->createQueryBuilder()
        ->select('tn','t')
        ->from('App\Entity\TipitakaTagNames','tn')
        ->innerJoin('tn.tagid', 't')
        ->where('tn.languageid=1')
        ->getQuery();
        
        return $query->getResult();
    }
    
    public function persistTagName(TipitakaTagNames $tn)
    {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($tn);
        $entityManager->flush();
    }
}

