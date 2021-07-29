
<!doctype html>
<html class="no-js" lang="en">
  <head>
    <meta charset="utf-8" />
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Retirement Rover</title>
    <!--<link rel="stylesheet" href="assets/css/app.css">--->
    <?php
        echo $this->Html->css('app.css');
        echo $this->Html->script('jquery.js');
        echo $this->Html->script('common.js');
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

<div id="about" class="marketing">
    <div class="top-bar">
        <div class="grid-container" style="width: 100%;">
            <div class="grid-x">
                <div class="cell small-6">
                   <ul class="menu">
                        <a href="../">
                            <li class="menu-text"></li>
                        </a>
                    </ul>
                </div>
                <div class="cell small-6">
                    <ul class="dropdown menu float-right" data-dropdown-menu>
                        <li>
                            <?php    
                            echo $this->Html->link(
                            'About',
                            ['controller' => 'users', 'action' => 'aboutus']
                            );
                            ?>
                        </li>
                        <li>
                            <?php    
                            echo $this->Html->link(
                            'Contact',
                            ['controller' => 'users', 'action' => 'contact']
                            );
                            ?>
                        </li>
                        <li>
                            <?php 
                            if($_SESSION['userid']=='')   
                                echo $this->Html->link(
                                'Sign In',
                                ['controller' => 'users', 'action' => 'signin'],
                                ['class'=>'button hollow']
                                );
                            else
                                echo $this->Html->link(
                                'Sign Out',
                                ['controller' => 'users', 'action' => 'signout'],
                                ['class'=>'button hollow']
                                );
                            ?>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="hero text-center">
        <div class="grid-container">
            <div class="grid-x">
                <div class="cell large-12">
                    <h2>About Us</h2>
                </div>
            </div>
        </div>
    </div>
    <div class="content-container">
        <div class="grid-container">
            <div class="grid-x grid-margin-x">
                <div class="cell large-5">
                    <img
                        src="https://images.unsplash.com/photo-1514415008039-efa173293080?ixid=MXwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHw%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=1652&q=80">
                </div>
                <div class="cell large-7 contact-text">
                    <h3>Morbi leo risus, porta ac consectetur ac, vestibulum at eros.!</h3>
                    <p class="lead">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam id dolor id nibh
                        ultricies vehicula ut id elit. Praesent commodo cursus magna, vel scelerisque nisl consectetur
                        et. Integer posuere erat a ante venenatis dapibus posuere velit aliquet. Donec id elit non mi
                        porta gravida at eget metus. Aenean lacinia bibendum nulla sed consectetur.</p>
                </div>
            </div>
        </div>
        <footer>
            <div class="grid-container">
                <div class="grid-x grid-margin-x">
                    <div class="cell large-12">
                        <ul class="menu align-right">
                            <li><a href="#">Contact</a></li>
                            <li><a href="#">Privacy</a></li>
                            <li><a href="#">Terms</a></li>
                          </ul>
                    </div>
                </div>
            </div>
        </footer>
    <?php
    echo $this->Html->script('app.js');
    ?>
</body>
</html>
