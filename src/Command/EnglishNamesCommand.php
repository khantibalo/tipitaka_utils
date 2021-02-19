<?php
namespace App\Command;

use App\Repository\TipitakaTagsRepository;
use App\Repository\TipitakaTocRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\Entity\TipitakaNodeNames;
use App\Entity\TipitakaTagNames;

class EnglishNamesCommand extends Command
{
    protected static $defaultName = 'app:create-english-names';
    
    private $tocRepository;
    private $tagsRepository;
    
    public function __construct(TipitakaTocRepository $tocRepository,TipitakaTagsRepository $tagsRepository)
    {
        $this->tocRepository = $tocRepository;
        $this->tagsRepository=$tagsRepository;
        
        parent::__construct();
    }
    
    
    protected function configure()
    {
        $this->setDescription('Creates english names.')->setHelp('This command allows you create node and tag names in English from Russian');
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $nodeNames=$this->tocRepository->listRussianNames();
        
        foreach($nodeNames as $name)
        {            
            $matches=array();
            if(preg_match('/^(ДН|МН|АН|СН|КХп|СНп|УД|ИТ)\s+(\S+)\s*(?!Комме)/',$name->getName(),$matches))
            {                
                $search=["ДН","СНп","МН","АН","СН","КХп","УД","ИТ"];
                $replace=["DN","Snp","MN","AN","SN","Khp","Ud","Iti"];
                
                $englishname=str_replace($search,$replace,$matches[1]);
                
                $paliname=$name->getNodeid()->getTitle();
                $palinameMatches=array();

                if(preg_match('/^\d+\.\s+\(\d+\)\s+(.+)/',$paliname,$palinameMatches))
                {
                    $paliname=$palinameMatches[1];
                }
                
                if(preg_match('/^\d+\.\s+(.+)/',$paliname,$palinameMatches))
                {
                    $paliname=$palinameMatches[1];
                }
                
                $output->writeln($name->getName()." ".$englishname." ".$matches[2]);//." ".$paliname
                                
                $nn=new TipitakaNodeNames();
                
                $englishLanguage=$this->tocRepository->getLanguage(2);
                
                $nn->setAuthorid($name->getAuthorid());
                $nn->setLanguageid($englishLanguage);
                $nn->setNodeid($name->getNodeid());
                $nn->setName($englishname." ".$matches[2]);//." ".$paliname
                $this->tocRepository->persistNodeName($nn);
            }
            
        }
        

        $tagNames=$this->tagsRepository->listRussianTagNames();
        
        foreach($tagNames as $name)
        {
            $englishname=NULL;
            
            $matches=array();
            if(preg_match('/^(ДН|МН|АН|СН|КХп|СНп|УД|ИТ)\s+(\S+)/',$name->getTitle(),$matches))
            {
                $search=["ДН","СНп","МН","АН","СН","КХп","УД","ИТ"];
                $replace=["DN","Snp","MN","AN","SN","Khp","Ud","Iti"];
                
                $englishname=str_replace($search,$replace,$matches[1])." ".$matches[2]." and its commentary";
                $englishname=str_replace("/Кхп","",$englishname);
                
                $output->writeln($name->getTitle()." ".$englishname." ".$matches[2]);                
            }
            
            if(preg_match('/^(\d+)\s+глава Дхаммапады/',$name->getTitle(),$matches))
            {              
                $englishname=$matches[1]." chapter of the Dhammapada and its commentary";
                
                $output->writeln($name->getTitle()." ".$englishname);
            }
            
            if($englishname)
            {
                $tn=new TipitakaTagNames();
                
                $englishLanguage=$this->tocRepository->getLanguage(2);
                
                $tn->setTagid($name->getTagid());
                $tn->setLanguageid($englishLanguage);
                $tn->setTitle($englishname);
                
                $this->tagsRepository->persistTagName($tn);
            }
        }
        
        
        return Command::SUCCESS;        
    }
}

