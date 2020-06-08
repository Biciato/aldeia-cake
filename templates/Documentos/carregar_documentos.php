<?php 
	foreach($documentos as $documento)
	  {
		?> 
		    <a href="<?php echo $this->Url->build(
	         [
			    'controller' => 'documentos',
			    'action'     => 'ver',
			    'documento',
				$documento->id
			]); ?>" class="kt-notification-v2__item item-sumario" data-id="<?php echo $documento->id; ?>">
			  <div class="kt-notification-v2__itek-wrapper thick-border-left">
			        <div class="kt-notification-v2__item-title">
			            <?php echo $documento->nome; ?>
			        </div>
					<div class="kt-notification-v2__item-desc">
					    <?php echo substr(strip_tags($documento->descricao), 0, 120) . "..."; ?>
					</div>
				</div>  
			</a>
					  		  
		<?php
	  }
?>