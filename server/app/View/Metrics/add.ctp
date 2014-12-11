<div class="metrics form">
<?php echo $this->Form->create('Metric'); ?>
	<fieldset>
		<legend><?php echo __('Add Metric'); ?></legend>
	<?php
		echo $this->Form->input('sensor');
		echo $this->Form->input('value');
		echo $this->Form->input('units');
		echo $this->Form->input('acquired');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('List Metrics'), array('action' => 'index')); ?></li>
	</ul>
</div>
