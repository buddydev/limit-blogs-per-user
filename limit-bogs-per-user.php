<?php
/*
Plugin name:Limit Blogs per User
Plugin Author:Brajesh Singh
Plugin URI:http://buddydev.com/buddypress/limit-blogs-per-user-plugin-for-wpmu
Author URI:http://buddydev.com/members/sbrajesh
Version:1.4
Last Updated: 28th september, 2011
Compatible with: BuddyPress 1.5+Wordpress 3.2.1+
License: GPL
*/
/**
 * Note: I originally released this plugin on http://ThinkingInWordpress.com, In the new release has been moved to buddydev.com
 * The function name prefix tiw: stands for thinking In Wordpress, my previous blog :), will change them in future release
 */
/*
 * Date: 28th septembem, 2011.
 * I have rewritten this plugin usin singletone pattern to avoid any cluttering of name space
 */
/**
 * @desc Check ,whether blog registration is allowed,and how many blogs per logged in user is allowed
 */
/*in version 1.4,
 *  we have rewritten the code to use class
 * Singleton Class
 */
class BPDevLimitBlogsPerUser{
   private static  $instance; 
   /**
    * Private construcvtor, to avoid multiple instances
    */
   private function __construct(){
        add_filter('wpmu_active_signup',array(&$this,'is_signup_allowed')); //send fake/true enable or disabled request
        add_action('wpmu_options',array(&$this,'display_options_form')); //show the form to allow how many number of blogs per user
        add_action('update_wpmu_options',array(&$this,'save_allowed_blogs_count'));//action to save number of allowed blogs per user
        
        //since wp3.0 handles number of allowed blog in Idiotic ways, we need to filter on the site option which can be considered as a bad practice but wp 3.0 leaves no other option.
        add_filter("site_option_registration",array(&$this,'is_signup_allowed'));

   }
   /**
    * Factory method
    * Use it to access the singleton instance and change/modyfy hooks*/
   
   function get_instance(){
       if(!isset (self::$instance))
               self::$instance=new self();
       
       return self::$instance;
   }
   /**
    * Check if current user can create new blog,
    * It is the core function which restircts a user from creating new blog
    */
    function is_signup_allowed($active_signup){
        global $current_user;
        //if the user is not logged in or the user is network admin, do not apply any restriction settings
	if( !is_user_logged_in()||is_super_admin() )
            return $active_signup;
        
	$current_blog_count=self::get_blogs_count_for_user($current_user->ID);//find all blogs for the user of which the user is either editor/admin
	$number_of_blogs_per_user=self::get_allowed_blogs_count();//find 
	
	//if number of allowed blog is greater than 0 and current user owns less number of blogs */
	if(($number_of_blogs_per_user==0)||($current_blog_count<$number_of_blogs_per_user))
			return $active_signup;
	else
	return "none";
       
}

/**
 * Find the no. of blogs of which user is admin/author/editor(we just check that the user must not be subscriber)
 * It return the total number of blogs for which the user is  admin
 * @param <int> $user_id: current user id
 * @return <int> total admin blog count
 */
function get_blogs_count_for_user($user_id){
  	$blogs=get_blogs_of_user($user_id);//get all blogs of user
        /**
         * Subscribers have user level 0, so that is not entered in the user meta, author:2, editor:7,Admin:10
         */
       
        $count=0;
        foreach($blogs as $blog){
          if(self::is_user_blog_admin($user_id,$blog->userblog_id))
	       $count++;
           }
 
       return $count;
    }
    
function get_allowed_blogs_count(){
        $num_allowed_blogs=get_site_option('tiw_allowed_blogs_per_user',0);//find how many blogs are allowed
        
        return $num_allowed_blogs;//return the number of allowed blogs
}

function save_allowed_blogs_count(){
        $allowed_number_of_blogs=intval($_POST["num_allowed_blogs"]);//how many blogs the user has set
        //save to the database
        update_site_option('tiw_allowed_blogs_per_user',$allowed_number_of_blogs);//now update
}

//check if the user is blog admin
function is_user_blog_admin($user_id,$blog_id){
    global $wpdb;
       
        $meta_key=$wpdb->prefix.$blog_id.'_capabilities';//.."_user_level";
	$role_sql="SELECT user_id,meta_value FROM {$wpdb->usermeta} WHERE meta_key='". $meta_key."'";
	
        $role=$wpdb->get_results($wpdb->prepare($role_sql),ARRAY_A);
	//clean the role
	$all_user=array_map(array('BPDevLimitBlogsPerUser','serialize_roles'),$role);//we are unserializing the role to make that as an array
	
	foreach($all_user as $key=>$user_info)
		if($user_info['meta_value']['administrator']==1&&$user_info['user_id']==$user_id)//if the role is admin
			return true;
	return false;
}

function serialize_roles($roles){
	$roles['meta_value']=maybe_unserialize($roles['meta_value']);
return $roles;
}


/**
 * Admin option form to show on Network Admin->Dashboard->network Settings page
 */


/*****Show the Number of Blogs to restrict per user at the bottom of Site options ****/
function display_options_form(){
?>
	<h3><?php _e('Limit Blog Registrations Per User') ?></h3>
	<table class="form-table">
	<tbody>
            <tr valign='top'> 
		<th scope='row'><?php _e('Number of blogs allowed per User','tiw');?></th> 
                <td>
                    <input type='text' name='num_allowed_blogs' value="<?php echo self::get_allowed_blogs_count()?>" />
					<p><?php _e('If the Value is Zero,It indicates any number of blog is allowed','tiw');?></p>
		</td>
            </tr>
	</tbody>
	</table>
<?php
}

}//end oc class

//initiate in
BPDevLimitBlogsPerUser::get_instance();

?>