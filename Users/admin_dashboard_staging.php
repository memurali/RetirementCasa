
<!doctype html>
<html id="html_id" class="no-js" lang="en">
  <head>
    <meta charset="utf-8" />
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex">
    <title>Admin-Seeking Retirement</title>
    <meta name="robots" content="noindex">
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
        $('#search_domain').keypress(function (e) {
            var key = e.which;
            if(key == 13)  // the enter key code
            {
                staging_search();
                return false;  
            }
        });   
        
     });
    </script>
	<style>
	.table-content .url a:hover 
	{
		overflow: visible; 
		white-space: normal; 
		width: auto; 
		
	}
	</style>
    <input type="hidden" id="actionname" name="actionname" value="staging"/>
    <!----publish--->
    <div class="reveal tiny" id="publishcon" data-reveal>
      <div class="grid-x">
        <div class="cell large-12">
          <h3>Are You Sure?</h3>
        </div>
      </div>
      <div class="grid-x modal-menu">
        <div class="cell medium-auto">
          <a onclick="popup_close('publishcon')">Cancel</a>
        </div>
        <div class="cell medium-auto">
          <a  class="confirm" onclick="stagingprocess('do_publish')">Confirm</a>
        </div>
      </div>
      <button class="close-button" data-close aria-label="Close modal" type="button">
        <span aria-hidden="true">&times;</span>
      </button>
  </div>
  <!----unpublish----->
  <div class="reveal tiny" id="unpublishcon" data-reveal>
      <div class="grid-x">
        <div class="cell large-12">
          <h3>Are You Sure?</h3>
        </div>
      </div>
      <div class="grid-x modal-menu">
        <div class="cell medium-auto">
          <a onclick="popup_close('unpublishcon')">Cancel</a>
        </div>
        <div class="cell medium-auto">
          <a  class="confirm" onclick="stagingprocess('do_unpublish')">Confirm</a>
        </div>
      </div>
      <button class="close-button" data-close aria-label="Close modal" type="button">
        <span aria-hidden="true">&times;</span>
      </button>
  </div>
  <!----delete----->
  <div class="reveal tiny" id="deletecon" data-reveal>
      <div class="grid-x">
        <div class="cell large-12">
          <h3>Are You Sure?</h3>
        </div>
      </div>
      <div class="grid-x modal-menu">
        <div class="cell medium-auto">
          <a onclick="popup_close('deletecon')">Cancel</a>
        </div>
        <div class="cell medium-auto">
          <a  class="confirm" onclick="stagingprocess('do_delete')">Confirm</a>
        </div>
      </div>
      <button class="close-button" data-close aria-label="Close modal" type="button">
        <span aria-hidden="true">&times;</span>
      </button>
  </div>
  <!----crawl----->
  <div class="reveal tiny" id="crawlcon" data-reveal>
      <div class="grid-x">
        <div class="cell large-12">
          <h3>Are You Sure?</h3>
        </div>
      </div>
      <div class="grid-x modal-menu">
        <div class="cell medium-auto">
          <a onclick="popup_close('crawlcon')">Cancel</a>
        </div>
        <div class="cell medium-auto">
          <a  class="confirm" onclick="stagingprocess('do_recrawl')">Confirm</a>
        </div>
      </div>
      <button class="close-button" data-close aria-label="Close modal" type="button">
        <span aria-hidden="true">&times;</span>
      </button>
  </div>

  <div class="reveal tiny" id="stagingerror" data-reveal>
      <div class="grid-x">
        <div class="cell large-12">
          <h3>Please select any URL.</h3>
        </div>
      </div>
      <button class="close-button" data-close aria-label="Close modal" type="button">
        <span aria-hidden="true">&times;</span>
      </button>
  </div>
  </head>
<body>
<div class="reveal small" id="addURL" data-reveal>
    <div class="grid-x">
        <div class="large-12">
           <h3>Add New Articles</h3>
        </div>
    </div>
    <div class="grid-x">
        <div class="large-12">
            <label>
                <textarea placeholder="" id="stagingaddurl" style="height: 10rem;"></textarea>
              </label>
        </div>
    </div>
    <div class="grid-x modal-menu">
        <div class="cell medium-auto">
            <a onclick="popup_close('addURL')" >Cancel</a>
        </div>
        <div class="cell medium-auto">
            <a  class="confirm" onclick="stagingurlsubmit();" id="stagingurlsubmit">Import</a>
        </div>
      </div>
    <button class="close-button" data-close aria-label="Close modal" type="button">
        <span aria-hidden="true">&times;</span>
    </button>
</div>

<div class="reveal" id="editArticle" data-reveal>   
    <div class="grid-x">
        <div class="large-3 end">
            <label>
                <select>
				  <option value="">--select--</option>
                  <option value="article">Article</option>
                  <option value="Video">Video</option>
                  <option value="Podcast">Podcast</option>
                </select>
              </label>
        </div>
    </div>
    <div class="article-structure">
        <div class="grid-x">
            <div class="cell large-12 text-center">
                <!--<img src="assets/img/loading.gif" class="loading-gif">-->
                <?php echo $this->Html->image("../assets/img/loading.gif" , array('class' => 'loading-gif'));?>
            </div>
        </div>
        <!--<div class="grid-x grid-margin-x">
            <div class="cell large-8">
                <span class="date">July 1, 2019</span>
                <h2>AARP Retirement Calculator: Are You Saving Enough?</h2>
                <a href="https://www.aarp.org/work/retirement-planning/retirement_calculator.html" target="_blank" class="link">https://www.aarp.org/work/retirement-planning/retirement_calculator.html</a>
                <p>Find out when — and how — to retire the way you want.  The AARP Retirement Calculator can provide you with a personalized snapshot of what your financial future might look like. Simply answer a few questions about your household status, salary and retirement savings, such as an IRA or 401(k).</p>
            </div>
            <div class="cell large-4">
                <img class="thumbnail" style="background: url(https://cdn.aarp.net/content/dam/aarp/Member-Benefits/2017/06/1140x641-aarp-programs-mbc-woman-on-computer-2020.imgcache.rev97641abfdebfaab5dd32a7affc5c2ff6.web.600.336.jpg)">
            </div>
        </div>
        <div class="grid-x">
            <div class="cell large-12">
                <hr>
                <div class="grid-x grid-margin-x">
                    <div class="cell large-6 end">
                        <div class="cell large-12">
                            <a  class="label tiny tag" data-closable>
                                Tag
                                <button class="close-button" aria-label="Close alert" type="button" data-close>
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </a>
                        </div>
                        <div class="grid-x" style="margin-top: 1rem;">
                            <div class="input-group">
                                <input class="input-group-field" type="text" placeholder="Add Tags">
                                <div class="input-group-button">
                                  <input type="submit" class="button" value="Add">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>-->
    </div>
    <div class="grid-x modal-menu">
        <div class="cell medium-auto">
            <a >Publish</a>
        </div>
        <div class="cell medium-auto">
            <a >Save</a>
        </div>
        <div class="cell medium-auto">
            <a >Re-Crawl</a>
        </div>
        <div class="cell medium-auto">
            <a class="confirm">Delete</a>
        </div>
      </div>
    <button class="close-button" data-close aria-label="Close modal" type="button">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<div class="reveal small" id="error_view" data-reveal>
    <center>
    <?php echo $this->Html->image("../assets/img/loading.gif" , array('class' => 'loading-gif'));?>
    </center>
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
                        ['class'=>'staging active']
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
                                    <a  class="button expanded" data-open="addURL" style="font-size: 16px; padding: 10.5px;">Add URL</a>
                                </li>
                                <li>
                                    <?php                
                                    if($stage_domain=='all')
                                        $stage_domain='';
                                    else
                                        $stage_domain=$stage_domain;
                                    ?>
                                    <div class="input-group" id='search_domain'>
                                        <input class="input-group-field" list="domains" id="domain" name="domain" type="text" ondblclick="clear_txt(this);" value='<?php echo $stage_domain;?>'  placeholder="Search Domain" autocomplete="off">
                                        <datalist id="domains">
                                        <?php
                                            $size = sizeof($domain);
                                            if($size>0)
                                            {
                                                for($i=0;$i<$size;$i++)
                                                {
                                                    echo '<option value="'.$domain[$i]['Domain_name'].'">';
                                                }                                                
                                            }
                                        ?>
                                        </datalist>
                                        <div class="input-group-button">
                                        <input type="submit" onclick='staging_search()' class="button" id="stagingsearch" value="Search">
                                        </div>
                                    </div>
                                    <?php //echo $this->Form->end(); ?>
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
            <div id="stagingrender">
                <div class="grid-container">
                    <div class="dashboard-content">
                        <div class="grid-x grid-margin-x">
                            <div class="cell large-12">
                                <div class="callout table-content">
                                    <div class="button-group float-left">
                                        <a class="button secondary" data-open="crawlcon" >Crawl</a>
                                    </div>
                                    <div class="float-left" style="margin-left: 10px;">
                                        <label>
                                            <select id="show_records" onchange="filterlimit_staging()" style="font-size: 13px;">
                                                <option value="20" <?php echo ($limit==20) ? 'selected' : 'notselected';?>>Show 20</option>
                                                <option value="50" <?php echo ($limit==50) ? 'selected' : 'notselected';?>>Show 50</option>
                                                <option value="100" <?php echo ($limit==100) ? 'selected' : 'notselected';?>>Show 100</option>
                                            </select>
                                        </label>                                    
                                    </div>
                                    <div class="float-left" style="margin-left: 10px;">
										<label>
											<?php
											$selected_status = $status;
											?>
                                            <select id="selectstatus" onchange="filterstaging()" style="font-size: 13px;">
                                                <option value="all" <?php echo ($status=='all') ? 'selected' : 'notselected';?>>All</option>
                                                <option value="submitted" <?php echo ($status=='submitted') ? 'selected' : 'notselected';?>>Submitted</option>
                                                <option value="processing" <?php echo ($status=='processing') ? 'selected' : 'notselected';?>>Processing</option>
                                                <option value="completed" <?php echo ($status=='completed') ? 'selected' : 'notselected';?>>Completed</option>
                                                <option value="error" <?php echo ($status=='error') ? 'selected' : 'notselected';?>>Error</option>
                                            </select>
                                        </label>                                     
                                    </div>
                                    <div class="float-left total-articles">                                        
                                        <label>Total: <?php echo $this->Paginator->params()['count'];?></label>
                                    </div> 
                                    <?php
                                    if($status=='completed')
                                        $btnpublish = 'display:block';
                                    else
                                        $btnpublish = 'display:none';  
                                    ?>
                                    <div class="button-group float-right">
                                        <a style='<?php echo $btnpublish;?>' class="button" data-open="publishcon">Publish</a>
                                        <a style='<?php echo $btnpublish;?>' class="button hollow" data-open="unpublishcon">Pre-Publish</a>
                                        <a class="button alert" data-open="deletecon" >Delete</a>                                        
                                    </div>                                                                               
                                    <table class="stack" id="tblsort">
                                        <thead>
                                            <tr>
                                                <th width="30"><input id="CheckAll" class="table-checkboxes" type="checkbox"></th>
                                                <th width="500">URL</th>
                                                <?php
												if($status=='completed')
												{
													echo '<th>Title</th>';
													echo '<th>Tags</th>';
												}
												else
												{
												?>
												<th>Status<a id="stagingth" onclick="sortTable(1)" class="sort"></a></th>
												<th>Date<a id="stagingth" onclick="sortTable(2,'date')" class="sort"></a></th>
												<?php
												}
												if($status=='error')
												{
													echo '<th></th>';
												}
												if($status=='error' || $status=='completed')
												{
													echo '<th></th>';
												}
												?>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php 
                                           // print_r($select_arr);
                                            $arrsize = sizeof($select_arr);
                                            if($arrsize>0)
                                            { 
                                                foreach($select_arr as $arr)
                                                {          
                                                    if($arr['tblcrawler_queue']['Url_id']!='')
                                                    {      
                                                        $url_id =  $arr['tblcrawler_queue']['Url_id'];
                                                        $url =   $arr['tblcrawler_queue']['Url'];
                                                        $status = $arr['tblcrawler_queue']['Status'];
                                                        $date = $arr['tblcrawler_queue']['Datecreated'];
                                                    }
                                                    else
                                                    {
                                                        $url_id =  $arr['Url_id'];
                                                        $url =   $arr['Url'];
                                                        $status = $arr['Status'];
                                                        $date = $arr['Datecreated'];
                                                    }
                                                    echo '<tr>';
														echo '<th width="30"><input value="'.$url_id.'" class="table-checkboxes" type="checkbox"></th>';
														echo '<td width="500" class="url"><a href="'.$url.'" target="_blank" onclick=cookieurl("'.$url.'")>'.htmlspecialchars(str_replace('"', '', $url), ENT_QUOTES).'</a></td>';
														//echo '<td>'.$arr['tbluser']['Email'].'</td>';
														if($status=='completed')
														{
															if($selected_status=='completed')
															{
																echo '<td width="200">'.$arr['Article_title'].'</td>';
																echo '<td width="200">'.$arr['tag'].'</td>';
															}
															else
															{
																$td3='<td><input type="hidden" value=3/><span class="label success">Completed</span></td>';                                                
																$td4 = '<td>'.$date.'</td>';
															}
															$td5='<td class="float-right"><a class="label button tiny expand" onclick=edit_article_staging('.$url_id.') data-open="editArticle">View</a></td>';
														}												
														if($status == 'submitted'|| $status == 'prepublished')
														{
															$td3='<td><input type="hidden" value=4/></td>';
															$td5='<td class="float-right"><a  class="label button tiny secondary" onclick=crawl('.$url_id.') >Crawl</a></td>';
															$td4 = '<td>'.$date.'</td>';
														}
														if($status == 'processing')
														{ 
															$td3='<td>'.$this->Html->image("../assets/img/loading.gif" , array('class' => 'loading-gif')).'<input type="hidden" value=2/></td>';
															$td5='<td class="float-right"></td>';
															$td4 = '<td>'.$date.'</td>';
														}
														if($status == 'error')
														{ 
															$td3='<td><input type="hidden" value=1/><a onclick=error_view('.$url_id.') data-open="error_view"><span class="label alert">Error</span></a></td>';
															if($selected_status=='error')
															{
																$td5='<td><a class="label button tiny expand" onclick=edit_article_staging('.$url_id.') data-open="editArticle">Edit</a></td>';
																$td5.='<td class="float-right"><a  class="label button tiny secondary" onclick=crawl('.$url_id.') >Crawl</a></td>';
															}
															$td4 = '<td>'.$date.'</td>';
														}
																									
														echo $td3;
														echo $td4;
														if($selected_status=='error' || $selected_status=='completed')
															echo $td5;
													echo '</tr>';
                                                }
                                            }
                                        ?>
                                        </tbody>
                                    </table>                                                                  
                                </div>
                            </div>
                        </div>
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
</div>

<!--<script src="assets/js/app.js"></script>-->
<?php
    echo $this->Html->script('app.js');   
?>
</body>
</html>
