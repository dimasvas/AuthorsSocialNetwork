<?php
 namespace Tuchka\Console\Command;
/**
 * Description of CountAuthorAverageCommand
 *
 * @author dimas
 */
class CountAuthorAverageCommand extends Command 
{
    protected function configure()
    {
        $this
            ->setName('author:average')
            ->setDescription('Counts average composition rate');
    }
    
     protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('name');
        if ($name) {
            $text = 'Hello '.$name;
        } else {
            $text = 'Hello';
        }

        if ($input->getOption('yell')) {
            $text = strtoupper($text);
        }

        $output->writeln('Finished Author counting');
    }
}
