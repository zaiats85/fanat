<?php
/*
 * Add function to widgets_init that'll load our widget.
 */
add_action( 'widgets_init', 'full_post_widget' );
/* Function that registers our widget. */
function full_post_widget() {
	register_widget( 'full_posts' );
}
class full_posts extends WP_Widget {
	function full_posts() {
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'full_posts', 'description' => 'Displays the post image and title.'.'pmc-themes' );
		/* Create the widget. */
		parent::__construct( 'full_posts-widget','Everest - Premium full width Posts', $widget_ops, '' );
	}
	function widget( $args, $instance ) {
		global $pmc_data;
		extract( $args );
		/* User-selected settings. */
		$title = apply_filters('widget_title', $instance['title'] );
		// to sure the number of posts displayed isn't negative or more than 10
		if ( !$number = (int) $instance['number'] )
			$number = 8;

		//the query that will get post from a specific category. 
		//Wr slug the category because you actualy need the slug and not the name
		$pc = new WP_Query(array('orderby'=>'date','post_type' => 'post' , 'showposts' => $number, 'nopaging' => 0, 'post_status' => 'publish', 'ignore_sticky_posts' => 1 ,'tax_query' => array(
                    array(
                        'taxonomy' => 'post_format',
                        'field' => 'slug',
                        'terms' => array('post-format-link'),
                        'operator' => 'NOT IN'
                    )
                )));
		
		//display the posts title as a link
		
		if ($pc->have_posts()) : 
			echo $before_widget; 
		
			if ( $title ) echo $before_title . $title . $after_title; 
			$i = $j = 0;
			?>			
			<ul class="post-widget-slider">
			<?php
			while ($pc->have_posts()) : $pc->the_post();  
			$i++;
			$images_class = array(2,3);


			?>	
				<?php
					
					$image=$comment_0=$comment_1=$comment_2= '';
					if ( has_post_thumbnail() ){
						if(in_array($i, $images_class)) { 
							$image = wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()), 'pmc-post-widget-even', false);
						}
						else{
							$image = wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()), 'pmc-post-widget-odd', false);
						}
						
						$image = $image[0];
					}	
					?>		
					<?php if ($i == 1){ ?>
						<li>
					<?php } ?>
					<div class="post-holder <?php if(in_array($i, $images_class)) {echo 'even'; } else {  echo 'odd';} ?>">
						<a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>">
						<div class="post-widget-image">
							
								<?php if (has_post_thumbnail( get_the_ID() )) { ?> <img src = "<?php echo $image?>" alt = " <?php echo esc_attr(get_the_title() ? get_the_title() : get_the_ID()); ?>"  <?php if(in_array($i, $images_class)) {echo ' width="360" height = "300" '; } else {  echo ' width="720" height = "300" ';} ?> > <?php } ?>	
							
						</div>
						</a>
						<div class="post-widget-title">
							<h2><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>"><?php the_title(); ?></a></h2>
							<div class="post-widget-category"><?php echo  get_the_category_list( esc_html__( ', ', 'pmc-themes' ) ); ?></div>
						</div>
						
					</div>
					<?php if ($i == 4){ 
					$i = 0;
					?>
					</li>
					<?php } ?>
			
			<?php 
	
			endwhile; 
			?>
			</ul>
		
		
	<?php
			wp_reset_query();  // Restore global post data stomped by the_post().
			endif;
		echo $after_widget;
	}
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		/* Strip tags (if needed) and update the widget settings. */
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['number'] = $new_instance['number'];
		
		return $instance;
	}
	function form( $instance ) {
		/* Set up some default widget settings. */
		$defaults = array( 'title' => 'Recent Posts', 'number' => 5);
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>">Title:</label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php esc_attr($instance['title']); ?>" />
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id( 'number' ); ?>">Number of posts to show:</label>
			<input id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" value="<?php echo $instance['number']; ?>" size="3" />
			<br /><small>(4 per slide)</small>
		</p>
		<?php
	}
	function slug($string)
	{
		$slug = trim($string);
		$slug= preg_replace('/[^a-zA-Z0-9 -]/','', $slug); // only take alphanumerical characters, but keep the spaces and dashes too...
		$slug= str_replace(' ','-', $slug); // replace spaces by dashes
		$slug= strtolower($slug); // make it lowercase
		return $slug;
	}
}
?>
