<?php
echo $this->Html->script('common.js');
if($render=='edit_view')
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
                <span class="date"><input type='date' id='edit_artdate' name='edit_artdate' value='<?php echo $articlearr[0]["Article_date"];?>' placeholder='Article date'></span>
                <input type='hidden' name='artid_live' id='artid_live' value='<?php echo $articlearr[0]["Article_id"];?>'>
                <input type='hidden' name='urlid_live' id='urlid_live' value='<?php echo $articlearr[0]["Url_id"];?>'>
                <h2><input type='text' placeholder='Article title' id='edit_arttitle' name='edit_arttitle' value='<?php echo htmlspecialchars($articlearr[0]["Article_title"], ENT_QUOTES);?>'></h2>
                <a href="<?php echo $articlearr[0]["Url"];?>" target="_blank" class="link"><?php echo $articlearr[0]["Url"];?></a>
                <p><textarea name='edit_artdesc' id='edit_artdesc' placeholder='Article description'><?php echo  htmlspecialchars($articlearr[0]["Article_desc"],ENT_QUOTES);?></textarea></p>
				<?php
				if($articlearr[0]["Article_id"]=='')
				{
					echo "<h2><input type='text' placeholder='Article image url' id='edit_artimgurl' name='edit_artimgurl' value=''></h2>";
					echo "<input type='text' name='edit_artdomain' id='edit_artdomain' value='' placeholder='Article domain'>";
					echo "<input type='hidden' name='edit_arturl' id='edit_arturl' value=".$articlearr[0]['Url'].">";
					
				}
				?>
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
                                            echo '<button class="close-button" aria-label="Close alert" onclick=remove_tag_staging('.$exp_classify[$i].') type="button" data-close>
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
                                <input class="input-group-field" id="addtag_txt" name="addtag_txt" type="text" placeholder="Add Tags" list='tag_datalist' oninput='tag_datalist_admin(this.value);' autocomplete="off">
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
                                  <input type="button" id="btnadd_tag_staging" class="button" value="Add">
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
                    <!--<div class="cell large-6  <?php echo $show;?>">
                        <div class="callout outbound-clicks">
                            <label><strong>Outbound Clicks</strong>
                                <p><?php echo $articlearr[0]["Clicks"];?></p>
                            </label>
                        </div>
                    </div>--->
                   
                </div>
                
            </div>
        </div>
        <?php echo $this->Form->end(); ?>
    </div>
    <div class="grid-x modal-menu">
        <div class="cell medium-auto">
            <?php
                if ($articlearr[0]["Status"]=='published')
                    echo '<a id=edit_publish onclick=edit_popup_staging("prepublished")>Unpublish</a>';
                else
                    echo '<a id=edit_publish onclick=edit_popup_staging("published")>Publish</a>';            
            ?>
        </div>
        <div class="cell medium-auto">
			<?php
                if ($articlearr[0]["Status"]=='error')
                    echo '<a id=comp_btn_staging onclick=edit_popup_staging("complete")>Complete</a>';
                else
                    echo '<a onclick=edit_popup_staging("save")>Save</a>';            
            ?>
        </div>
        <div class="cell medium-auto">
            <a onclick="edit_popup_staging('re_crawl');">Re-Crawl</a>
        </div>
        <div class="cell medium-auto">
            <a onclick="edit_popup_staging('delete');"  class="confirm">Delete</a>
        </div>
      </div>
    <button class="close-button" data-close aria-label="Close modal" type="button">
        <span aria-hidden="true">&times;</span>
    </button>
<?php
}
else if($render=='error')
{
	?>
	<div class="grid-x">
        <div class="large-12">
           <h3>Error details</h3>
        </div>
    </div>
    <div class="grid-x">
        <div class="large-12">
            <label>
                <b>Stage:</b> <?php echo $error_arr[0]['Stage'];  ?>
            </label>
            <label>
                <b>Error:</b> <?php echo $error_arr[0]['Status_description'];  ?>
            </label>
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
<span style="display:none" id="staging"><?php echo $staging;?></span>
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
							<select id="show_records"  onchange="filterlimit_staging()" style="font-size: 13px;">
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
					<div>                                        
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
											echo '<td width="500" class="url"><a href="'.$url.'" target="_blank" onclick=cookieurl("'.$url.'") data-tooltip tabindex="1" title="'.$url.'" data-position="bottom" data-alignment="center">'.htmlspecialchars(str_replace('"', '', $url), ENT_QUOTES).'</a></td>';
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
<?php  
}   
exit;
?>
