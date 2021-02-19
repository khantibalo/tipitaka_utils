<?php
namespace App\Repository;

use App\Entity\TipitakaNodeNames;
use App\Entity\TipitakaToc;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class TipitakaTocRepository  extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TipitakaToc::class);
    }
    
    public function listRussianNames()
    {
        $entityManager = $this->getEntityManager();
        
        $query = $entityManager->createQueryBuilder()
        ->select('nn','toc','user')
        ->from('App\Entity\TipitakaNodeNames','nn')
        ->innerJoin('nn.nodeid', 'toc')
        ->innerJoin('nn.authorid', 'user')
        ->where('nn.languageid=1')
        ->getQuery();
            
        return $query->getResult();
    }
    
    public function persistNodeName(TipitakaNodeNames $nn)
    {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($nn);
        $entityManager->flush();
    }
    
    public function getLanguage($languageid)
    {
        $entityManager = $this->getEntityManager();
        
        $query = $entityManager->createQueryBuilder()
        ->select('l')
        ->from('App\Entity\TipitakaLanguages','l')
        ->where('l.languageid=:id')
        ->getQuery()
        ->setParameter('id', $languageid);
        
        return $query->getOneOrNullResult();
    }
    
    public function fixChildNodePaths($nodeid)
    {
        $parent=$this->find($nodeid);
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQueryBuilder()
        ->select('toc')
        ->from('App\Entity\TipitakaToc','toc')
        ->where('toc.parentid=:nid')
        ->getQuery()
        ->setParameter('nid',$nodeid);
        
        $childNodes=$query->getResult();
        
        foreach($childNodes as $childNode)
        {
            $this->fixChildNodePathsRecursive($parent,$childNode);
        }
    }
    
    private function fixChildNodePathsRecursive($parent,$child)
    {
        $entityManager = $this->getEntityManager();
        
        $child->setPath($parent->getPath().$child->getNodeid()."\\");
        $child->setTextPath($parent->getTextPath().' '.$child->getTitle()."\\");
        $entityManager->persist($child);
        $entityManager->flush();
        
        $query = $entityManager->createQueryBuilder()
        ->select('toc')
        ->from('App\Entity\TipitakaToc','toc')
        ->where('toc.parentid=:nid')
        ->getQuery()
        ->setParameter('nid',$child->getNodeid());
        
        $childNodes=$query->getResult();
        
        foreach($childNodes as $childNode)
        {
            $this->fixChildNodePathsRecursive($child,$childNode);
        }
    }    
    
}

