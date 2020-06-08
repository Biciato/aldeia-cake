<?php
namespace App\Command;

use Cake\Console\Arguments;
use Cake\Console\Command;
use Cake\Console\ConsoleIo;

class IndicacoesCommand extends Command
  {
    public function execute(Arguments $args, ConsoleIo $io)
      {
        $now = new \DateTime();
        $io->out('Agora é ' . $now->format('d/m/Y H:i:s'));
      }
  }