<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;
use Cake\ORM\TableRegistry;



class Ocorrencia extends Entity {

  protected $_accessible = 
    [
        '*' => true,
    ];

  protected $_virtual =
    [
    ];
  protected function _getTextoFormatado()
    {
        $pessoasTable = TableRegistry::get('Pessoas');
        $pessoas = $pessoasTable->find('all', ['conditions' => ['nome !=' => '', 'OR' => ['Alunos.id IS NOT NULL', 'Colaboradores.id IS NOT NULL']]])->contain(['Alunos', 'Colaboradores'])->order(['Pessoas.nome ASC'])->toArray();
        $tagsTable = TableRegistry::get('Tags');
        $tags = $tagsTable->find('all')->toArray();
        $texto = $this->_fields['texto'];
        foreach($pessoas as $pessoa)
          {
            if(strpos($texto, '@' . $pessoa->nome) !== false)
              {
                $inicio = strpos($texto, '@' . $pessoa->nome);
                $final = ($inicio + strlen($pessoa->nome) + 1);
                $texto = substr($texto, 0, $inicio) . '<b>@' . substr($texto, ($inicio + 1), (strlen($pessoa->nome))) . '</b>' . substr($texto, ($inicio + strlen($pessoa->nome) + 1));
              }
          }
        foreach($tags as $tag)
          {
            if(strpos($texto, '#' . $tag->nome) !== false)
              {
                $inicio = strpos($texto, '#' . $tag->nome);
                $final = ($inicio + strlen($tag->nome) + 1);
                $texto = substr($texto, 0, $inicio) . '<b>#' . substr($texto, ($inicio + 1), (strlen($tag->nome))) . '</b>' . substr($texto, ($inicio + strlen($tag->nome) + 1));
              }
          }
        return $texto;
    }
  
  protected function _getMencoesArray()
    {
      $array = null;
      if($this->_fields['mencoes'])
        {
          $array = json_decode($this->_fields['mencoes'], true);
        }
      return ($array) ? $array : [];
    }
  protected function _getVistoPorArray()
    {
      $array = null;
      if($this->_fields['visto_por'])
        {
          $array = json_decode($this->_fields['visto_por'], true);
        }
      return ($array) ? $array : [];
    }
  protected function _getVistoPorFormatado()
    {
        $pessoasTable = TableRegistry::get('Pessoas');
        $_visto_por = $this->_getVistoPorArray();
        $visto_por = [];
        foreach($_visto_por as $id)
          {
            $pessoa = $pessoasTable->get($id);
            $visto_por[] = $pessoa->nome;
          }
        if(count($visto_por) == 0)
          {
            return "";
          }
        if(count($visto_por) == 1)
          {
            return $visto_por[0];
          }
        $ultimo = array_pop($visto_por);
        return implode(', ', $visto_por) . ' e ' . $ultimo;
    }
}