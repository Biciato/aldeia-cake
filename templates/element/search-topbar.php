<div class="search-topbar">
    <?php echo $this->Form->create(null, ['url' => false, 'id' => 'search-topbar-form']); ?>
    	<input type="text" class="transition-024" name="termo" placeholder="&#xf131; <?php echo $placeholder; ?>" id="search-term"> <a href="javascript:void(0)" class="btn btn-success transition-024" id="do-search">Buscar</a>
    <?php echo $this->Form->end(); ?>
</div>