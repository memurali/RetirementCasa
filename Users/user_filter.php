<?php
echo $this->Html->script('common.js');
if($render =='savedarticle')
{
?>    
    <div class="grid-container">
        <div class="dashboard-content">
            <div class="grid-x grid-margin-x">
                <div class="cell large-12 end">
                    <div class="callout table-content">
                        <div class="float-left">
                            <label>
                                <select id="savedshow_records" style="font-size: 13px;">                                    
                                    <option value="20" <?php echo ($limit==20) ? 'selected' : 'notselected';?>>Show 20</option>
                                    <option value="50" <?php echo ($limit==50) ? 'selected' : 'notselected';?>>Show 50</option>
                                    <option value="100" <?php echo ($limit==100) ? 'selected' : 'notselected';?>>Show 100</option>
                                </select>
                            </label>
                        </div>
                        <div class="button-group float-right">
                            <a class="button alert" data-open="saveddelete">Delete</a>
                        </div>                                    
                        <table class="stack" id="tblsort">
                            <thead>
                                <tr>
                                    <th width="30"><input id="CheckAll" class="table-checkboxes" type="checkbox"></th>
                                    <th width="78">Date<a onclick="sortTable(0,'date')" class="sort"></a></th>
                                    <th width="100">Type</th>
                                    <th width="500">URL</th>
                                    <th>Tags</a></th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                            if(count($saved_arr)>0)
                            {
                                $type="userarticle";
                                foreach($saved_arr as $arr)
                                {
                                    $tags = explode(',',$arr['tag']);
                                    echo '<tr>';
                                    echo '<th><input class="table-checkboxes" type="checkbox" value="'.$arr['Article_id'].'"></th>';
                                    if($arr['Article_date']!='')
										echo '<td>'.date('m/d/y',strtotime($arr['Article_date'])).'</td>';
									else
										echo '<td></td>';
                                    echo '<td>Article</td>';
                                    echo '<td class="url"><a href="'.$arr['Url'].'" target="_blank">'.$arr['Url'].'</a></td>';
                                    echo '<td class="tags">';                                           
                                            foreach($tags as $tag)
                                            {
                                                echo '<a href="" target="_blank">'.$tag.'</a>';                                                   
                                            }
                                    echo '</td>';
                                    echo '<td class="float-right">                                               
                                            <ul class="dropdown menu" data-dropdown-menu>
                                                <li>
                                                    <a href=""></a>
                                                    <ul class="menu">
                                                        <li><a onclick=user_delete('.$arr["Article_id"].',"'.$type.'")>Delete</a></li>                                                           
                                                    </ul>
                                                </li>
                                            </ul>
                                        </td>';
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
else
{
?>
    <table class="stack">
        <thead>
            <tr>
                <th width="78">Date</th>
                <th width="">Article</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
        <?php
        if(count($saved_arr)>0)
        {
            foreach($saved_arr as $arr)
            {
                echo '<tr>';
                echo '<td>'.date('m/d/y',strtotime($arr['Article_date'])).'</td>';
                echo '<td class="url"><a href="'.$arr['Url'].'" target="_blank">'.$arr['Url'].'</a></td>';
                echo '<td>
                        <ul class="dropdown menu" data-dropdown-menu>
                            <li>
                            <a></a>
                            <ul class="menu">
                                <li><a onclick="user_delete('.$arr['Article_id'].')">Delete</a></li>                                                        
                            </ul>
                            </li>
                        </ul>
                    </td>';
                echo '</tr>';
            }
        }
        ?>
       </tbody>
    </table>
<?php
}
exit;
?>