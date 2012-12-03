<?php
/*
Plugin Name: Twitter Home Time line
Plugin URI: http://www.phpquestionsandanswers.com/useful-links/wordpress
Description: Twitter Home Time line, Displays latest tweets just like the home page after you login to twitter using twitteroauth
Author: Bhuwan kumar singh
Version: 1.0
Author URI: http://www.phpquestionsandanswers.com/useful-links/wordpress
*/
function twitter_time($a) {
    //get current timestampt
    $b = strtotime("now"); 
    //get timestamp when tweet created
    $c = strtotime($a);
    //get difference
    $d = $b - $c;
    //calculate different time values
    $minute = 60;
    $hour = $minute * 60;
    $day = $hour * 24;
    $week = $day * 7;
        
    if(is_numeric($d) && $d > 0) {
        //if less then 3 seconds
        if($d < 3) return "right now";
        //if less then minute
        if($d < $minute) return floor($d) . " seconds ago";
        //if less then 2 minutes
        if($d < $minute * 2) return "about 1 minute ago";
        //if less then hour
        if($d < $hour) return floor($d / $minute) . " minutes ago";
        //if less then 2 hours
        if($d < $hour * 2) return "about 1 hour ago";
        //if less then day
        if($d < $day) return floor($d / $hour) . " hours ago";
        //if more then day, but less then 2 days
        if($d > $day && $d < $day * 2) return "yesterday";
        //if less then year
        if($d < $day * 365) return floor($d / $day) . " days ago";
        //else return more than a year
        return "over a year ago";
    }
}



    function getConnectionWithAccessToken($consumer_key, $consumer_secret,$oauth_token, $oauth_token_secret) {

      $connection = new TwitterOAuth($consumer_key, $consumer_secret, $oauth_token, $oauth_token_secret);

      return $connection;

    }
	
	function show_twitter_home_time_line($title){
	
	?>
	
	<script>
function PopupCenter(pageURL, title,w,h) {
  var left = (screen.width/2)-(w/2);
  var top = (screen.height/2)-(h/2);
  var targetWin = window.open (pageURL, title, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width='+w+', height='+h+', top='+top+', left='+left);
} 

</script>
<?php
define( 'MYPLUGINNAME_PATH', plugin_dir_path(__FILE__) );

require_once(MYPLUGINNAME_PATH.'/twitteroauth/twitteroauth.php');


    $connection = getConnectionWithAccessToken(get_option('php_consumer_key'),get_option('php_consumer_secret'),get_option('php_oauth_token'), get_option('php_oauth_token_secret'));

    $content = $connection->get("statuses/home_timeline", array(

  'count' => 100));
?>



<?php
if($title){

echo "<h3 class='tweet_h3'>".$title."</h3>";
}


$i	=	0;
foreach($content as $out){
$i++;



 			$unix_time = strtotime($out->created_at);
//            $pretty_time = relativeTime($unix_time);
			
			$pretty_time	=	twitter_time($out->created_at);

?>
		<div class="b_tweets">
			

			
			<div class="t_w">
				<div class="avatar">
				<a href="https://twitter.com/<?php echo $out->user->screen_name; ?>"><img src="<?php  echo $out->user->profile_image_url; ?>"></a>
				</div>
				
				<div class="content">
				<a href="https://twitter.com/<?php echo $out->user->screen_name; ?>"><?php echo $out->user->name; ?></a><br>
				<?php echo $out->text; ?>
				<br>
				<a href="#" onclick="PopupCenter('https://twitter.com/intent/tweet?in_reply_to=<?php echo  $out->id_str; ?>','Reply',800,600);return false;">Reply</a> | <a href="#" onclick="PopupCenter('https://twitter.com/intent/retweet?tweet_id=<?php echo  $out->id_str; ?>','Reply',800,600);return false;">Re-tweet</a> 
				
				
				<div style="clear:both;"></div>
				<div class="date"><?php echo $pretty_time; ?></date>
				<div style="clear:both;"></div>
				
				</div>
				
								
				
			</div>
			
			
		</div>	
		</div>
<?php

if($i==get_option('php_no_of_tweets'))
break;
}


	
	
	
	}
class WP_Widget_TwitterHomeTimeline extends WP_Widget {

	// The widget construct. Mumbo-jumbo that loads our code.
	function WP_Widget_TwitterHomeTimeline() {
		$widget_ops = array( 'classname' => 'widget_BareBones', 'description' => __( "Twitter Home Timeline Widget" ) );
		$this->WP_Widget('twitter-home-timeline', __('Twitter Home Timeline'), $widget_ops);
	} // End function WP_Widget_BareBones

	// This code displays the widget on the screen.
public function widget($args, $instance) {
	
		extract($args);
		echo $before_widget;
		
		
		
		show_twitter_home_time_line($instance['title']);
		
		?>
		<?php
		
		
		
		
		echo $after_widget;
	} // End function widget.
	
	// Updates the settings.
	
	public function update( $new_instance, $old_instance ) {

		$instance = array();

		$instance['title'] = strip_tags( $new_instance['title'] );



		return $new_instance;

	}
		
	
	// The admin form.
public	function form($instance) {	
	
	$title = isset( $instance['title'] ) ? $instance['title'] : 'Latest Tweets';
	
	
	?>
	<p>

			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>

			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />

		</p>
	<?php
	
	echo "Go to <a href='options-general.php?page=TwitterHomeTimeline' target='_new'>options</a> for settings.";	
	} // end function form

} // end class WP_Widget_BareBones

// Register the widget.
//add_action('widgets_init', create_function('', 'return register_widget("WP_Widget_TwitterHomeTimeline");'));

// Register the widget.

add_action( 'widgets_init', 'TwitterHomeTimeline_init' );

function TwitterHomeTimeline_init() {

	register_widget( 'WP_Widget_TwitterHomeTimeline' );

}


?>
<?php
function TwitterHomeTimeline_enqueue_stylesheet() {

$plugins_url = get_bloginfo('url') . '/wp-content/plugins/Twitter-Home-Timeline/';

    wp_enqueue_style( 'TwitterHomeTimeline-stylesheet', $plugins_url."stylesheet.css");
}
add_action( 'wp_enqueue_scripts', 'TwitterHomeTimeline_enqueue_stylesheet' );
?>
<?php
if (!function_exists('TwitterHomeTimeline_menus')) :
function TwitterHomeTimeline_menus() {
	
	if (function_exists('add_submenu_page')) {
		add_options_page('Twitter Home Timeline Options','Twitter Home Timeline Options', 8, 'TwitterHomeTimeline',  'wp_twitter_home_time_line_options_page', 'wp_twitter_home_time_line_options_pages');
		
	}
	
} // End of wp_mail_smtp_menus() function definition
endif;

function wp_twitter_home_time_line_options_page(){
?>
<h3>Twitter Home Time Line Options</h3>
Go to <a href="https://dev.twitter.com/apps" target="_new">https://dev.twitter.com/apps</a> to get  your api credentials

<br /><br />

<?php
if($_POST[submit]){

update_option('php_consumer_key',$_POST[php_consumer_key]);

update_option('php_consumer_secret',$_POST[php_consumer_secret]);

update_option('php_oauth_token',$_POST[php_oauth_token]);

update_option('php_oauth_token_secret',$_POST[php_oauth_token_secret]);

update_option('php_no_of_tweets',$_POST[php_no_of_tweets]);

}

?>


<form action="options-general.php?page=TwitterHomeTimeline" method="post">





Consumer Key<br>
<input type="text" name="php_consumer_key" value="<?php echo get_option('php_consumer_key'); ?>"><br><br>

Consumer Secret<br>
<input type="text" name="php_consumer_secret" value="<?php echo get_option('php_consumer_secret'); ?>"><br><br>

Oauth Token<br>
<input type="text" name="php_oauth_token" value="<?php echo get_option('php_oauth_token'); ?>"><br><br>

Oauth Token Secret<br>
<input type="text" name="php_oauth_token_secret" value="<?php echo get_option('php_oauth_token_secret'); ?>"><br><br>

Number of tweets to display<br>
<input type="text" name="php_no_of_tweets" value="<?php echo get_option('php_no_of_tweets'); ?>"><br><br>


<input type="submit" value="Save" name="submit">
</form>
<?php
}

add_action('admin_menu','TwitterHomeTimeline_menus');

function TwitterHomeTimeline_shortcode($atts) {
	extract(shortcode_atts(array(
		'title' => '',
	), $atts));

	show_twitter_home_time_line($title);
	
}
add_shortcode('TwitterHomeTimeline', 'TwitterHomeTimeline_shortcode');
?>