<?php
        echo $this->Html->css('app.css');
		echo $this->Html->css('jquery.classysocial.min.css');
        echo $this->Html->script('jquery.js');
		echo $this->Html->script('common.js');
        echo $this->Html->script('jquery.classysocial.min.js');
    ?>
<script>
$(document).ready(function()
{	
	$(".classysocial").ClassySocial();	
});
</script>

<div class="classysocial" data-orientation="line" data-picture="images/share_core_square.jpg" data-facebook-handle="picozu" data-socl-handle="marius-stanciu" data-twitter-handle="picozu_editor" data-email-handle="office@picozu.net" data-pinterest-handle="picozu" data-instagram-handle="picozu" 
data-whatsapp-handle="picozu" data-networks="socl,facebook,twitter,instagram,pinterest,email,whatsapp">
</div>
<?php
    echo $this->Html->script('app.js');
    
?>
