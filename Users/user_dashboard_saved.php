
<!doctype html>
<html class="no-js" id="html_id" lang="en">
  <head>
    <meta charset="utf-8" />
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Foundation for Sites</title>
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
        $('#article_searchdiv').keypress(function (e) {
            var key = e.which;
            if(key == 13)  // the enter key code
            {
                article_search();
                return false;  
            }
        });   
        
     });
    </script>
    <div class="reveal tiny" id="saveddelete" data-reveal>
      <div class="grid-x">
        <div class="cell large-12">
          <h3>Are You Sure?</h3>
        </div>
      </div>
      <div class="grid-x modal-menu">
        <div class="cell medium-auto">
          <a onclick="popup_close('saveddelete')">Cancel</a>
        </div>
        <div class="cell medium-auto">
          <a  class="confirm" onclick="savedarticle('delete')">Confirm</a>
        </div>
      </div>
      <button class="close-button" data-close aria-label="Close modal" type="button">
        <span aria-hidden="true">&times;</span>
      </button>
  </div>
  <div class="reveal tiny" id="savederror" data-reveal>
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

<div class="dashboard-layout">
    <div class="grid-x">
        <div class="cell large-2">
            <div class="callout side-nav show-for-large">
                <ul class="vertical menu">
                    <li>
                        <?php
                            echo $this->Html->link(
                            'Retirement Casa',
                            ['controller' => 'users', 'action' => 'index'],
                            ['class'=>'title']
                            );
                        ?>
                    </li>
                    <li>
                        <?php
                        echo $this->Html->link(
                        'Dashboard',
                        ['controller' => 'users', 'action' => 'userDashboard'],
                        ['class'=>'dashboard']
                        );
                        ?>
                    </li>
                    <li>
                        <?php
                        echo $this->Html->link(
                        'Saved Articles',
                        ['controller' => 'users', 'action' => 'userDashboardSaved'],
                        ['class'=>'live active']
                        );
                        ?>
                    </li>
                    <!--<li><a href="user_dashboard.html" class="dashboard">Dashboard</a></li>
                    <li><a href="#" class="live active">Saved Articles</a></li>-->
                    <li><a href="#" class="settings">Settings</a></li>
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
                                    <div class="input-group" id="article_searchdiv">
                                        <input class="input-group-field" id="savedsearch" type="text" placeholder="Search Articles" value='<?php echo $search;?>' autocomplete="off">
                                        <div class="input-group-button">
                                            <input type="submit" class="button" onclick='article_search();' id="article_search" value="Search" >
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
            <div id="savedrender">
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
                                        <!--<li class="current"><span class="show-for-sr">You're on page</span> 1</li>
                                        <li><a href="#" aria-label="Page 2">2</a></li>
                                        <li><a href="#" aria-label="Page 3">3</a></li>
                                        <li><a href="#" aria-label="Page 4">4</a></li>
                                        <li class="ellipsis" aria-hidden="true"></li>
                                        <li><a href="#" aria-label="Page 12">12</a></li>
                                        <li><a href="#" aria-label="Page 13">13</a></li>
                                        <li class="pagination-next"><a href="#" aria-label="Next page">Next <span class="show-for-sr">page</span></a></li>--->
                                        <?php
                                            echo $this->Paginator->prev('Previous'); 
                                            //echo $this->Paginator->first(3);
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
