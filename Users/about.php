
<!doctype html>
<html class="no-js" lang="en">
  <head>
    <meta charset="utf-8" />
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About - Seeking Retirement</title>
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

<div id="about">
    <?php include('header.php');?>
    <div class="off-canvas-content" data-off-canvas-content>
    <div class="hero text-center">
        <div class="grid-container">
            <div class="grid-x">
                <div class="cell large-12">
                    <h2>About</h2>
                </div>
            </div>
        </div>
    </div>
    <div class="content-container">
        <div class="grid-container">
            <div class="grid-x grid-margin-x">
                <!--
                <div class="cell large-5">
                    <img
                        src="https://images.unsplash.com/photo-1559734840-f9509ee5677f?ixid=MXwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHw%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=668&q=80">
                        
                </div>-->
                
                <div class="cell large-7 contact-text">
                    <br><br>
                    <h3>Retire Easier by Understanding Retirement </h3>
                    <p class="lead">If you are looking for information on retirement, it is hard to know where to start. There are thousands of articles about retirement online, but they are scattered across hundreds of different websites.
                        
                    <p class="lead">Seeking Retirement compiles the best retirement educational resources into one place so that you can easily find them all in one spot. Our goal with this project is to help retirement seekers and retirees make better decisions when planning their financial future and retire happier. </p>
                    
                                        <p class="lead">The Seeking Retirement website contains over 10,000 articles on a wide range of topics related to retirement including: Retirement Planning, Lifestyle, Saving & Investing, Retirement Accounts, Early Retirement/FIRE, Calculators, Taxes, Places to Retire and much more (see Browse Categories). Every article has been reviewed by an editor for accuracy and relevance before being added to our database. Once you find an article that interests you just click and learn.</p>
                                        
                                         <p class="lead">We hope you enjoy it.  </p>
                    
                     <p class="lead"><a href="https://seekingretirement.com/contact">Feedback welcome</a> </p>
                </div>
            </div>
        </div>
        <footer>
            <div class="grid-container">
                <div class="grid-x grid-margin-x">
                    <div class="cell large-12">
                        <ul class="menu align-right">
                            <li>
								<?php
								echo $this->Html->link(
								'About',
								['controller' => '', 'action' => 'about']
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
                          </ul>
                    </div>
                </div>
            </div>
        </footer>
    </div>
<!--<script src="assets/js/app.js"></script>--->
<?php
    echo $this->Html->script('app.js');
?>
</body>
</html>
