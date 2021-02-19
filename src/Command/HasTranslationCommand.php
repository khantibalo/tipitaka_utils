<?php
namespace App\Command;

use App\Repository\TipitakaSentencesRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class HasTranslationCommand extends Command
{
    protected static $defaultName = 'app:populate-hastranslation';
    
    private $sentenceRepository;
    
    public function __construct(TipitakaSentencesRepository $sentenceRepository)
    {
        $this->sentenceRepository=$sentenceRepository;
        
        parent::__construct();
    }
    
    
    protected function configure()
    {
        $this->setDescription('populates HasTranslation flag.')
        ->setHelp('This command allows you to populate HasTranslation flag for nodes that have paragraphs with hastranslation');
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        
        $this->sentenceRepository->populateNodeHasTranslation($output);
        
        return Command::SUCCESS;  
    }
}

