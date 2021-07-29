
<!doctype html>
<html class="no-js" lang="en">
  <head>
    <meta charset="utf-8" />
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Retirement Rover</title>
    <meta name="robots" content="noindex">
    <!--<link rel="stylesheet" href="assets/css/app.css">--> 
    <?php
        echo $this->Html->css('app.css');
        echo $this->Html->script('jquery.js');
        echo $this->Html->script('common.js');
        echo $this->Html->script('sort.js');         
    ?>   
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Ubuntu:wght@300;400;500;700&display=swap" rel="stylesheet">

    <div class="reveal tiny" id="confirmation" data-reveal>
      <div class="grid-x">
        <div class="cell large-12">
          <h3>Are You Sure?</h3>
        </div>
      </div>
      <div class="grid-x modal-menu">
        <div class="cell medium-auto">
          <a href="#">Cancel</a>
        </div>
        <div class="cell medium-auto">
          <a href="#" class="confirm">Confirm</a>
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
                  <option value="article">Article</option>
                  <option value="Video">Video</option>
                  <option value="Podcast">Podcast</option>
                </select>
              </label>
        </div>
    </div>
    <div class="article-structure">
        <div class="grid-x">
            <div class="cell large-12 text-center hide">
                <!--<img src="assets/img/loading.gif" class="loading-gif">-->
                <?php echo $this->Html->image("../assets/img/loading.gif" , array('class' => 'loading-gif'));?>
            </div>
        </div>
        <div class="grid-x grid-margin-x">
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
        </div>
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
                    <li>
                        <?php
                        echo $this->Html->link(
                        'Dashboard',
                        ['controller' => 'users', 'action' => 'adminDashboard'],
                        ['class'=>'dashboard active']
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
                    <li>
                        <?php    
                        echo $this->Html->link(
                        'Users',
                        ['controller' => 'users', 'action' => 'adminDashboardUsers'],
                        ['class'=>'users']
                        );
                        ?>
                    </li> 
                    <!--<li><a href="#" class="dashboard active">Dashboard</a></li>                
                    <li><a href="admin_dashboard-staging.html" class="staging">Staging</a><span class="alert">10</span></li>
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
                    <div class="grid-x grid-margin-x">
                        <div class="cell medium-auto">
                            <div class="callout data-callout">
                                <label><strong>Needs Approval</strong>
                                    <a id='click_approval'><p><?php echo $approvalcount;?></p></a>
                                </label>
                            </div>
                        </div>
                        <div class="cell medium-auto">
                            <div class="callout">
                                <div class="grid-x">
                                    <div class="cell small-6">
                                        <div class="data-callout">
                                            <label><strong>Total Domains</strong>
                                                <p><?php echo $domaincount;?></p>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="cell small-6">
                                        <div class="data-callout">
                                            <label><strong>Total Articles</strong>
                                                <p><?php echo $articlecount;?></p>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="cell medium-auto">
                            <div class="callout data-callout">
                                <label><strong>Total Users</strong>
                                    <p><?php echo $user_count;?></p>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="grid-x grid-margin-x">
                        <div class="cell large-7">
                            <div class="callout table-content">
                                <div class="float-left"> 
                                    <h4>New Users</h4>
                                </div>
                                <div class="button-group float-right">
                                   <!-- <a class="button secondary">Mute</a>
                                    <a class="button alert">Delete</a>-->
                                </div>
                                <table class="stack">
                                    <thead>
                                        <tr>
                                            <th width="78">Date</th>
                                            <th width="">First Name</th>
                                            <th width="">Last Name</th>
                                            <th width="">Email</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if(count($userarr)>0)
                                        {
                                            for($i=0;$i<count($userarr);$i++)
                                            {
                                                echo '<tr>';
                                                echo '<td>'.date('m/d/y',strtotime($userarr[$i]['date'])).'</td>';
                                                echo '<td>'.$userarr[$i]['First_name'].'</td>';
                                                echo '<td>'.$userarr[$i]['Last_name'].'</td>';
                                                echo '<td>'.$userarr[$i]['Email'].'</td>';
                                                echo '</tr>';
                                            } 
                                        } 
                                        ?>                     
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="cell large-5">
                            <div class="callout table-content">
                                <h4>Top Tags</h4>
                                <table class="stack unstriped" id="tblsort" >
                                    <thead>
                                        <tr>
                                            <th width="78" >#<a class="sort" onclick="sortTable(0,'number')"></a></th>
                                            <th>Tag</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        
                                       <?php
                                        $size = sizeof($tags_arr);
                                        if($size>0)
                                        {
                                            for($i=0;$i<$size;$i++)
                                            {
                                                echo '<tr>';
                                                    echo '<td>'.$tags_arr[$i]['cnt'].'</td>';
                                                    echo '<td>'.$tags_arr[$i]['Tags'].'</td>';
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
