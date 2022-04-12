
<!doctype html>
<html id='html_id' class="no-js" lang="en">
  <head>
    <meta charset="utf-8" />
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Retirement Rover</title>
    <meta name="robots" content="noindex">
    <!---<link rel="stylesheet" href="assets/css/app.css">--->
    <?php
        echo $this->Html->css('app.css');
        echo $this->Html->script('jquery.js');
        echo $this->Html->script('common.js');
    ?>
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Ubuntu:wght@300;400;500;700&display=swap" rel="stylesheet">
    <script>
        $(document).ready(function() {
           // $('#live_allarticle').DataTable();
            var i =1;
            $.each($('.reveal-overlay'), function (index, value) {
                $(this).attr('id', 'popup'+i);
                i++;
            });
           $('#search_domain').keypress(function (e) {
                var key = e.which;
                if(key == 13)  // the enter key code
                {
                    domain_btnsearch();
                    return false;  
                }
             });   
        } );
    </script>
     <input type="hidden" id="actionname" name="actionname" value=""/>
    <div class="reveal tiny" id="do_publish" data-reveal>
      <div class="grid-x">
        <div class="cell large-12">
          <h3>Are You Sure to publish?</h3>
        </div>
      </div>
      <div class="grid-x modal-menu">
        <div class="cell medium-auto">
          <a onclick="popup_close('publish_confirmation')">Cancel</a>
        </div>
        <div class="cell medium-auto">
          <a id='btn_do_publish' onclick="livepage_process('do_publish');" class="confirm">Confirm</a>
        </div>
      </div>
      <button class="close-button" data-close aria-label="Close modal" type="button">
        <span aria-hidden="true">&times;</span>
      </button>
    </div>
    <div class="reveal tiny" id="domain_error" data-reveal>
        <div class="grid-x">
            <div class="cell large-12">
            <h3>Enter valid domain</h3>
            </div>
        </div>
        <button class="close-button" data-close aria-label="Close modal" type="button">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <div class="reveal tiny" id="change_dscore" data-reveal>
        <div class="grid-x">
            <div class="cell large-12">
            <h3>Are you sure to change domain score?</h3>
            </div>
        </div>
        <div class="grid-x modal-menu">
            <div class="cell medium-auto">
            <a onclick="popup_close('change_dscore')">Cancel</a>
            </div>
            <div class="cell medium-auto">
            <a id='confirm_dscore' class="confirm">Confirm</a>
            </div>
        </div>
        <button class="close-button" data-close aria-label="Close modal" type="button">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <div class="reveal tiny" id="do_unpublish" data-reveal>
        <div class="grid-x">
            <div class="cell large-12">
            <h3>Are You Sure to unpublish?</h3>
            </div>
        </div>
        <div class="grid-x modal-menu">
            <div class="cell medium-auto">
            <a onclick="popup_close('do_unpublish')">Cancel</a>
            </div>
            <div class="cell medium-auto">
            <a id='btn_do_unpublish' onclick="livepage_process('do_unpublish');" class="confirm">Confirm</a>
            </div>
        </div>
        <button class="close-button" data-close aria-label="Close modal" type="button">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <div class="reveal tiny" id="do_recrawl" data-reveal>
        <div class="grid-x">
            <div class="cell large-12">
            <h3>Are You Sure to recrawl?</h3>
            </div>
        </div>
        <div class="grid-x modal-menu">
            <div class="cell medium-auto">
            <a onclick="popup_close('do_recrawl')">Cancel</a>
            </div>
            <div class="cell medium-auto">
            <a id='btn_do_recrawl' onclick="livepage_process('do_recrawl');" class="confirm">Confirm</a>
            </div>
        </div>
        <button class="close-button" data-close aria-label="Close modal" type="button">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <div class="reveal tiny" id="do_delete" data-reveal>
        <div class="grid-x">
            <div class="cell large-12">
            <h3>Are You Sure to recrawl?</h3>
            </div>
        </div>
        <div class="grid-x modal-menu">
            <div class="cell medium-auto">
            <a onclick="popup_close('do_delete')">Cancel</a>
            </div>
            <div class="cell medium-auto">
            <a id="btn_do_delete" onclick="livepage_process('do_delete');" class="confirm">Confirm</a>
            </div>
        </div>
        <button class="close-button" data-close aria-label="Close modal" type="button">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <div class="reveal tiny" id="do_retag" data-reveal>
        <div class="grid-x">
            <div class="cell large-12">
            <h3>Are You Sure to recrawl?</h3>
            </div>
        </div>
        <div class="grid-x modal-menu">
            <div class="cell medium-auto">
            <a onclick="popup_close('do_retag')">Cancel</a>
            </div>
            <div class="cell medium-auto">
            <a id='btn_do_retag' onclick="livepage_process('do_retag');" class="confirm">Confirm</a>
            </div>
        </div>
        <button class="close-button" data-close aria-label="Close modal" type="button">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <div class="reveal tiny" id="popup_error" data-reveal>
        <div class="grid-x">
            <div class="cell large-12">
            <h3>Please select any url</h3>
            </div>
        </div>
        <button class="close-button" data-close aria-label="Close modal" type="button">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
  </head>
<body>

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
            <?php echo $this->Html->image("../assets/img/loading.gif" , array('class' => 'loading-gif'));?>
            <!--<img src="assets/img/loading.gif" class="loading-gif">--->
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
                <div class="cell large-6">
                    <div class="cell large-12">
                        <a href="#" class="label tiny tag" data-closable>
                            Retirement Plan
                            <button class="close-button" aria-label="Close alert" type="button" data-close>
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </a>
                        <a href="#" class="label tiny tag" data-closable>
                            Savings
                            <button class="close-button" aria-label="Close alert" type="button" data-close>
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </a>
                        <a href="#" class="label tiny tag" data-closable>
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
                <div class="cell large-6">
                    <div class="callout outbound-clicks">
                        <label><strong>Outbound Clicks</strong>
                            <p>1078</p>
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>--->
</div>
<div class="grid-x modal-menu">
    <div class="cell medium-auto">
        <a href="#">Publish</a>
    </div>
    <div class="cell medium-auto">
        <a href="#">Save</a>
    </div>
    <div class="cell medium-auto">
        <a href="#">Re-Crawl</a>
    </div>
    <div class="cell medium-auto">
        <a href="#" class="confirm">Delete</a>
    </div>
  </div>
<button class="close-button" data-close aria-label="Close modal" type="button">
    <span aria-hidden="true">&times;</span>
</button>
    
</div>
<div class="dashboard-layout">
    <div class="grid-x">
        <div class="cell large-2">
            <div class="callout side-nav show-for-large">
                <ul class="vertical menu">
                    <li>
                         <?php
                        echo $this->Html->link(
                        'Retirement Rover',
                        ['controller' => 'users', 'action' => 'index'],
                        ['class'=>'menu-text']
                        );
                        ?>
                    </li>
                    <!--<li><a href="admin_dashboard.html" class="dashboard">Dashboard</a></li>--->
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
                                ['class'=>'live active']
                            );
                        ?>
                        <span class="alert" id="approvalcount"><?php echo $approvalcount;?></span>
                    </li>
                    <!--<li><a href="admin_dashboard-staging.html" class="staging">Staging</a><span class="alert">10</span></li>
                    <li><a href="admin_dashboard-users.html" class="users">Users</a></li>---->
                    <li>
                        <?php
                            echo $this->Html->link(
                                'Users',
                                ['controller' => 'users', 'action' => 'adminDashboardUsers'],
                                ['class'=>'users']
                            );
                        ?>
                    </li>
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
        <div class="cell large-10" id='domain_response'>
            <div class="grid-x">
                <div class="cell small-12">
                    <div class="top-bar">
                        <div class="top-bar-left">                            
                            <ul class="menu">
                                <li>
                                    <label>
                                        <select name='select_domain' id='select_domain'>
                                          <!--<option value="aarp">AARP</option>
                                          <option value="Fidelity">Fidelity</option>--->
                                          <?php
                                           
                                            $select = ($selected_domain=='all') ? 'selected' : 'notselected';
                                            if($domain_count_all[0]['article_count']=='')
                                                $domain_count_all[0]['article_count'] = 0;
                                            if($domain_count_all[0]['sum_click']=='')
                                                $domain_count_all[0]['sum_click'] = 0;
                                            $count = '('.$domain_count_all[0]['article_count'].','.$domain_count_all[0]['sum_click'].')';
                                            echo '<option value="all" '.$select.'>all '.$count.'</option>';      
                                            if(count($domain_arr)>0) 
                                            {  
                                                foreach($domain_arr as $domain)
                                                {                                                   
                                                    $count = '('.$domain['artcnt'].','.$domain['clickcnt'].')';
                                                    $select = ($domain['Domain_name']==$selected_domain) ? 'selected' : 'notselected';
                                                    $domain_name = substr($domain['Domain_name'], 0, strpos($domain['Domain_name'], "."));
                                                    echo '<option value="'.$domain['Domain_name'].'" '.$select.'>'.$domain_name.' '.$count.'</option>';
                                                }
                                            } 
                                          ?>
                                        </select>
                                    </label>
                                </li>
                                <li>
                                    <div class="input-group" id="search_domain">
                                        <input list="domain_list" id="domain_input" ondblclick='clear_txt(this);'  class="input-group-field" type="text" value='<?php echo $selected_domain;?>' placeholder="Search Domain" autocomplete="off">
                                        <datalist id="domain_list">
                                            <?php
                                                if(count($domain_arr)>0) 
                                                {
                                                    foreach($domain_arr as $domain)
                                                    {
                                                        echo '<option  value="'.$domain['Domain_name'].'">';
                                                    }
                                                } 
                                            ?>
                                        </datalist>
                                        <div class="input-group-button">
                                        <input type="submit" onclick='domain_btnsearch()' id="domain_btnsearch" class="button" value="Search">
                                        </div>                                       
                                    </div>
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
            <div class="grid-container">
                <div class="dashboard-content">
                    <div class="grid-x grid-margin-x grid-padding-y">
                        <div class="cell large-12">
                            <?php
                            if($selected_domain=='all')
                                $style='display:none';
                            else
                                $style='display:block';
                            ?>
                            <div class="float-left" style="<?php echo $style;?>">
                                <h2><?php echo substr($domain_count[0]['Domain_name'], 0, strpos($domain_count[0]['Domain_name'], ".")); ?></h2>
                                <!--<a href="#" target="_blank">www.aarp.com</a>--->
                                <?php 
                                    $domain_url =  $domain_count[0]['Domain_name'];
                                    echo $this->Html->link( $domain_url, 'https://www.'.$domain_url ,array('target','_blank') ); 
                                ?>
                                
                            </div>                            
                            <div class="float-right">
                                <ul class="menu align-right data-info" style="padding-top: 1rem;">
                                    <li class="callout total-articles">
                                        <label><strong>Total Articles</strong>
                                        <p><?php echo $domain_count[0]['article_count'];?></p>
                                        </label>
                                    </li>
                                    <li class="callout outbound-clicks">
                                        <label><strong>Outbound Clicks</strong>
                                        <?php 
                                            if($domain_count[0]['sum_click']!='')
                                                echo '<p>'.$domain_count[0]['sum_click'].'</p>';
                                        ?>
                                        </label>
                                    </li>
                                    <li class="callout das" style="<?php echo $style;?>">
										<label><strong>Domain Authority Score</strong>
                                            <select name='domain_score' id='domain_score'>
                                                <option value='0.1' <?php echo $domain_count[0]['Domain_score']==0.1 ? 'selected' : 'notselected';?>>0.1</option>
												<option value='0.2' <?php echo $domain_count[0]['Domain_score']==0.2 ? 'selected' : 'notselected';?>>0.2</option>
												<option value='0.3' <?php echo $domain_count[0]['Domain_score']==0.3 ? 'selected' : 'notselected';?>>0.3</option>
												<option value='0.4' <?php echo $domain_count[0]['Domain_score']==0.4 ? 'selected' : 'notselected';?>>0.4</option>
												<option value='0.5' <?php echo $domain_count[0]['Domain_score']==0.5 ? 'selected' : 'notselected';?>>0.5</option>
												<option value='0.6' <?php echo $domain_count[0]['Domain_score']==0.6 ? 'selected' : 'notselected';?>>0.6</option>
												<option value='0.7' <?php echo $domain_count[0]['Domain_score']==0.7 ? 'selected' : 'notselected';?>>0.7</option>
												<option value='0.8' <?php echo $domain_count[0]['Domain_score']==0.8 ? 'selected' : 'notselected';?>>0.8</option>
												<option value='0.9' <?php echo $domain_count[0]['Domain_score']==0.9 ? 'selected' : 'notselected';?>>0.9</option>
                                            </select>
                                        </label>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="grid-x grid-margin-x">
                        <div class="cell large-12">
                            <div class="callout table-content">
                                <div class="button-group float-left">
                                    <a class="button" id='btnpublish_live' data-open="do_publish">Publish</a>
                                    <a class="button hollow" id='btnunpublish_live' data-open="do_unpublish">Unpublish</a>
                                </div>
                                <div class="float-left" style="margin-left: 10px;">
                                    <label>
                                        <select style="font-size: 13px;" onchange='live_show_filter();' id="show_records">
                                            <option value="20" <?php echo ($limit==20) ? 'selected' : 'notselected';?>>Show 20</option>
                                            <option value="50" <?php echo ($limit==50) ? 'selected' : 'notselected';?>>Show 50</option>
                                            <option value="100" <?php echo ($limit==100) ? 'selected' : 'notselected';?>>Show 100</option>
                                        </select>
                                    </label>
                                </div>
                                <div class="float-left" style="margin-left: 10px;">
                                    <label>
                                        <select style="font-size: 13px;" id="live_publish_filter">
                                            <option value="all" <?php echo ($filter=='all') ? 'selected' : 'notselected';?>>All</option>
                                            <option value="publish" <?php echo ($filter=='publish') ? 'selected' : 'notselected';?>>Publish</option>
                                            <option value="unpublish" <?php echo ($filter=='unpublish') ? 'selected' : 'notselected';?>>Unpublish</option>
                                        </select>
                                    </label>
                                </div>
								<div class="float-left" style="margin-left: 10px;">
                                    <label>
                                        <input type='search' name='art_title_search' id='art_title_search' value='<?php echo $_SESSION['art_srch_live'];?>'>
									</label>
                                </div>
								<div class="float-left" style="margin-left: 10px;">
									<a class="button" id='btn_live_art_srch' onclick='art_live_search();'>Search</a>
								</div>
                                <div class="button-group float-right">
                                    <a class="button secondary" data-open="do_recrawl">Re-Crawl</a>
                                    <a class="button secondary" data-open="do_retag">Re-Tag</a>
                                    <a class="button alert" data-open="do_delete">Delete</a>
                                </div>
                                <input type='hidden' name='domain' id='domain' value='<?php echo $selected_domain;?>'>
                                <div id='tbldiv'>
                                    <table id='tblsort' class="stack">
                                        <thead>
                                            <tr>
                                                <th width="30"><input id="checkAll_live" class="table-checkboxes" type="checkbox"></th>
                                                <th width="78">Date<a onclick="sortTable(0,'date')" class="sort"></a></th>
                                                <th width="500">Article Title</th>
                                                <th>From<a onclick="sortTable(2)" class="sort"></a></th>
                                                <th>Clicks<a onclick="sortTable(3,'number')" class="sort"></a></th>
                                                <th></th>
                                                <th class="float-right"><a onclick="sortTable(5)" class="sort"></a></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                                if(count($all_article)>0)
                                                {
                                                    $i=1;
                                                    foreach($all_article as $articlearr)
                                                    {
                                                        echo '<tr>';
                                                            echo '<th width="30"><input id="" class="table-checkboxes" type="checkbox" value="'.$articlearr['tblcrawler_queue'][Url_id].'"></th>';
                                                            if($articlearr["Article_date"]!='')
																echo '<td>'.date("m/d/y",strtotime($articlearr["Article_date"])).'</td>';
															else
																echo '<td></td>';
                                                            echo '<td width="500" class="url">
                                                                    <a href="'.$articlearr["Url"].'" target="_blank" onclick=cookieurl("'.$articlearr["Url"].'")>'
                                                                        .htmlspecialchars($articlearr["Article_title"], ENT_QUOTES).
                                                                    '</a>
                                                                </td>';
                                                            echo '<td>'.$articlearr["tbluser"]["Email"].'</td>';
                                                            echo '<td>'.$articlearr["Clicks"].'</td>';
                                                            echo '<td><a class="label tiny secondary" onclick=edit_article('.$articlearr['tblcrawler_queue'][Url_id].') data-open="editArticle">Edit</a></td>';
                                                            if($articlearr['tblcrawler_queue']["Status"]=='published')
                                                            {
                                                                $checked = 'checked';
                                                                $publish_val = 1;
                                                            }
                                                            else
                                                            {
                                                                $checked = '';
                                                                $publish_val = 0;
                                                            }
                                                            echo '<td class="text-right"><input type=hidden value='.$publish_val.'>
                                                                    <div class="switch tiny rounded">
                                                                        <input class="switch-input" id="tinySwitch'.$i.'" onchange=change_publish('.$articlearr['tblcrawler_queue'][Url_id].',this); type="checkbox" '.$checked.'>
                                                                        <label class="switch-paddle" for="tinySwitch'.$i.'">
                                                                            <span class="show-for-sr">Tiny Sandwiches Enabled</span>
                                                                        </label>
                                                                    </div>
                                                                </td>';
                                                            $i++;
                                                        echo '</tr>';
                                                    }
                                                }

                                            ?>
                                        </tbody>
                                    </table>
                                </div>
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
<!---<script src="assets/js/app.js"></script>--->
<?php
    echo $this->Html->script('app.js');
?>
</body>
</html>
