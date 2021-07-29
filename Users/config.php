<head>
	<?php
	echo $this->Html->script('jquery.js');
	echo $this->Html->script('common.js');
	?>
	<style>
		#domain_div
		{
			float:left;
		}
		#input_div,#links_div
		{
			float:right;
		}
		#content_div
		{
			width:50%;
		}
	</style>
</head>
<body>
	<div id='links_div'>
		<?php
		echo $this->Html->link(
			'Config',
			['controller' => 'users', 'action' => 'config']
		);
		echo '&nbsp&nbsp';
		echo $this->Html->link(
			'Search',
			['controller' => 'users', 'action' => 'search']
		);
		echo '&nbsp&nbsp';
		echo $this->Html->link(
			'single url upload',
			['controller' => 'users', 'action' => 'urlprocess']
		);
		?>
	</div>
	<div id='content_div'>
		<?php 
			echo $this->Form->create(null,['name'=>'frmdomain','id'=>'frmdomain','novalidate','url' => [
					'controller' => 'Users',
					'action' => 'config'
				]
			]); 
		?>
			<div id='domain_div'>
				<?php
				echo '<select name=domain size='.count($domain_arr).'>';
					
					foreach($domain_arr as $domain)
					{
						$domain_val = $domain['Domain'];
						if($domain_val!='')
						{
							echo '<option value='.$domain_val.'>'.$domain_val.'</option>';
						}
					}
					
				echo '</select>';
				?>
			</div>
			<div id='input_div'>
				<table>
					<tr>
						<td>Domain score:</td>
						<td><input type='text' name='dcore' id='dcore'></td>
					</tr>
					<tr>
						<td>Click:</td>
						<td><input type='number' name='click' id='click'></td>
					</tr>
					<tr>
						<td>Article date:</td>
						<td><input type='date' name='article_date' id='date'></td>
					</tr>
				</table>
			</div><br>
			<center><input type='submit' value='Update'></center>
		<?php echo $this->Form->end(); ?>
	</div>
</body>