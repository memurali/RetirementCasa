<?php
if($flow=='editflow')
{
?>  
	<?php 
		echo $this->Form->create(null,['name' => 'frm_match_edit',
				'id' => 'frm_match_edit'],['data-abide'=>'','novalidate']); 
		$kwmatch = '';
		$matchid = [];
		if(count($matcharr)>0)
		{
			foreach($matcharr as $match)
			{
				$kwmatch.=$match['Kw_Phrase'].':'.$match['Kw_Group'];
				$kwmatch.="\n";
				$matchid[]=$match['Matchid'];
			}
		}
	
	?>
		<div class="grid-x">
			<div class="large-12">
			   <h3>Edit Match</h3>
			</div>
		</div>
		<div class="grid-x">
			<div class="large-12">
				<label>Keyword Search Phrase:</label>
				<textarea id="kwphr_edit" name="kwphr_edit" style="height: 10rem;"><?php echo htmlspecialchars($kwmatch,ENT_QUOTES); ?></textarea>
			</div>
			<input type="hidden" name="matchid_edit" id="matchid_edit" value="<?php echo implode(',', $matchid);?>">
		</div>
		<div class="grid-x modal-menu">
			<div class="cell medium-auto">
				<a onclick="popup_close('editmatch')" >Cancel</a>
			</div>
			<div class="cell medium-auto">
				<a  class="confirm" onclick="match_update_click();">Update</a>
			</div>
		  </div>
		<!--<button class="close-button" data-close aria-label="Close modal" type="button">
			<span aria-hidden="true">&times;</span>
		</button>--->
	<?php echo $this->Form->end(); ?>
<?php	
}
else
{
?>

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
	
<?php
}
?>