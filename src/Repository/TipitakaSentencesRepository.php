<?php
namespace App\Repository;

use App\Entity\TipitakaSentences;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\Expr\Join;
use App\Entity\TipitakaSentenceTranslations;

class TipitakaSentencesRepository  extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TipitakaSentences::class);
    }
    
    
    public function setParentNodesHasTranslation($paragraphid)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQueryBuilder()
        ->select('toc')
        ->from('App\Entity\TipitakaToc','toc')
        ->innerJoin('App\Entity\TipitakaParagraphs', 'c',Join::WITH,'toc.nodeid=c.nodeid')
        ->where('c.paragraphid=:id')
        ->getQuery()
        ->setParameter('id', $paragraphid);
        
        $node=$query->getOneOrNullResult();
        
        $qParent=$entityManager->createQueryBuilder()
        ->select('toc')
        ->from('App\Entity\TipitakaToc','toc')
        ->where('toc.nodeid=:id')
        ->getQuery();
        
        while($node && !$node->getHasTranslation())
        {
            $node->setHastranslation(true);
            $entityManager->persist($node);
            $entityManager->flush();
            
            $node=$qParent->setParameter('id', $node->getParentid())->getOneOrNullResult();
        }                
    }
    
    public function populateNodeHasTranslation($output)
    {        
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQueryBuilder()
        ->select('p.paragraphid')
        ->from('App\Entity\TipitakaParagraphs','p')
        ->where('p.hastranslation=1')
        ->getQuery();
        
        $results=$query->getResult();
        
        foreach ($results as $result)
        {
            $this->setParentNodesHasTranslation($result['paragraphid']);
            $output->writeln($result['paragraphid']);
        }
    }
    
    public function setParagraphHasTranslation($sentenceid)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQueryBuilder()
        ->select('c')
        ->from('App\Entity\TipitakaParagraphs','c')
        ->innerJoin('App\Entity\TipitakaSentences', 's',Join::WITH,'c.paragraphid=s.paragraphid')
        ->where('s.sentenceid=:id')
        ->getQuery()
        ->setParameter('id', $sentenceid);
        
        $paragraph=$query->getOneOrNullResult();
        
        if(!$paragraph->getHastranslation())
        {
            $paragraph->setHastranslation(true);
            $entityManager->persist($paragraph);
            $entityManager->flush();
        }
    }
    
    public function populateParagraphHasTranslation()
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQueryBuilder()
        ->select('s.sentenceid')
        ->from('App\Entity\TipitakaSentenceTranslations','t')
        ->innerJoin('t.sentenceid', 's')
        ->groupBy('s.sentenceid')
        ->getQuery();
        
        $results=$query->getResult();
        
        foreach ($results as $result)
        {
            $this->setParagraphHasTranslation($result['sentenceid']);
        }
    }
    
    public function paliCopy($nodeid,$sourceid,$user)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQueryBuilder()
        ->select('s')
        ->from('App\Entity\TipitakaSentences','s')
        ->innerJoin('s.paragraphid', 'c')
        ->innerJoin('c.nodeid', 'toc')
        ->where('toc.nodeid=:nid')
        ->getQuery()
        ->setParameter('nid',$nodeid);
        
        $sentences=$query->getResult();
        
        $query = $entityManager->createQueryBuilder()
        ->select('s')
        ->from('App\Entity\TipitakaSources','s')
        ->where('s.sourceid=:soid')
        ->getQuery()
        ->setParameter('soid',$sourceid);
        $source=$query->getOneOrNullResult();
        
        foreach($sentences as $sentence)
        {
            $translation=new TipitakaSentenceTranslations();
            $translation->setSourceid($source);
            $translation->setUserid($user);
            $translation->setDateupdated(new \DateTime());
            $translation->setSentenceid($sentence);
            $translation->setTranslation($sentence->getSentencetext());
            $this->persistTranslation($translation);
        }
    }
    
}

