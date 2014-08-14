<div class="metrics view">
<h2><?php echo __('Metric'); ?></h2>
	<dl>
		<dt><?php echo __('Id'); ?></dt>
		<dd>
			<?php echo h($metric['Metric']['id']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Created'); ?></dt>
		<dd>
			<?php echo h($metric['Metric']['created']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Modified'); ?></dt>
		<dd>
			<?php echo h($metric['Metric']['modified']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Type'); ?></dt>
		<dd>
			<?php echo $this->Html->link($metric['Type']['name'], array('controller' => 'metrics_types', 'action' => 'view', $metric['Type']['id'])); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Source'); ?></dt>
		<dd>
			<?php echo $this->Html->link($metric['Source']['name'], array('controller' => 'metrics_sources', 'action' => 'view', $metric['Source']['id'])); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Path'); ?></dt>
		<dd>
			<?php echo h($metric['Metric']['path']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Value'); ?></dt>
		<dd>
			<?php echo h($metric['Metric']['value']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Epoch'); ?></dt>
		<dd>
			<?php echo h($metric['Metric']['epoch']); ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Edit Metric'), array('action' => 'edit', $metric['Metric']['id'])); ?> </li>
		<li><?php echo $this->Form->postLink(__('Delete Metric'), array('action' => 'delete', $metric['Metric']['id']), array(), __('Are you sure you want to delete # %s?', $metric['Metric']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('List Metrics'), array('action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Metric'), array('action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Metrics Types'), array('controller' => 'metrics_types', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Type'), array('controller' => 'metrics_types', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Metrics Sources'), array('controller' => 'metrics_sources', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Source'), array('controller' => 'metrics_sources', 'action' => 'add')); ?> </li>
	</ul>
</div>
