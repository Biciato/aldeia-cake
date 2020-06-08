<?php
foreach($aluno->pessoa->cobrancas as $cobranca)
{
  ?>
      <tr>
            <td><?php echo $cobranca->data_envio->format('d/m/Y'); ?></td>
            <td><?php echo $cobranca->assunto; ?></td>
            <td><?php echo ($cobranca->lida) ? 'Sim' : 'Não'; ?></td>
       </tr>
  <?php
} ?>