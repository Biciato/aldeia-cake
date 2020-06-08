<?php
namespace App\Command;

use Cake\Console\Arguments;
use Cake\Console\Command;
use Cake\Console\ConsoleIo;
use Cake\ORM\TableRegistry;


class ControleMensalNotasCommand extends Command
  {
    public function execute(Arguments $args, ConsoleIo $io)
      {
        $unidadesTable = TableRegistry::get('Unidades');
        $alunosTable   = TableRegistry::get('Alunos');
        $servicosTable = TableRegistry::get('Servicos');
        $registrosTable = TableRegistry::get('RegistrosNotasFiscais');
        $unidades = $unidadesTable->find('all', 
          [
            'conditions' =>
            [
            'ativo' => true
            ]
          ])->toArray();
        $_servicos = [];
        foreach($unidades as $unidade)
          {
            $existente = $registrosTable->find('all', 
              [
                'conditions' =>
                  [
                    'mes_referencia' => date('m'),
                    'ano_referencia' => date('Y'),
                    'unidade' => $unidade->id
                  ]
              ])->first();  
            if(!is_null($existente))
              {
                $io->out('Os dados já foram registrados esse mês em ' . $unidade->nome);
                continue;
              }
            $io->out('Registrando dados de ' . $unidade->nome);
            $alunos = $alunosTable->find('all',
                [
                'conditions' =>
                    [
                    'unidade' => $unidade->id,
                    'ano_letivo' => date('Y')
                    ]
                ])->contain(['Pessoas'])->toArray();
            $io->out(count($alunos) . ' alunos encontrados');
            $total = 0;
            $total_marcado = 0;
            $total_alunos = count($alunos);
            $total_alunos_marcados = 0;
            $alunos_array = [];
            $alunos_marcados_array = [];
            foreach($alunos as $aluno)
              {
                $servicos_aluno = $aluno->servicos_array;
                $financeiro     = $aluno->financeiro_array;
                $marcado        = $aluno->emite_nota_fiscal;
                $valores        = 0;
                foreach($servicos_aluno as $servico)
                  {
                    if(!isset($_servicos[$servico]))
                      {
                        $servico = $servicosTable->get($servico);
                      }
                    else
                      {
                        $servico = $_servicos[$servico];
                      }
                    $valor = $servico->valor_atual;
                    if($valor)
                      {
                        $desconto = (((int)$valor->valor*(int)$aluno->financeiro_array[$servico->id])/100);
                        $valor_servico = ((int)$valor->valor - $desconto);
                        $valores += $valor_servico;
                      }
                  }
                $total += $valores;
                $alunos_array[] = $aluno->id;
                if($marcado)
                  {
                    $total_marcado += $valores;
                    $total_alunos_marcados++;
                    $alunos_marcados_array[] = $aluno->id;
                  }
              }
            $registro = $registrosTable->newEntity(
              [
                'unidade' => $unidade->id,
                'mes_referencia' => date('m'),
                'ano_referencia' => date('Y'),
                'total_alunos' => $total_alunos,
                'total_alunos_marcados' => $total_alunos_marcados,
                'valor_total' => $total,
                'valor_total_marcado' => $total_marcado,
                'alunos' => json_encode($alunos_array),
                'alunos_marcados' => json_encode($alunos_marcados_array)
              ]
            );
            if($registrosTable->save($registro))
              {
                $io->success('Registro gerado com sucesso!');
              }
            else
              {
                $io->error('Erro ao gerar o registro de ' . $unidade->nome);
              }
          }
      }
  }
