
<!doctype html>
<html id="html_id" class="no-js" lang="en">
  <head>
    <meta charset="utf-8" />
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex">
    <title>Admin-Seeking Retirement</title>
    <meta name="robots" content="noindex">
	<style>
	
	</style>
    <!--<link rel="stylesheet" href="assets/css/app.css">-->
    <?php
        echo $this->Html->css('app.css');        
        echo $this->Html->script('jquery.js');
        echo $this->Html->script('common.js'); 
    ?>  
	<script src="https://cdn.jsdelivr.net/npm/foundation-sites@6.6.3/dist/js/foundation.min.js" integrity="sha256-pRF3zifJRA9jXGv++b06qwtSqX1byFQOLjqa2PTEb2o=" crossorigin="anonymous"></script>
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Ubuntu:wght@300;400;500;700&display=swap" rel="stylesheet">
    <script>
     $(document).ready(function(){
        //reveal overlay
        var i =1;
        $.each($('.reveal-overlay'), function (index, value) {
            $(this).attr('id', 'popup'+i);
            i++;
        });
    });
    </script>
  </head>
<body>
<div class="reveal small" id="addmatch" data-reveal>
    <div class="grid-x">
        <div class="large-12">
           <h3>Add New Match</h3>
        </div>
    </div>
    <div class="grid-x">
        <div class="large-12">
            <label>
                <textarea placeholder="Keyword Search Phrase:Keyword Group" id="kwgrp_match" style="height: 10rem;"></textarea>
            </label>
        </div>
    </div>
    <div class="grid-x modal-menu">
        <div class="cell medium-auto">
            <a onclick="popup_close('addmatch')" >Cancel</a>
        </div>
        <div class="cell medium-auto">
            <a  class="confirm" onclick="kwgrp_match_click();">Save</a>
        </div>
      </div>
    <button class="close-button" data-close aria-label="Close modal" type="button">
        <span aria-hidden="true">&times;</span>
    </button>
</div>

<div class="reveal small" id="editmatch" data-reveal>
    
</div>
<div class="reveal tiny" id="deletecon_match" data-reveal>
  <div class="grid-x">
	<div class="cell large-12">
	  <h3>Are You Sure?</h3>
	</div>
  </div>
  <div class="grid-x modal-menu">
	<div class="cell medium-auto">
	  <a onclick="popup_close('deletecon_match')">Cancel</a>
	</div>
	<div class="cell medium-auto">
	  <a  class="confirm" onclick="delete_match();">Confirm</a>
	</div>
  </div>
  <button class="close-button" data-close aria-label="Close modal" type="button">
	<span aria-hidden="true">&times;</span>
  </button>
</div>

<div class="reveal tiny" id="warning_match" data-reveal>
  <div class="grid-x">
	<div class="cell large-12">
	  <h3>Please enter data in proper format</h3>
	</div>
  </div>
  <div class="grid-x modal-menu">
	<div class="cell medium-auto">
	  <a onclick="popup_close('warning_match')">Cancel</a>
	</div>
  </div>
</div>

<div class="reveal tiny" id="warning_match_select" data-reveal>
  <div class="grid-x">
	<div class="cell large-12">
	  <h3>Please select anyone data</h3>
	</div>
  </div>
  <div class="grid-x modal-menu">
	<div class="cell medium-auto">
	  <a onclick="popup_close('warning_match_select')">Cancel</a>
	</div>
  </div>
</div>

<div class="dashboard-layout">
    <div class="grid-x">
        <div class="cell large-2">
            <div class="callout side-nav show-for-large">
                <ul class="vertical menu">
                    <li>
                         <?php
                        echo $this->Html->link(
                        'Seeking Retirement',
                        ['controller' => 'users', 'action' => 'index'],
                        ['class'=>'menu-text']
                        );
                        ?>
                    </li>
                    <li>
                        <?php
                        echo $this->Html->link(
                        'Dashboard',
                        ['controller' => 'users', 'action' => 'adminDashboard'],
                        ['class'=>'dashboard']
                        );
                        ?>
                    </li>
                    <li>
                        <?php
                        echo $this->Html->link(
                        'Staging',
                        ['controller' => 'users', 'action' => 'adminDashboardStaging'],
                        ['class'=>'staging']
                        ); 
                        ?>
                    </li>
                    <li>
                        <?php  
                        echo $this->Html->link(
                        'Live',
                        ['controller' => 'users', 'action' => 'adminDashboardLive'],
                        ['class'=>'live']
                        );
                        ?>
                        <span id='approvalcount' class="alert"><?php echo $approvalcount;?></span>
                    </li>
                    <li>
                        <?php    
                        echo $this->Html->link(
                        'Users',
                        ['controller' => 'users', 'action' => 'adminDashboardUsers'],
                        ['class'=>'users']
                        );
                        ?>
                    </li> 
                    <!--<li><a href="#" class="dashboard">Dashboard</a></li>                
                     <li><a href="admin_dashboard-staging.html" class="staging live">Staging</a><span class="alert">10</span></li>
                    <li><a href="admin_dashboard-live.html" class="live ">Live</a></li>
                    <li><a href="admin_dashboard-users.html" class="users">Users</a></li>-->
                    <li>
                        <?php    
                        echo $this->Html->link(
                        'Settings',
                        ['controller' => 'users', 'action' => 'admin_setting'],
                        ['class'=>'settings']
                        );
                        ?>
                    </li> 
					<li>
                        <?php    
                        echo $this->Html->link(
                        'Match',
                        ['controller' => 'users', 'action' => 'admin-match'],
                        ['class'=>'settings active']
                        );
                        ?>
                    </li>
                </ul>

                <ul class="vertical menu bottom">
                    <li>
                        <?php
                            echo $this->Html->link(
                            'Logout',
                            ['controller' => 'users', 'action' => 'signout'],
                            ['class'=>'logout']
                            );
                        ?>
                    </li>
                </ul>
            </div>
        </div>
        <div class="cell large-10">
            <div class="grid-x">
                <div class="cell small-12">
                    <div class="top-bar">
                        <div class="top-bar-left">
                            <ul class="menu">
                                <li>
                                    <a class="button expanded" data-open="addmatch" style="font-size: 16px; padding: 10.5px;">Add Match</a>
                                </li>
                                
                            </ul>
                        </div>
                        <div class="top-bar-right">
                            <ul class="menu">
                                <li><span><?php echo $_SESSION['username'];?></span></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
			<div id="match_div">
				<div class="callout table-content">
					<div class="float-left" style="margin-left: 10px;">
						<label>
							<select style="font-size: 13px;" onchange='match_show_filter();' id="show_records">
								<option value="all" <?php echo ($limit=='all') ? 'selected' : 'notselected';?>>Show All</option>
								<option value="50" <?php echo ($limit==50) ? 'selected' : 'notselected';?>>Show 50</option>
								<option value="100" <?php echo ($limit==100) ? 'selected' : 'notselected';?>>Show 100</option>
							</select>
						</label>
					</div>
					<div class="button-group float-right">
						<a class="button alert" onclick="editmatch();" id="btn_edit_match">Edit</a>
						<a class="button alert" data-open="deletecon_match" id="btn_delete_match">Delete</a>
					</div>
					<table class="stack" id="tblsort">
					<?php
					if(count($matchdata)>0)
					{
						echo "<thead>";
							echo "<tr>";
								echo "<th><input type=checkbox name=all_chk_match id=all_chk_match onchange=allcheck_match(this)></th>";
								echo "<th>Keyword / Phrase Match<a onclick=sortTable(1) class=sort></th>";
								echo "<th>Category Group<a onclick=sortTable(2) class=sort></th>";
							echo "</tr>";
						echo "</thead>";
						echo "<tbody>";
						foreach($matchdata as $match)
						{
							echo "<tr>";
								echo "<td><input class=checkbox_match type=checkbox name=matchid value=".$match['Matchid']."></td>";
								echo "<td>".$match['Kw_Phrase']."</td>";
								echo "<td>".$match['Kw_Group']."</td>";
							echo "</tr>";
						}
						echo "</tbody>";
					}
					?>
					</table>
					<div class="grid-x">
						<div class="cell large-12">
							<nav aria-label="Pagination">
								<ul class="pagination">
									<?php
									echo $this->Paginator->prev('Previous'); 
									echo $this->Paginator->numbers(['first' => 2, 'last' => 2]); 
									echo $this->Paginator->next('Next'); 
									?> 
								</ul>
							</nav>
						</div>
					</div>
				</div>         
            </div>         
        </div>
    </div>
</div>

<!--<script src="assets/js/app.js"></script>-->
<?php
    echo $this->Html->script('app.js');   
?>
</body>
</html>
