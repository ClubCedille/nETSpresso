<div class="metrics index">
	<h2><?php echo __('Metrics'); ?></h2>
	<table cellpadding="0" cellspacing="0">
	<thead>
	<tr>
			<th><?php echo $this->Paginator->sort('id'); ?></th>
			<th><?php echo $this->Paginator->sort('created'); ?></th>
			<th><?php echo $this->Paginator->sort('modified'); ?></th>
			<th><?php echo $this->Paginator->sort('type_id'); ?></th>
			<th><?php echo $this->Paginator->sort('source_id'); ?></th>
			<th><?php echo $this->Paginator->sort('path'); ?></th>
			<th><?php echo $this->Paginator->sort('value'); ?></th>
			<th><?php echo $this->Paginator->sort('epoch'); ?></th>
			<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	</thead>
	<tbody>
	<?php foreach ($metrics as $metric): ?>
	<tr>
		<td><?php echo h($metric['Metric']['id']); ?>&nbsp;</td>
		<td><?php echo h($metric['Metric']['created']); ?>&nbsp;</td>
		<td><?php echo h($metric['Metric']['modified']); ?>&nbsp;</td>
		<td>
			<?php echo $this->Html->link($metric['Type']['name'], array('controller' => 'metrics_types', 'action' => 'view', $metric['Type']['id'])); ?>
		</td>
		<td>
			<?php echo $this->Html->link($metric['Source']['name'], array('controller' => 'metrics_sources', 'action' => 'view', $metric['Source']['id'])); ?>
		</td>
		<td><?php echo h($metric['Metric']['path']); ?>&nbsp;</td>
		<td><?php echo h($metric['Metric']['value']); ?>&nbsp;</td>
		<td><?php echo h($metric['Metric']['epoch']); ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('View'), array('action' => 'view', $metric['Metric']['id'])); ?>
			<?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $metric['Metric']['id'])); ?>
			<?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $metric['Metric']['id']), array(), __('Are you sure you want to delete # %s?', $metric['Metric']['id'])); ?>
		</td>
	</tr>
<?php endforeach; ?>
	</tbody>
	</table>
	<p>
	<?php
	echo $this->Paginator->counter(array(
	'format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}')
	));
	?>	</p>
	<div class="paging">
	<?php
		echo $this->Paginator->prev('< ' . __('previous'), array(), null, array('class' => 'prev disabled'));
		echo $this->Paginator->numbers(array('separator' => ''));
		echo $this->Paginator->next(__('next') . ' >', array(), null, array('class' => 'next disabled'));
	?>
	</div>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('New Metric'), array('action' => 'add')); ?></li>
		<li><?php echo $this->Html->link(__('List Metrics Types'), array('controller' => 'metrics_types', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Type'), array('controller' => 'metrics_types', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Metrics Sources'), array('controller' => 'metrics_sources', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Source'), array('controller' => 'metrics_sources', 'action' => 'add')); ?> </li>
	</ul>
</div>
