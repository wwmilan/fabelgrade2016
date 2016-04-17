</div><!-- end main container -->

<div class="footer-container">			
	<footer class="short footer">
		<div class="container">
		
			<?php 
				if( is_active_sidebar('footer1') ) 
					get_template_part('inc/content','widgets'); 
			?>
			
<?php
            /*
			 * <div class="row">
			 * 
			 * 	<div class="col-sm-3">
			 * 		<?php
			 * 			// 
			 * 			// Subfooter nav menu, allows top level items only
			 * 			//
			 * 			if ( has_nav_menu( 'footer' ) ) { 
			 * 			    wp_nav_menu( 
			 * 				    array(
			 * 				        'theme_location'    => 'footer',
			 * 				        'depth'             => 1,
			 * 				        'container'         => false,
			 * 				        'container_class'   => false,
			 * 				        'menu_class'        => 'menu'
			 * 				    ) 
			 * 			    );
			 * 			}
			 * 		?>
			 * 	</div>
	
			 * 	<div class="col-sm-6">
			 * 		<span class="text-white"><?php echo htmlspecialchars_decode(get_option('subfooter_text','FABelgrade2016')); ?></span>
			 * 	</div>

             *     <div class="col-sm-3 text-right">
             *         <div class="footer-contact"><a href="mailto:contact@fabelgrade.io">contact@fabelgrade.io</a></div>
             *         <div class="footer-contact"><a href="tel:+38162123456">+381 62 123456</a></div>
             *         <div class="footer-contact">Belgrade, Serbia</div>
             *     </div>

			 * </div><!--end of row-->
             */
?>
			
		</div><!--end of container-->
	</footer>
<script type="text/javascript" charset="utf-8">
    var footerCols = jQuery('footer .container .row > div:not([class="clear"])');
    jQuery(footerCols[0]).addClass('first');
    jQuery(footerCols[footerCols.length-1]).addClass('last');
</script>
</div>
		
<?php wp_footer(); ?>
</body>
</html>
