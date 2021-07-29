<?php
    echo $this->Html->script('common.js');
    if($render=='')
    {
?>
     <div class="dashboard-content">
                    <div class="grid-x grid-margin-x grid-padding-y">
                        <div class="cell large-12">
                            <div class="float-left">
                                <h2>Users</h2>
                            </div>
                            <div class="float-right">
                                <ul class="menu align-right data-info" style="padding-top: 1rem;">
                                    <li class="callout total-users">
                                        <label><strong>Total Users</strong>
                                            <p><?php echo $user_count;?></p>
                                        </label>
                                    </li>
                                    <li class="callout new-users">
                                        <label><strong>New Users</strong>
                                            <p><?php echo $countarr[0]['count']; ?></p>
                                        </label>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="grid-x grid-margin-x">
                        <div class="cell large-12">
                            <div class="callout table-content">
                                <div class="float-left">
                                    
                                    <label>
                                        <select id='admin_user_range' onchange='admin_user_range();' style="font-size: 13px;">
                                            <option value="20" <?php echo ($limit==20) ? 'selected' : 'notselected';?>>Show 20</option>
                                            <option value="50" <?php echo ($limit==50) ? 'selected' : 'notselected';?>>Show 50</option>
                                            <option value="100" <?php echo ($limit==100) ? 'selected' : 'notselected';?>>Show 100</option>
                                        </select>
                                    </label>
                                </div>
                                <div class="button-group float-right">
                                    <a class="button secondary" data-open='do_mute'>Mute</a>
                                    <a class="button secondary" data-open='do_unmute'>UnMute</a>
                                    <a class="button alert" data-open='do_delete'>Delete</a>
                                </div>
                                <div id='tbldata'>
                                    <table id="tblsort" class="stack">
                                        <thead>
                                            <tr>
                                                <th width="30"><input id="checkAll_live" class="table-checkboxes" type="checkbox"></th>
                                                <th width="78">Date<a onclick="sortTable(0,'date')" class="sort"></a></th>
                                                <th width="">First Name</th>
                                                <th width="">Last Name</th>
                                                <th>Email</th>

                                                <th></th>
                                                <th class="float-right"><a onclick="sortTable(5)" class="sort"></a></th>
                                            </tr>
                                        </thead>
                                        <tbody id="tbodysort">
                                            <?php 
                                            if(count($allusers)>0)
                                            {
                                                $i=1;
                                                foreach ($allusers as $users)
                                                {
                                                    echo '<tr>';
                                                        echo '<th width=30><input class="table-checkboxes" type="checkbox" value='.$users[Userid].'></th>';
                                                        echo '<td>'.date("m/d/y",strtotime($users[date])).'</td>';
                                                        echo '<td>'.$users[First_name].'</td>';
                                                        echo '<td>'.$users[Last_name].'</td>';
                                                        echo '<td>'.$users[Email].'</td>';
                                                        echo '<td><a class="label tiny secondary" onclick=edituser_view('.$users[Userid].') data-open="editUser">Edit</a></td>';
                                                        if($users['Status']=='y')
                                                        {
                                                            $checked = 'checked';
                                                            $active_val = 1;
                                                        }
                                                        else
                                                        {
                                                            $checked = '';
                                                            $active_val = 0;
                                                        }
                                                        echo '<td class="text-right"><input type=hidden value='.$active_val.'>
                                                                <div class="switch tiny">
                                                                    <input class="switch-input" id="tinySwitch'.$i.'" onchange="change_userstatus('.$users[Userid].',this);" type="checkbox" '.$checked.'>
                                                                    <label class="switch-paddle" for="tinySwitch'.$i.'">
                                                                        <span class="show-for-sr">Tiny Sandwiches Enabled</span>
                                                                    </label>
                                                                </div>
                                                            </td>';
                                                    echo '</tr>';
                                                    $i++;
                                                }
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>   
    <?php
    }
   else
   {?>

         <div class="grid-x">
            <div class="large-12">
                <h3>Edit Users</h3>
            </div>
        </div>
        <div class="grid-x">
            <div class="cell large-12">
                <?php 
                    echo $this->Form->create(null,['name' => 'frmedit',
                            'id' => 'frmedit'],['data-abide'=>'','novalidate','url' => [
                            'controller' => 'Users',
                            'action' => 'admin_dashboard_users_filter',
                            
                            ]
                    ]); 
                ?>
                    <label>First Name
                        <input type="text" name='user_fname' id='user_fname' value='<?php echo $userarr[0][First_name];?>'>
                    </label>
                    <label>Last Name
                        <input type="text" name='user_lname' id='user_lname' value='<?php echo $userarr[0][Last_name];?>'>
                    </label>
                    <label>Email
                        <input type="text" name='user_email' id='user_email' value='<?php echo $userarr[0][Email];?>'>
                        <input type="hidden" name='user_id' id='user_id' value='<?php echo $userarr[0][Userid];?>'>
                    </label>
                <?php echo $this->Form->end(); ?>
            </div>
        </div>
        <div class="grid-x modal-menu">
            <div class="cell medium-auto">
                <a onclick='edituser_process("save")'>Save</a>
            </div>
            <div class="cell medium-auto">
                <?php
                if($userarr[0][Status]=='y')
                    echo '<a onclick=edituser_process("mute")>Mute</a>';
                else
                    echo '<a onclick=edituser_process("unmute")>UnMute</a>';
                ?>
            </div>
            <div class="cell medium-auto">
                <a class="confirm" onclick="edituser_process('delete')">Delete</a>
            </div>
        </div>
        <button class="close-button" data-close aria-label="Close modal" type="button">
            <span aria-hidden="true">&times;</span>
        </button>
   <?php
   }
   exit;?>
