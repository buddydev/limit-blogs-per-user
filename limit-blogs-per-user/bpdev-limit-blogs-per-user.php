<?php

	class BPDevLimitBlogsPerUser {
		private static $instance;
		
		/**
		 * Private constructor, to avoid multiple instances
		 */
		private function __construct() {
			add_filter( 'wpmu_active_signup', array( &$this, 'is_signup_allowed' ) ); // send fake/true enable or disabled request
			
			if ( ! defined( 'LBPU_BLOG_LIMIT' ) ) {
				add_action( 'wpmu_options', array( &$this, 'display_options_form' ) ); // show the form to allow how many number of blogs per user
				add_action( 'update_wpmu_options', array( &$this, 'save_allowed_blogs_count' ) ); // action to save number of allowed blogs per user
			}
			
			// since wp 3.0 handles number of allowed blog in idiotic ways, we need to filter on the site option which can be considered as a bad practice but wp 3.0 leaves no other option.
			add_filter( 'site_option_registration', array( &$this, 'is_signup_allowed' ) );

			// Add an easy to find 'Create a Blog' button
			add_action( 'wp_before_admin_bar_render', array( &$this, 'admin_bar_create_a_blog') );
			add_action( 'admin_enqueue_scripts', array( &$this, 'admin_bar_create_a_blog_pointer'), 1000 );
		}
		/**
		 * Factory method
		 * Use it to access the singleton instance and change/modify hooks
		 */
		function get_instance() {
			if( ! isset( self::$instance ) )
				self::$instance = new self();
			return self::$instance;
		}
		
		/**
		 * Check if current user can create new blog, 
		 * It is the core function which restricts a user from creating new blog
		 */
		function is_signup_allowed( $active_signup ) {
			global $current_user;
			
			// if the user is not logged in or the user is network admin, do not apply any restriction settings
			if( ! is_user_logged_in() || is_super_admin () )
				return $active_signup;
			
			$current_blog_count = self::get_blogs_count_for_user( $current_user->ID ); // find all blogs for the user of which the user is either editor/admin
			$number_of_blogs_per_user = self::get_allowed_blogs_count(); // find 
			
			// if number of allowed blog is greater than 0 and current user owns less number of blogs */
			if ( ( $number_of_blogs_per_user == 0 ) || ( $current_blog_count < $number_of_blogs_per_user ) ) {
				return $active_signup;
			}
			
			return 'none';
		}
		
		/**
		 * Find the no. of blogs of which user is admin/author/editor( we just check that the user must not be subscriber )
		 * It return the total number of blogs for which the user is admin
		 * @param <int> $user_id: current user id
		 * @return <int> total admin blog count
		 */
		function get_blogs_count_for_user( $user_id ) {
			$blogs = get_blogs_of_user( $user_id ); // get all blogs of user

			/**
			 * Subscribers have user level 0, so that is not entered in the user meta, author:2, editor:7, Admin:10
			 */
			$count = 0;
			foreach( $blogs as $blog ) {
				if ( self::is_user_blog_admin( $user_id, $blog->userblog_id ) ) {
					$count++;
				}
			}
			return $count;
		}
		
		function get_allowed_blogs_count() {
			if ( defined( 'LBPU_BLOG_LIMIT' ) )
				return LBPU_BLOG_LIMIT;
			
			$num_allowed_blogs = get_site_option( 'tiw_allowed_blogs_per_user', 0 ); // find how many blogs are allowed
			return $num_allowed_blogs; // return the number of allowed blogs
		}
		
		function save_allowed_blogs_count() {
			$allowed_number_of_blogs = intval( $_POST['num_allowed_blogs'] ); // how many blogs the user has set
			
			// save to the database
			update_site_option( 'tiw_allowed_blogs_per_user', $allowed_number_of_blogs ); // now update
		}
		
		function is_user_blog_admin( $user_id, $blog_id ) {
			global $wpdb;
			
			$query = $wpdb->prepare( "SELECT user_id, meta_value FROM $wpdb->usermeta WHERE meta_key = %s",
				$wpdb->base_prefix . $blog_id . '_capabilities'
			);
			
			$role = $wpdb->get_results( $query, ARRAY_A );
			
			// clean the role
			$all_users = array_map( array( 'BPDevLimitBlogsPerUser', 'serialize_roles' ), $role ); // we are unserializing the role to make that as an array;


			foreach( $all_users as $key => $user_info ) {
				if( isset($user_info['meta_value']['administrator']) && $user_info['meta_value']['administrator'] == 1 && $user_info['user_id'] == $user_id ) {
					// if the role is admin
					return true;
				}
			}
			return false;
		}
		
		function serialize_roles( $roles ) {
			$roles['meta_value'] = maybe_unserialize( $roles['meta_value'] );
			return $roles;
		}
		
		/**
		 * Admin option form to show on Network Admin > Dashboard > Network Settings page
		 */
		function display_options_form() {
			?>
			<h3><?php _e( 'Limit Blog Registrations Per User' ) ?></h3>
			<table class="form-table">
				<tbody>
					<tr valign='top'> 
						<th scope='row'><?php _e( 'Number of blogs allowed per User', 'tiw' ) ?></th> 
						<td>
							<input type='text' name='num_allowed_blogs' value="<?php echo self::get_allowed_blogs_count() ?>" />
							<p><?php _e( 'If the Value is Zero, It indicates any number of blog is allowed', 'tiw' ) ?></p>
						</td>
					</tr>
				</tbody>
			</table>
			<?php
		}

		/**
		 * Add Create New Blog to admin_menu
		 */
		function admin_bar_create_a_blog() {
			global $current_user, $wp_admin_bar, $current_site;

			// if the user is not logged in or the user is network admin, do not apply any restriction settings
			if( ! is_user_logged_in() || is_super_admin () )
				return;
			
			$current_blog_count = self::get_blogs_count_for_user( $current_user->ID ); // find all blogs for the user of which the user is either editor/admin
			$number_of_blogs_per_user = self::get_allowed_blogs_count(); // find 
			
			if ( $number_of_blogs_per_user == 0 || ( $current_blog_count < $number_of_blogs_per_user ) ) {
				$wp_admin_bar->add_node( array(
					'id'     => 'tiw-create-a-blog',
					'title'  => __('Create a Blog', 'tiw'),
					'href'   => site_url( '/wp-signup.php' ),
					'parent' => 'top-secondary'
				) );
			}
		}

		function admin_bar_create_a_blog_pointer() {
			if ( get_bloginfo('version') < '3.3' )
				return;

		 	wp_enqueue_style( 'wp-pointer' );
		    wp_enqueue_script( 'tiw-pointer', plugins_url( 'js/limit-blogs-per-user-pointer.js', __FILE__ ), array( 'wp-pointer' ) );
		 
		    $pointers = array(
				'pointers' => array(
					'tiw-create-a-blog' => array(
						'target' => '#wp-admin-bar-tiw-create-a-blog',
					'options' => array(
						'content' => sprintf( '<h3> %s </h3> <p> %s </p>',
							__( 'Create a Blog' ,'tiw'),
							__( 'You have not yet created a blog, click here to create one.','tiw')
						),
						'position' => array( 'edge' => 'top', 'align' => 'center' )
					)
				)
			) );



			wp_localize_script( 'tiw-pointer', 'tiw_pointer', self::pointers_remove_dismissed( $pointers ) );
		}

		/**
		 * Utils
		 */
		function pointers_remove_dismissed( $pointers ) {
			$valid_pointers = array('pointers' => array( ) );
			$dismissed_pointers_string = (string) get_user_meta( get_current_user_id(), 'dismissed_wp_pointers', true );
			$dismissed_pointers = explode( ',', $dismissed_pointers_string );

			if ( $dismissed_pointers_string == "" ) return $pointers;

			foreach ( $pointers as $pointer_id => $pointer ) {
				if ( in_array( $pointer_id, $dismissed_pointers ) || empty( $pointer )  || empty( $pointer_id ) || empty( $pointer['target'] ) || empty( $pointer['options'] ) )
					continue;

				$valid_pointers['pointers'][] = $pointer;
			}

			return $valid_pointers;
		}
	}
	
	BPDevLimitBlogsPerUser::get_instance();