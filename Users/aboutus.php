
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
                    <h3>Retiring is not as easy as you may think. </h3>
                    <p class="lead">Retiring means different things to different people. So just figuring out what retirement means to you is a huge achievement.</p>
                    <p class="lead">Retiring often means having enough money and not having to work. Well, this is different for everyone. There is no one answer. There are a multitude of factors to consider: lifestyle, income, expenses, health, family, the list goes on.</p>
                    <p class="lead">Understanding retirement and the myriad of choices is daunting and time consuming. Those who have started their retirement journey to educate themselves will have noticed there is a lot of information everywhere. Where do you start? What is most useful? What is trustworthy?</p>
                    <p class="lead">This is where Seeking Retirement comes into play. Seeking Retirement is a search engine for retirement knowledge. It crawls the web for retirement information (blogs, videos, podcasts, etc), and organizes it into topics or tags. With a simple click of  a tag, you can find the best content about a particular subject: 401ks, saving money, expenses, taxes, annuities, etc. Moreover, as the power of a community, you can rate the content to help other users find the best content, saving time for all.</p>
                    <p class="lead">Seeking Retirement launched in June 2021 - beta. There are a lot more features in the works. We hope you find it useful and welcome your <a href="https://seekingretirement.com/users/contact">feedback</a>. </p>
                </div>
            </div>
        </div>
        <footer>
            <div class="grid-container">
                <div class="grid-x grid-margin-x">
                    <div class="cell large-12">
                        <ul class="menu align-right">
                            <li><a href="aboutus">About</a></li>
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
<script src="assets/js/app.js"></script>
<?php
    echo $this->Html->script('app.js');
    ?>
</body>
</html>
