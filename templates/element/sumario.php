<div class="kt-notification-v2 sumario">
  <?php
  foreach($capitulos as $capitulo)
    {
      ?>
        <a href="<?php echo $this->Url->build(
              [
                'controller' => 'documentos',
                'action'     => 'ver',
                'capitulo',
                str_replace(" ", "-", strtolower($capitulo->nome)),
                $capitulo->id
              ]); ?>" class="kt-notification-v2__item item-sumario" data-id="<?php echo $capitulo->id; ?>">
          <div class="kt-notification-v2__itek-wrapper thick-border-left">
            <div class="kt-notification-v2__item-title">
              <?php echo $capitulo->nome; ?>
            </div>
            <div class="kt-notification-v2__item-desc">
              <?php echo substr(strip_tags($capitulo->conteudo), 0, 120) . "..."; ?>
            </div>
          </div>  
        </a>
      <?php
    }
  ?>
</div>
