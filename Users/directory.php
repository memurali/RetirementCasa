
<!doctype html>
<html class="no-js" lang="en">
  <head>
    <meta charset="utf-8" />
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Topic Directory - Seeking Retirement</title>
    <?php
        echo $this->Html->css('app.css');
        echo $this->Html->script('jquery.js');
        echo $this->Html->script('common.js');
    ?>

    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Ubuntu:wght@300;400;500;700&display=swap" rel="stylesheet">
    
  </head>
<body>

<div id="about">
    <?php include('header.php');?>
    <div class="off-canvas-content" data-off-canvas-content>
    <div class="hero text-center">
        <div class="grid-container">
            <div class="grid-x">
                <div class="cell large-12">
                    <h2>Topic Directory</h2>
                </div>
            </div>
        </div>
    </div>
	<br><br>
	<div class="content-container">
		<div class="grid-container">
			<div class="grid-x grid-margin-x">
				<div class="cell large-7 contact-text">
					<h4>Popular</h4>
					<?php
						foreach($tag_top_arr as $tag)
						{
							if($tag['Tags']!='')
							{
								echo '<p class="lead">';
									echo $this->Html->link(addslashes($tag['Tags']),
										['controller' => '', 'action' => $tag['SEO_Tag']],
										['data-toggle'=>'data-toggle']
									);
								echo '</p>';
							}
						}
					?>
					
				</div>
				<div class="cell large-5">
					<div class="grid-x">
						<div class="cell large-12">
							<div class="">
								<h4>A-Z</h4>
								<?php
									foreach($tag_alpha_arr as $tag)
									{
										if($tag['Tags']!='')
										{
											echo '<p class="lead">';
												echo $this->Html->link(addslashes($tag['Tags']),
													['controller' => '', 'action' => $tag['SEO_Tag']],
													['data-toggle'=>'data-toggle']
												);
											echo '</p>';
										}
									}
								?>
								
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>


	
	
    
		
        <footer>
            <div class="grid-container">
                <div class="grid-x grid-margin-x">
                    <div class="cell large-12">
                        <ul class="menu align-right">
                            <li>
								<?php
								echo $this->Html->link(
								'About',
								['controller' => '', 'action' => 'about']
								);
								?>
							</li>
                            <li>
                                <?php    
                                echo $this->Html->link(
                                'Contact',
                                ['controller' => 'users', 'action' => 'contact']
                                );
                                ?>
                            </li>
                          </ul>
                    </div>
                </div>
            </div>
        </footer>
    </div>
	
	
<!--<script src="assets/js/app.js"></script>--->
<?php
    echo $this->Html->script('app.js');
?>
</body>
</html>
