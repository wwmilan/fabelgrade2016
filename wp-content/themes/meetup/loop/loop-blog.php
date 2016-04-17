<section>
	<div class="container">
		<div class="row">
		
			<div class="col-md-8 col-sm-12">
				<?php 
					if ( have_posts() ) : while ( have_posts() ) : the_post();
						
						/**
						 * Get blog posts by blog layout.
						 */
						get_template_part('loop/content', 'post');
					
					endwhile;	
					else : 
						
						/**
						 * Display no posts message if none are found.
						 */
						get_template_part('loop/content','none');
						
					endif;
					
					/**
					* Post pagination, use ebor_pagination() first and fall back to default
					*/
					echo function_exists('ebor_pagination') ? ebor_pagination() : posts_nav_link();
				?>
			</div>
			
			<?php get_sidebar(); ?>
			
		</div>
	</div>
</section>