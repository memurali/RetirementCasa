<!doctype html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact - Seeking Retirement</title>
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

    <div id="contact">
        <?php include('header.php'); ?>
        <div class="off-canvas-content" data-off-canvas-content>
            <div class="hero text-center">
                <div class="grid-container">
                    <div class="grid-x">
                        <div class="cell large-12">
                            <h2>Contact</h2>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content-container">
                <div class="grid-container">
                    <div class="grid-x grid-margin-x">
                        <div class="cell large-7 contact-text">
                            <!---class="cell large-7 contact-text" --->
                            <h3>Send us a note</h3>
                            <p class="lead"></p>
                        </div>
                        <div class="cell large-5">
                            <div class="grid-x">
                                <div class="cell large-12">
                                    <div class="callout contact-form">
                                        <?php
                                        echo $this->Form->create(null, [
                                            'data-abide' => '', 'novalidate',
                                            'url' => [
                                                'controller' => '',
                                                'action' => 'contact'
                                            ]
                                        ]);

                                        ?>
                                        <?php
                                        if ($_POST['email'] == '') {
                                        ?>
                                            <div class="">
                                                <div class="cell large-12">
                                                    <label>First Name
                                                        <input type="text" id="firstName" name="firstName" placeholder="First Name" required>
                                                    </label>
                                                </div>
                                                <div class="cell large-12">
                                                    <label>Last Name
                                                        <input type="text" id="lastName" name="lastName" placeholder="Last Name" required>
                                                    </label>
                                                </div>
                                                <div class="cell large-12">
                                                    <label>Email
                                                        <input type="email" id="email" name="email" placeholder="Email" required>
                                                    </label>
                                                </div>
                                                <div class="cell large-12">
                                                    <label>
                                                        Message<br><small>If you would like to submit an article, please include
                                                            the link in your message.</small>
                                                        <textarea name="contact_text" placeholder=""></textarea>
                                                    </label>
                                                </div>
                                                <div class="cell large-12">
                                                    <button class="button large" type="submit" value="Submit">Submit</button>
                                                </div>
                                            </div>
                                        <?php
                                        } else {
                                        ?>
                                            <div class="thank-you">
                                                <div class="cell large-12">
                                                    <h4>Thank you for getting in touch!</h4>
                                                    <p>We appreciate you contacting us.</p>
                                                    <p>We will get back in touch with you soon! </p>
                                                    <p>Have a great day!</p>
                                                </div>
                                            </div>
                                        <?php
                                        } ?>
                                        <?php echo $this->Form->end(); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
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
                                        ['controller' => '', 'action' => 'contact']
                                    );
                                    ?>
                                </li>

                            </ul>
                        </div>
                    </div>
                </div>
            </footer>
            <script src="assets/js/app.js"></script>
            <?php
    echo $this->Html->script('app.js');
    ?>
</body>

</html>