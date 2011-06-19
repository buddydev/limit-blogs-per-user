<?php
/*
Plugin name:Limit Blogs per User
Plugin Author:Brajesh Singh
Plugin URI:http://buddydev.com/buddypress/limit-blogs-per-user-plugin-for-wpmu
Author URI:http://buddydev.com/members/sbrajesh
Version:1.3.1
Last Updated: 2nd August 2010
License: GPL
*/
/**
 * Note: I originally released this plugin on http://ThinkingInWordpress.com, In the new release has been moved to buddydev.com
 * The function name prefix tiw: stands for thinking In Wordpress, my previous blog :), will change them in future release
 */
add_filter("wpmu_active_signup","tiw_check_current_users_blog"); //send fake/true enable or disabled request
add_action("wpmu_options","tiw_display_options_form"); //show the form to allow how many number of blogs per user
add_action("update_wpmu_options","tiw_save_num_allowed_blogs");//action to save number of allowed blogs per user

//since wp3.0 handles number of allowed blog in Idiotic ways, we need to filter on the site option which can be considered as a bad practice but wp 3.0 leaves no other option.
add_filter("site_option_registration","tiw_check_current_users_blog");
/**
 * @desc Check ,whether blog registration is allowed,and how many blogs per logged in user is allowed
 */
function tiw_check_current_users_blog($active_signup){
    global $current_user;
    
	if( !is_user_logged_in()||is_site_admin() )
            return $active_signup;//if the user is not logged in, or is site admin, do not change the site policies

	$current_blog_count=tiw_find_non_subscriber_blogs($current_user->ID);//find all blogs for the user of which the user is either editor/admin
	$number_of_blogs_per_user=tiw_num_allowed_blogs();//find 
	
	//if number of allowed blog is greater than 0 and current user owns less number of blogs */
	if(($number_of_blogs_per_user==0)||($current_blog_count<$number_of_blogs_per_user))
			return $active_signup;
	else
	return "none";
       
}
/**
 * @desc Find the blogs of which user is admin
 *  It return the total number of blogs for which the user is  admin
 * @param <array> $blogs
 * @param <int> $user_id
 * @return <int> total admin blog count
 */
function tiw_find_non_subscriber_blogs($user_id){
  	$blogs=get_blogs_of_user($user_id);//get all blogs of user
        /**
         * Subscribers have user level 0, so that is not entered in the user meta, author:2, editor:7,Admin:10
         */
       
        $count=0;
        foreach($blogs as $blog){
				if(bpdev_is_user_blog_admin($user_id,$blog->userblog_id))
			        $count++;
        
            }
 
       return $count;
    }

/****How many blogs are allowed per user *************/

function tiw_num_allowed_blogs()
{
    $num_allowed_blog=get_site_option("tiw_allowed_blogs_per_user");//find how many blogs are allowed
        if(!isset($num_allowed_blog))
            $num_allowed_blog=0;

return $num_allowed_blog;//return the number of allowed blogs
}

/*****Show the Number of Blogs to restrict per user at the bottom of Site options ****/
function tiw_display_options_form()
{
?>
	<h3><?php _e('Limit Blog Registrations Per User') ?></h3>
	<table>
	<tbody>
		<tr valign="top"> 
				<th scope="row"><?php _e("Number of blogs allowed per User","tiw");?></th> 
				<td>
					<input type="text" name="num_allowed_blogs" value="<?php echo tiw_num_allowed_blogs()?>" />
					<p><?php _e("If the Value is Zero,It indicates any number of blog is allowed","tiw");?></p>
				</td>
		</tr>
	</tbody>
	</table>
<?php
}

/**************Save the Number of blogs per user when the form is updated **************/
function tiw_save_num_allowed_blogs(){
$allowed_number_of_blogs=intval($_POST["num_allowed_blogs"]);//how many blogs the user has set
//save to the database
update_site_option("tiw_allowed_blogs_per_user",$allowed_number_of_blogs);//now update

}
//check if the user is blog admin
function bpdev_is_user_blog_admin($user_id,$blog_id){
global $wpdb;
    $meta_key="wp_".$blog_id."_capabilities";//.."_user_level";
	$role_sql="select user_id,meta_value from {$wpdb->usermeta} where meta_key='". $meta_key."'";
	$role=$wpdb->get_results($wpdb->prepare($role_sql),ARRAY_A);
	//clean the role
	$all_user=array_map("tiw_serialize_roles",$role);//we are unserializing the role to make that as an array
	
	foreach($all_user as $key=>$user_info)
		if($user_info['meta_value']['administrator']==1&&$user_info['user_id']==$user_id)//if the role is admin
			return true;
	return false;
}

function tiw_serialize_roles($roles){
	$roles['meta_value']=maybe_unserialize($roles['meta_value']);
return $roles;
}
?>