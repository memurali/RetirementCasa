
<!doctype html>
<html id='html_id' class="no-js" lang="en">
  <head>
    <meta charset="utf-8" />
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Retirement Rover</title>
    <meta name="robots" content="noindex">
    <?php
        echo $this->Html->css('app.css');
        echo $this->Html->script('jquery.js');
       // echo $this->Html->script("jquery.dataTables.min.js");
        //echo $this->Html->script("dataTables.foundation.min.js");
        echo $this->Html->script('common.js');
    ?>
    <script>
        $(document).ready(function() {
            var i =1;
            $.each($('.reveal-overlay'), function (index, value) {
                $(this).attr('id', 'popup'+i);
                i++;
            });
            
            $("#txt_user").on("keyup", function() {
                var value = $(this).val().toLowerCase();
                $("#tbodysort tr").filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                });
            });
        });
    </script>
   
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Ubuntu:wght@300;400;500;700&display=swap" rel="stylesheet">

    <div class="reveal tiny" id="do_mute" data-reveal>
      <div class="grid-x">
        <div class="cell large-12">
          <h3>Are You Sure to mute?</h3>
        </div>
      </div>
      <div class="grid-x modal-menu">
        <div class="cell medium-auto">
          <a onclick="popup_close('do_recrawl')">Cancel</a>
        </div>
        <div class="cell medium-auto">
          <a onclick="user_process('mute');" id="btnconfirm_mute" class="confirm">Confirm</a>
        </div>
      </div>
      <button class="close-button" data-close aria-label="Close modal" type="button">
        <span aria-hidden="true">&times;</span>
      </button>
    </div>
    <div class="reveal tiny" id="do_unmute" data-reveal>
      <div class="grid-x">
        <div class="cell large-12">
          <h3>Are You Sure to unmute?</h3>
        </div>
      </div>
      <div class="grid-x modal-menu">
        <div class="cell medium-auto">
          <a onclick="popup_close('do_delete')">Cancel</a>
        </div>
        <div class="cell medium-auto">
          <a onclick="user_process('unmute');" id="btnconfirm_unmute" class="confirm">Confirm</a>
        </div>
      </div>
      <button class="close-button" data-close aria-label="Close modal" type="button">
        <span aria-hidden="true">&times;</span>
      </button>
    </div>
    <div class="reveal tiny" id="do_delete" data-reveal>
      <div class="grid-x">
        <div class="cell large-12">
          <h3>Are You Sure to delete?</h3>
        </div>
      </div>
      <div class="grid-x modal-menu">
        <div class="cell medium-auto">
          <a onclick="popup_close('do_delete')">Cancel</a>
        </div>
        <div class="cell medium-auto">
          <a onclick="user_process('delete');" id="btnconfirm_delete" class="confirm">Confirm</a>
        </div>
      </div>
      <button class="close-button" data-close aria-label="Close modal" type="button">
        <span aria-hidden="true">&times;</span>
      </button>
    </div>
    <div class="reveal tiny" id="popup_error" data-reveal>
        <div class="grid-x">
            <div class="cell large-12">
            <h3>Please select any users</h3>
            </div>
        </div>
        <button class="close-button" data-close aria-label="Close modal" type="button">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
  </head>
<body>

<div class="reveal tiny" id="editUser" data-reveal>
    <div class="grid-x">
        <div class="cell large-12 text-center">
            <?php echo $this->Html->image("../assets/img/loading.gif" , array('class' => 'loading-gif'));?>
            <!--<img src="assets/img/loading.gif" class="loading-gif">--->
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
                        'Retirement Rover',
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
                        <span class="alert"><?php echo $approvalcount;?></span>
                    </li>
                    <!--<li><a href="admin_dashboard-staging.html" class="staging">Staging</a><span class="alert">10</span></li>
                    <li><a href="admin_dashboard-users.html" class="users">Users</a></li>---->
                    <li><a href="#" class="users active">Users</a></li>
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
                                    <div class="input-group">
                                        <input class="input-group-field" name="txt_user" id="txt_user" type="text" placeholder="Search User" autocomplete="off">
                                        <div class="input-group-button">
                                            <input type="submit" id='btn_usersearch' class="button" value="Search">
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
            <div class="grid-container" id='tbldata'>
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
                                <div >
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
            </div>
        </div>
    </div>
</div>
<!----<script src="assets/js/app.js"></script>------>
<?php
    echo $this->Html->script('app.js');
    
?>
</body>
</html>
