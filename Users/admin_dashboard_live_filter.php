<?php
echo $this->Html->script('common.js');
?>
<span style="display:none" id="staging"><?php echo $staging;?></span>
<?php
if($render=='edit')
{
?> 
	<?php 
		echo $this->Form->create(null,['name' => 'frmedit',
				'id' => 'frmedit'],['data-abide'=>'','novalidate']); 
	?>
    <div class="grid-x">
        <div class="large-3 end">
            <label>
                <?php
				$content_type = $articlearr[0]["Content_type"];
				?>
                <select name='content_type' id='content_type'>
                  <option value="article" <?php echo ($content_type=='article') ? 'selected' : 'notselected';?>>Article</option>
                  <option value="video" <?php echo ($content_type=='video') ? 'selected' : 'notselected';?>>Video</option>
                  <option value="podcast" <?php echo ($content_type=='podcast') ? 'selected' : 'notselected';?>>Podcast</option>
                </select>
            </label>
        </div>
    </div>
    <div class="article-structure">
        <div class="grid-x">
            <div class="cell large-12 text-center hide">
                <?php echo $this->Html->image("../assets/img/loading.gif" , array('class' => 'loading-gif'));?>
                <!--<img src="assets/img/loading.gif" class="loading-gif">--->
            </div>
        </div>
       
        <div class="grid-x grid-margin-x">
            <div class="cell large-8">
                <!--<span class="date"><input type='text' id='edit_artdate' name='edit_artdate' value='<?php echo date("F d, Y",strtotime($articlearr[0]["Article_date"]));?>'></span>--->
                <span class="date"><input type='date' id='edit_artdate' name='edit_artdate' value='<?php echo $articlearr[0]["Article_date"];?>'></span>
                <input type='hidden' name='artid_live' id='artid_live' value='<?php echo $articlearr[0]["Article_id"];?>'>
                <input type='hidden' name='urlid_live' id='urlid_live' value='<?php echo $articlearr[0]["Url_id"];?>'>
                <h2><input type='text' id='edit_arttitle' name='edit_arttitle' value='<?php echo htmlspecialchars($articlearr[0]["Article_title"], ENT_QUOTES);?>'></h2>
                <a href="<?php echo $articlearr[0]["Url"];?>" target="_blank" class="link"><?php echo $articlearr[0]["Url"];?></a>
                <p><textarea name='edit_artdesc' id='edit_artdesc' ><?php echo  htmlspecialchars($articlearr[0]["Article_desc"],ENT_QUOTES);?></textarea></p>
            </div>
            <div class="cell large-4">
				<?php
				  if ($articlearr[0]["Url_image"] != '')
					$urlimage = $articlearr[0]["Url_image"];
				  else
					$urlimage = '../img/no-image.svg';
				?>
                <!---<img class="thumbnail" style="background: url(<?php echo $urlimage;?>)">--->
				<?php echo $this->Html->image($urlimage,['class'=>'thumbnail']); ?>
            </div>
        </div>
        <div class="grid-x">
            <div class="cell large-12">
                <hr>
                <div class="grid-x grid-margin-x">
                    <div class="cell large-6">
                        <div class="cell large-12" id="tag_div">
                            <?php
                                if($articlearr[0]["tag"]!='')
                                {
                                    $tag_arr = $articlearr[0]["tag"];
									$classify_arr = $articlearr[0]["Classify_id"];
                                    $exp_tag = explode(',',$tag_arr);
									$exp_classify = explode(',',$classify_arr);
									$i=0;
                                    foreach($exp_tag as $tag)
                                    {
                                        echo '<a class="label tiny tag" data-closable>';
                                            echo $tag;
                                            echo '<button class="close-button" aria-label="Close alert" onclick=remove_tag('.$exp_classify[$i].') type="button" data-close>
                                                    <span aria-hidden="true">&times;</span>
                                                </button>';
                                        echo '</a>';
										$i++;
                                    }
                                }
                            ?>
                        </div>
                        <div class="grid-x" style="margin-top: 1rem;">
                            <div class="input-group">
                                <input class="input-group-field" id="addtag_txt" type="text" placeholder="Add Tags" list='tag_datalist' oninput='tag_datalist_admin(this.value);' autocomplete="off">
                                <?php
								if(count($datalist_tags)>0)
								{
									echo '<datalist class=tag_datalist>';
									foreach($datalist_tags as $tag)
									{
										echo '<option value="'.addslashes($tag['Tags']).'" />';
									}
									echo '</datalist>';
								}
								?>
								<div class="input-group-button">
                                  <input type="button" id="btnadd_tag" class="button" value="Add">
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                    if($page!='')
                        $show = 'hide';
                    else
                        $show = '';

                    ?>
                    <div class="cell large-6  <?php echo $show;?>">
                        <div class="callout outbound-clicks">
                            <label><strong>Outbound Clicks</strong>
                                <p><?php echo $articlearr[0]["Clicks"];?></p>
                            </label>
                        </div>
                    </div>
                   
                </div>
                
            </div>
        </div>
        <?php echo $this->Form->end(); ?>
    </div>
    <div class="grid-x modal-menu">
        <div class="cell medium-auto">
            <?php
                if ($articlearr[0]["Status"]=='published')
                    echo '<a id=edit_publish onclick=edit_popup("prepublished")>Unpublish</a>';
                else
                    echo '<a id=edit_publish onclick=edit_popup("published")>Publish</a>';            
            ?>
        </div>
        <div class="cell medium-auto">
            <a onclick="edit_popup('save');">Save</a>
        </div>
        <div class="cell medium-auto">
            <a onclick="edit_popup('re_crawl');">Re-Crawl</a>
        </div>
        <div class="cell medium-auto">
            <a onclick="edit_popup('delete');"  class="confirm">Delete</a>
        </div>
      </div>
    <button class="close-button" data-close aria-label="Close modal" type="button">
        <span aria-hidden="true">&times;</span>
    </button>
<?php
}
else
{
?>   
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
                                <input type="submit" id="domain_btnsearch" onclick="domain_btnsearch();" class="button" value="Search">
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
<?php
}?>