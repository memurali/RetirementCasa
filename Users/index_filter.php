<script>
/*$("a.page_link").each(function() {
	var page_num = $(this).text();
	if(page_num==1)
	{
	  var url = window.location.href;
	  var newurl = $('#paramlink_index').val();
	  newurl = newurl+'/1';
	  $(this).attr('href',newurl);
	}
	
});*/
</script>
<?php 
  $parameters = $this->request->getAttribute('params');
  if($parameters['param']!='')
	$param_tag = $parameters['param'];
  else if($parameters['param1']!='')
  {
	  if(!is_numeric($parameters['param1']))
		  $param_tag = $parameters['param1'];
	  else
		  $param_tag = '';
  }
  else
	  $param_tag = '';
?>
<div class="off-canvas position-right" id="tagMenu" data-transition="overlap" data-off-canvas>
      <ul class="tabs" data-active-collapse="false" data-tabs id="collapsing-tabs">
        <li class="tabs-title is-active"><a href="#topCategories" aria-selected="true">Top</a></li>
        <li class="tabs-title"><a href="#alphebetical">Alphabetical</a></li>
      </ul>

      <div class="tabs-content" data-tabs-content="collapsing-tabs">
        <div class="tabs-panel is-active" id="topCategories">
          <ul class="no-bullet">
		    <?php
			if (count($tag_arr_top) > 0) 
			{
				
				foreach ($tag_arr_top as $tag) 
				{
					if($tag['Tags']!='')
					{
						echo '<li><a data-toggle onclick=tagclick(this.text)>'.addslashes($tag['Tags']).'</a></li>';
					}
				}
			}
			?>
          </ul>
        </div>
        <div class="tabs-panel" id="alphebetical">
          <ul class="no-bullet">
		    <?php
			if(count($tag_arr_alpha)>0)
			{
				foreach($tag_arr_alpha as $tag)
				{
					if($tag['Tags']!='')
					{
						echo '<li><a data-toggle onclick=tagclick(this.text)>'.addslashes($tag['Tags']).'</a></li>';
					}
				}
			}	
            ?>			
          </ul>
        </div>
		<input type='hidden' name='tag_val' id='tag_val' value="<?php echo $_SESSION['tag']; ?>">
      </div>
</div>
<!-- June 2021 Update End -->
<div class="content-container">
	<div class="grid-container">
	  <div class="grid-x grid-margin-x">
		<div class="cell large-9">
			<div class="grid-x">
				<?php
				if ($_SESSION['tag'] != '') 
				{
				?>
				<div class="cell medium-2">
					<?php 
						if($this->Paginator->params()['count']!='')
							$article_count = $this->Paginator->params()['count'];
						else
							$article_count = 0;
					?>
					<p><strong><span><?php echo $article_count;?></span> results for</strong></p>
				</div>
				<div class="cell medium-10">
					<div class="filter">
					<?php
					$searched_tag = $_SESSION['filter_tag'];
					foreach ($searched_tag as $seltag) 
					{
						echo '<a class="label tiny tag" id='.$seltag['SEO_Tag'].' onclick=remove_tag_index(this.id) data-closable>';
							echo $seltag['Tags'];
							echo '<button class="close-button"  aria-label="Close alert" type="button" data-close>';
							echo '<span aria-hidden="true">&times;</span>';
							echo '</button>';
						echo '</a>';
					}	
					?>	
					
					</div>
				</div>
				<?php
				}?>
			</div>
			<div id="filter" class="filter hide">
			<div class="grid-x grid-margin-x">
			  <div class="cell large-7">
				<div class="input-group hide" id="search_tag">
				  <input class="input-group-field" type="text" id="txt_tag_search" placeholder="Refine your search by choosing adding a topic">
				  <div class="input-group-button">
					<input type="button" class="button" onclick='btn_tag_search();' value="Search" style="margin-top: 2px; margin-bottom: 1px;">
				  </div>
				</div>
				<div class="grid-x">
				  <div class="cell large-12" style="margin-top: 1rem;" id="searched_tag_div">
					<?php
					if ($_SESSION['tag'] != '') {
					  $searched_tag = $_SESSION['tag'];
					  $sel_tag_arr = explode(",", $searched_tag);
					  $sel_tag_arr = array_unique($sel_tag_arr);
					  foreach ($sel_tag_arr as $seltag) {
						echo '<a class="label tiny tag" onclick=remove_tag_index(this.text) data-closable>'; //onclick=remove_tag_index("'.$seltag.'")
						echo $seltag;
						echo '<button class="close-button"  aria-label="Close alert" type="button" data-close>';
						echo '<span aria-hidden="true">&times;</span>';
						echo '</button>';
						echo '</a>';
					  }
					}
					?>
				  </div>
				</div>
			  </div>
						  
			</div>
		  </div>
		  <?php 
		  if($_SESSION['tag']=='' && $_SESSION['filter']=='all')
		  {
			  $remove_index = '';
		  }
		  else
		  {
			 
			  if($_SESSION['match_tag']=='false' && $_SESSION['tag']!='')
			  {
				  $remove_index = '';
			  }
			  else
			  {
				  $remove_index = 'true';
			  }
		  }
		
		  ?>
		  <input type='hidden' name='tag_search_index_head' id='tag_search_index_head' value="<?php echo $remove_index;?>">
		  <?php
		  if (count($article_arr) > 0) {
			foreach ($article_arr as $article) {
		  ?>
			  <div class="article">
				<div class="article-structure callout">
				  <div class="grid-x grid-margin-x">
					<div class="cell large-8">
					   <?php
					   if($article['Content_type']=='article')
							echo '<span class="article-type article"></span>';
					   else
							echo '<span></span>';
					   if($article['Article_date']!='')
							echo '<span class="date">'.date("F d, Y", strtotime($article['Article_date'])).'</span>';
                       else
							echo '<span class="date"></span>';
					    ?>
					    <h2>
						<a href="<?php echo $article['url'] ?>" target="_blank" onclick=cookieurl(<?php echo '"' . $article['url'] . '"'; ?>) rel="nofollow">
						  <?php echo htmlspecialchars($article['Article_title'], ENT_QUOTES); ?>
						</a>
					  </h2>
					  <p>
						<?php echo htmlspecialchars($article['Article_desc'], ENT_QUOTES); ?>...
					  </p>
					  <a href="<?php echo $article['url'] ?>" target="_blank" class="link" onclick=cookieurl(<?php echo '"' . $article['url'] . '"'; ?>) rel="nofollow"><span><?php echo $article['domain'] ?></span>
					  </a>
					</div>
					<div class="cell large-4">
					  <?php
					  if ($article['Url_image'] != '')
						$urlimage = $article['Url_image'];
					  else
						$urlimage = '/img/no-image.svg';
					  ?>
					  <a href="<?php echo $article['url'] ?>" onclick=cookieurl(<?php echo '"' . $article['url'] . '"'; ?>) target="_blank" rel="nofollow">
						<?php echo $this->Html->image($urlimage,['class'=>'thumbnail']); ?>
					  </a>
					</div>
				  </div>
				  <div class="grid-x">
					<div class="cell large-12">
					  <hr>
					  <div class="grid-x grid-margin-x">
						<div class="cell large-6 end">
						  <div class="cell large-12">
							<?php
							if ($article['tag'] != null) {
							  $tagarr = explode(',', $article['tag']);
							  foreach ($tagarr as $tag) {
								echo '<a class="label tiny tag hollow" onclick=tagclick(this.text)>' . addslashes($tag) . '</a>';
							  }
							}
							?>
						  </div>
						</div>
					  </div>
					</div>
				  </div>
				  <div class="grid-x">
					<div class="cell large-12">
					  <div class="social float-right">
						<ul class="menu">
						  <li>
							<!--<a href="#" class="share">Share</a>
						   Go to www.addthis.com/dashboard to customize your tools -->
							<!--<div class="addthis_inline_share_toolbox"></div>---->
						  </li>
						  <li><a id="save_<?php echo $article['Article_id']; ?>" onclick="save_article_index(<?php echo $article['Article_id']; ?>,this.id);" class="bookmark">Save</a></li>
						</ul>
					  </div>
					</div>
				  </div>
				</div>
			  </div>
		  <?php
			}
		  } else {
			echo "<span>Sorry, there are no results for that.</span>";
			echo "<meta name='robots' content='noindex, noarchive'/>";
		  }
		  ?>
		  <!---<div class="article">
			<div class="article-structure callout">
			  <div class="grid-x grid-margin-x">
				<div class="cell large-8">
				  <span class="date">July 1, 2019</span>
				  <h2>AARP Retirement Calculator: Are You Saving Enough?</h2>
				  <a href="https://www.aarp.org/work/retirement-planning/retirement_calculator.html" target="_blank"
					class="link">https://www.aarp.org/work/retirement-planning/retirement_calculator.html</a>
				  <p>Find out when — and how — to retire the way you want. The AARP Retirement Calculator can provide
					you
					with a personalized snapshot of what your financial future might look like. Simply answer a few
					questions about your household status, salary and retirement savings, such as an IRA or 401(k).
				  </p>
				</div>
				<div class="cell large-4">
				  <img class="thumbnail"
					style="background: url(https://cdn.aarp.net/content/dam/aarp/Member-Benefits/2017/06/1140x641-aarp-programs-mbc-woman-on-computer-2020.imgcache.rev97641abfdebfaab5dd32a7affc5c2ff6.web.600.336.jpg)">
				</div>
			  </div>
			  <div class="grid-x">
				<div class="cell large-12">
				  <hr>
				  <div class="grid-x grid-margin-x">
					<div class="cell large-6 end">
					  <div class="cell large-12">
						<a href="#" class="label tiny tag hollow">Retirement Plan</a>
						<a href="#" class="label tiny tag hollow">Retirement Plan</a>
					  </div>
					</div>
				  </div>
				</div>
			  </div>
			  <div class="grid-x">
				<div class="cell large-12">
				  <div class="social float-right">
					<ul class="menu">
					  <li><a href="#" class="share">Share</a></li>
					  <li><a href="#" class="bookmark">Save</a></li>
					</ul>
				  </div>
				</div>
			  </div>
			</div>
		  </div>--->
		  <nav aria-label="Pagination">
			<ul class="pagination">
			<?php
			if(count($article_arr) > 0) 
			{
				$prev_dnc = preg_replace('/page=([5-9][0-9]|\d{3,})/','$0?dnc=1',$this->Paginator->prev('Previous'));
				$pagenum_dnc = preg_replace('/page=([5-9][0-9]|\d{3,})/','$0?dnc=1',$this->Paginator->numbers(['first' => 2, 'last' => 2]));
				$nxt_dnc = preg_replace('/page=([5-9][0-9]|\d{3,})/','$0?dnc=1',$this->Paginator->next('Next'));
				
				/*if($param_tag!='')
				{*/
					
					echo str_replace('?page=', '/', $prev_dnc);
					echo str_replace('?page=', '/', $pagenum_dnc);
					echo str_replace('?page=', '/', $nxt_dnc);
					
					
				/*}
				else
				{
					echo str_replace('?page=', '', $prev_dnc);
					echo str_replace('?page=', '', $pagenum_dnc);
					echo str_replace('?page=', '', $nxt_dnc);
					
				}*/
			}
			?>
			</ul>
		  </nav>
		</div>
		<div class="cell large-3">
			<div class="grid-x">
				<div class="cell large-12">
				  <div class="callout submit-content">
					<h4>Readers</h4>
					<p>Have an idea or feedback? Let us know.</p>
					
					<?php
					//removed the session value to force to contact page
					if ($_SESSION['userid'] != '')
					  echo $this->Html->link(
						'Contact Us',
						['controller' => 'users', 'action' => 'userDashboard'],
						['class' => 'button expanded']
					  );
					else
					  echo $this->Html->link(
						'Contact Us',
						['controller' => '', 'action' => 'contact'],
						['class' => 'button expanded']
					  );
					?>
				  </div>
				</div>
			</div>
		  
		  <div class="grid-x">
			<div class="cell large-12">
			  <div class="callout submit-content">
				<h4>Publishers</h4>
				<p>Submit your content to Seeking Retirement and get more readers.</p>
				
				<?php
				if ($_SESSION['userid'] != '')
				  echo $this->Html->link(
					'Contact Us',
					['controller' => 'users', 'action' => 'userDashboard'],
					['class' => 'button expanded']
				  );
				else
				  echo $this->Html->link(
					'Contact Us',
					['controller' => '', 'action' => 'contact'],
					['class' => 'button expanded']
				  );
				?>
			  </div>
			</div>
		  </div>
		  <!---<div class="grid-x">
		  <div class="cell large-12">
			<div class="callout">
			  Ads
			</div>
		  </div>
		</div>---->
		</div>
	  </div>
	</div>
</div>