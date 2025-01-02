<?php
namespace App\Command;

use App\Repository\TipitakaTocRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class NodePathsCommand extends Command
{
    protected static $defaultName = 'app:fix-childnodepaths';
    
    private $tocRepository;
    
    public function __construct(TipitakaTocRepository $tocRepository)
    {
        $this->tocRepository = $tocRepository;
        
        parent::__construct();
    }
    
    
    protected function configure()
    {
        $this->setDescription('fixes child node paths.')
        ->setHelp('This command allows you to correct child node paths after changing node parent in tipitaka_toc table');
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {        
        $nodeid=1;//TODO: pass this as a command parameter
        $this->tocRepository->fixChildNodePaths($nodeid);
        
        return Command::SUCCESS;
    }
}

