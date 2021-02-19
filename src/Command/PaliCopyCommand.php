<?php
namespace App\Command;

use App\Repository\TipitakaSentencesRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\Entity\TipitakaUsers;

class PaliCopyCommand extends Command
{
    protected static $defaultName = 'app:pali-copy';
    
    private $sentenceRepository;
    
    public function __construct(TipitakaSentencesRepository $sentenceRepository)
    {
        $this->sentenceRepository = $sentenceRepository;
        
        parent::__construct();
    }
    
    
    protected function configure()
    {
        $this->setDescription('Copy pali text to a translation source.')
        ->setHelp('');
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        //TODO: pass these as parameters
        $nodeid=1;
        $sourceid=1;
        $user=new TipitakaUsers();
        
        $this->sentenceRepository->paliCopy($nodeid,$sourceid,$user);
        
        return Command::SUCCESS;
    }
}

