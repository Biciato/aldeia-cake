<?php if(count($ocorrencias) > 0)
  {
    foreach($ocorrencias as $ocorrencia)
      {
        echo $this->element('ocorrencia_individual', ['ocorrencia' => $ocorrencia, 'scope' => 3, 'parent_id' => 'ocorrencias', 'marcar_visto' => true, 'usuario_id' => $user['id'], 'force_display' => true]);
      }
  } 
else
  {
    ?>
    <p>Não foram encontradas ocorrências</p>
    <?php
  }
?>