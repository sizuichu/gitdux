<?php
/**
 * WP First Letter Avatar
 * Author URI: http://dev49.net
 */

class ZM_First_Letter_Avatar {
	const IMAGES_PATH = 'avatar'; // 图片目录
	const GRAVATAR_URL = 'https://cn.gravatar.com/avatar/'; // 从cn获取url
	// const GRAVATAR_URL = 'https://secure.gravatar.com/avatar/'; // 从ssl获取url

	// 默认配置:
	const USE_GRAVATAR = true;      // true：如果用户设置了头像，则显示Gravatar头像；false：所有用户使用字母头像
	const LETTER_INDEX = 0;         // 0:首字母;1:第二字母;-1:最后一个字母等。
	const IMAGES_FORMAT = 'png';    // 文件格式
	const ROUND_AVATARS = false;    // TRUE: 头像添加类round-avatars; FALSE: 不添加
	const IMAGE_UNKNOWN = 'mystery';// 未知头像名称，
	const FILTER_PRIORITY = 10;     // 过滤优先级别

	// 从数据库读取配置:
	private $use_gravatar = self::USE_GRAVATAR;
	private $letter_index = self::LETTER_INDEX;
	private $images_format = self::IMAGES_FORMAT;
	private $round_avatars = self::ROUND_AVATARS;
	private $image_unknown = self::IMAGE_UNKNOWN;
	private $filter_priority = self::FILTER_PRIORITY;

    // 创建函数
	public function __construct() {
		$initial_settings = array(
			'wpfla_use_gravatar' => self::USE_GRAVATAR,
			'wpfla_letter_index' => self::LETTER_INDEX,
			'wpfla_file_format' => self::IMAGES_FORMAT,
			'wpfla_round_avatars' => self::ROUND_AVATARS,
			'wpfla_unknown_image' => self::IMAGE_UNKNOWN,
			'wpfla_filter_priority' => self::FILTER_PRIORITY
		);

		/* --------------- WP 钩子 --------------- */
		// add filter to get_avatar:
		add_filter('get_avatar', array($this, 'set_comment_avatar'), $this->filter_priority, 6);

        // add filter for wpDiscuz:
		add_filter('wpdiscuz_author_avatar_field', array($this, 'set_wpdiscuz_avatar'), $this->filter_priority, 4);

		// add additional filter for userbar avatar, but only when not in admin:
		if (!is_admin()){
			add_action('admin_bar_menu', array($this, 'admin_bar_menu_action'), 0);
		} else { // when in admin, make sure first letter avatars are not displayed on discussion settings page
			global $pagenow;
			if ($pagenow == 'options-discussion.php'){
				remove_filter('get_avatar', array($this, 'set_comment_avatar'), $this->filter_priority);
			}
		}
	}

	/**
     * This is method is used to filter wpDiscuz parameter - it feeds $comment object to get_avatar() function
     */
	public function set_wpdiscuz_avatar($author_avatar_field, $comment, $user, $profile_url){

        // that's all we need - instead of user ID or guest email supplied in
        // $author_avatar_field, we just need to return the $comment object
		return $comment;
	}

	/**
	 * This is the main method used for generating avatars. It returns full HTML <img /> tag.
	 */
	private function set_avatar($name, $email, $size, $alt = '', $args = array()){

		if (empty($name)){ // if, for some reason, there is no name, use email instead
			$name = $email;
		} else if (empty($email)){ // and if no email, use user/guest name
			$email = $name;
		}

		// first check whether Gravatar should be used at all:
		if ($this->use_gravatar == true){
			$gravatar_uri = $this->generate_gravatar_uri($email, $size);
			$first_letter_uri = $this->generate_first_letter_uri($name, $size);
			$avatar_uri = $gravatar_uri . '&default=' . urlencode($first_letter_uri);
		} else {
			// gravatar is not used:
			$first_letter_uri = $this->generate_first_letter_uri($name, $size);
			$avatar_uri = $first_letter_uri;
		}
		$avatar_img_output = $this->generate_avatar_img_tag($avatar_uri, $size, $alt, $args); // get final <img /> tag for the avatar/gravatar
		return $avatar_img_output;
	}

	/**
	 * This filters every WordPress avatar call and return full HTML <img /> tag
	 */
	public function set_comment_avatar($avatar, $id_or_email, $size = '96', $default = '', $alt = '', $args = array()){

		// create two main variables:
		$name = '';
		$email = '';
		$user = null; // we will try to assign User object to this

		if (is_object($id_or_email)){ // id_or_email can actually be also a comment object, so let's check it first
			if (!empty($id_or_email->comment_ID)){
				$comment_id = $id_or_email->comment_ID; // it is a comment object and we can take the ID
			} else {
				$comment_id = null;
			}
		} else {
			$comment_id = null;
		}

		if ($comment_id === null){ // if it's not a regular comment, use $id_or_email to get more data

			if (is_numeric($id_or_email)){ // if id_or_email represents user id, get user by id
				$id = (int) $id_or_email;
				$user = get_user_by('id', $id);
			} else if (is_object($id_or_email)){ // if id_or_email represents an object
				if (!empty($id_or_email->user_id)){  // if we can get user_id from the object, get user by id
					$id = (int) $id_or_email->user_id;
					$user = get_user_by('id', $id);
				}
			}

			if (!empty($user) && is_object($user)){ // if commenter is a registered user...
				$name = $user->data->display_name;
				$email = $user->data->user_email;
			} else if (is_string($id_or_email)){ // if string was supplied
				if (!filter_var($id_or_email, FILTER_VALIDATE_EMAIL)){ // if it is NOT email, it must be a username
					$name = $id_or_email;
				} else { // it must be email
					$email = $id_or_email;
					$user = get_user_by('email', $email);
				}
			} else { // if commenter is not a registered user, we have to try various fallbacks
				$post_id = get_the_ID();
				if ($post_id !== null){ // if this actually is a post...
					$post_data = array('name' => '', 'email' => '');
					// first we try for bbPress:
					$post_data['name'] = get_post_meta($post_id, '_bbp_anonymous_name', true);
					$post_data['email'] = get_post_meta($post_id, '_bbp_anonymous_email', true);
					if (!empty($post_data)){ // we have some post data...
						$name = $post_data['name'];
						$email = $post_data['email'];
					}
				} else { // nothing else to do, assign email from id_or_email to email and later use it as name
					if (!empty($id_or_email)){
						$email = $id_or_email;
					}
				}
			}

		} else { // if it's a standard comment, use basic comment properties and/or functions to retrieve info

			$comment = $id_or_email;

			if (!empty($comment->comment_author)){
				$name = $comment->comment_author;
			} else {
				$name = get_comment_author();
			}

			if (!empty($comment->comment_author_email)){
				$email = $comment->comment_author_email;
			} else {
				$email = get_comment_author_email();
			}

		}

		if (empty($name) && !empty($user) && is_object($user)){ // if we do not have the name, but we have user object
			$name = $user->display_name;
		}

		if (empty($email) && !empty($user) && is_object($user)){ // if we do not have the email, but we have user object
			$email = $user->user_email;
		}
		$avatar_output = $this->set_avatar($name, $email, $size, $alt, $args);
		return $avatar_output;
	}

	/**
	 * This method is used to filter the avatar displayed in upper bar (displayed only for logged in users)
	 */
	public function set_userbar_avatar($avatar, $id_or_email, $size = '96', $default = '', $alt = '', $args = array()){

		// get user information:
		$current_user = wp_get_current_user();
		$name = $current_user->display_name;
		$email = $current_user->user_email;

		// use obtained data to return full HTML <img> tag
		$avatar_output = $this->set_avatar($name, $email, $size, $alt, $args);
		return $avatar_output;
	}

	/**
	 * Generate full HTML <img /> tag with avatar URL, size, CSS classes etc.
	 */
	private function generate_avatar_img_tag($avatar_uri, $size, $alt = '', $args = array()){

		// Default classes
		$css_classes = 'avatar avatar-' . $size . ' photo';
		
		// Append plugin class
		$css_classes .= ' wpfla';
		
		// prepare extra classes for <img> tag depending on plugin settings:
		if ($this->round_avatars == true){
			$css_classes .= ' round-avatars';
		}
		
		// Append extra classes
		if (array_key_exists('class', $args)) {
			if (is_array($args['class'])) {
				$css_classes .= ' ' . implode(' ', $args['class']);
			} else {
				$css_classes .= ' ' . $args['class'];
			}
		}
		$output_data = "<img alt='{$alt}' src='{$avatar_uri}' class='{$css_classes}' width='{$size}' height='{$size}' />";

		// return the complete <img> tag:
		return $output_data;
	}

	/**
	 * This method generates full URL for letter avatar (for example http://yourblog.com/wp-content/plugins/wp-first-letter-avatar/images/default/96/k.png),
	 */
	private function generate_first_letter_uri($name, $size){

		// get picture filename (and lowercase it) from commenter name:
		if (empty($name)){  // if, for some reason, the name is empty, set file_name to default unknown image

			$file_name = $this->image_unknown;

		} else { // name is not empty, so we can proceed

 			// 如果为空则显示中文首字母
			$name2=chineseFirst($name);
			if(empty($name2)){
				$name=$name;
			} else {
				$name=$name2;
			}

			$file_name = substr($name, $this->letter_index, 1); // get one letter counting from letter_index
			$file_name = strtolower($file_name); // lowercase it...

			if (extension_loaded('mbstring')){ // check if mbstring is loaded to allow multibyte string operations
				$file_name_mb = mb_substr($name, $this->letter_index, 1); // repeat, this time with multibyte functions
				$file_name_mb=chineseFirst($file_name_mb);
				$file_name_mb = mb_strtolower($file_name_mb); // and again...
			} else { // mbstring is not loaded - we're not going to worry about it, just use the original string
				$file_name_mb = $file_name;
			}

			// couple of exceptions:
			if ($file_name_mb == 'ą'){
				$file_name = 'a';
				$file_name_mb = 'a';
			} else if ($file_name_mb == 'ć'){
				$file_name = 'c';
				$file_name_mb = 'c';
			} else if ($file_name_mb == 'ę'){
				$file_name = 'e';
				$file_name_mb = 'e';
			} else if ($file_name_mb == 'ń'){
				$file_name = 'n';
				$file_name_mb = 'n';
			} else if ($file_name_mb == 'ó'){
				$file_name = 'o';
				$file_name_mb = 'o';
			} else if ($file_name_mb == 'ś'){
				$file_name = 's';
				$file_name_mb = 's';
			} else if ($file_name_mb == 'ż' || $file_name_mb == 'ź'){
				$file_name = 'z';
				$file_name_mb = 'z';
			}

			// create arrays with allowed character ranges:
			$allowed_numbers = range(0, 9);
			foreach ($allowed_numbers as $number){ // cast each item to string (strict param of in_array requires same type)
				$allowed_numbers[$number] = (string)$number;
			}
			$allowed_letters_latin = range('a', 'z');
			$allowed_letters_cyrillic = range('а', 'ё');
			$allowed_letters_arabic = range('آ', 'ی');
			// check if the file name meets the requirement; if it doesn't - set it to unknown
			$charset_flag = ''; // this will be used to determine whether we are using latin chars, cyrillic chars, arabic chars or numbers
			// check whther we are using latin/cyrillic/numbers and set the flag, so we can later act appropriately:
			if (in_array($file_name, $allowed_numbers, true)){
				$charset_flag = 'number';
			} else if (in_array($file_name, $allowed_letters_latin, true)){
				$charset_flag = 'latin';
			} else if (in_array($file_name, $allowed_letters_cyrillic, true)){
				$charset_flag = 'cyrillic';
			} else if (in_array($file_name, $allowed_letters_arabic, true)){
				$charset_flag = 'arabic';
			} else { // for some reason none of the charsets is appropriate
				$file_name = $this->image_unknown; // set it to uknknown
			}

			if (!empty($charset_flag)){ // if charset_flag is not empty, i.e. flag has been set to latin, number or cyrillic...
				switch ($charset_flag){ // run through various options to determine the actual filename for the letter avatar
					case 'number':
						$file_name = 'number_' . $file_name;
						break;
					case 'latin':
						$file_name = 'latin_' . $file_name;
						break;
					case 'cyrillic':
						$temp_array = unpack('V', iconv('UTF-8', 'UCS-4LE', $file_name_mb)); // beautiful one-liner by @bobince from SO - http://stackoverflow.com/a/27444149/4848918
						$unicode_code_point = $temp_array[1];
						$file_name = 'cyrillic_' . $unicode_code_point;
						break;
					case 'arabic':
						$temp_array = unpack('V', iconv('UTF-8', 'UCS-4LE', $file_name_mb));
						$unicode_code_point = $temp_array[1];
						$file_name = 'arabic_' . $unicode_code_point;
						break;
					default:
						$file_name = $this->image_unknown; // set it to uknknown
						break;
				}
			}

		}

		// 判断avatar大小选择不同的图片目录:
		if ($size <= 48) $custom_avatar_size = '96';
		else if ($size > 48 && $size <= 96) $custom_avatar_size = '96';
		else if ($size > 96 && $size <= 128) $custom_avatar_size = '128';
		else if ($size > 128 && $size <= 256) $custom_avatar_size = '128';
		else $custom_avatar_size = '128';

		// create file path - $avatar_uri variable will look something like this:
		// http://yourblog.com/wp-content/plugins/wp-first-letter-avatar/images/default/96/k.png):
		$avatar_uri =
			get_stylesheet_directory_uri(). '/'
			// get_template_directory_uri(). '/'
			. self::IMAGES_PATH . '/'
			. $this->avatar_set . '/'
			. $custom_avatar_size . '/'
			. $file_name . '.'
			. $this->images_format;

		// return the final first letter image url:
		return $avatar_uri;
	}

	/**
	 * This method generates full URL for Gravatar, according to the $email and $size provided
	 */
	private function generate_gravatar_uri($email, $size = '96'){

		if (!filter_var($email, FILTER_VALIDATE_EMAIL)){ // if email not correct
			$email = ''; // set it to empty string
		}

		// email to gravatar url:
		$avatar_uri = self::GRAVATAR_URL;
		$avatar_uri .= md5(strtolower(trim($email)));
		$avatar_uri .= "?s={$size}&r=g";

		return $avatar_uri;
	}
}

// 获取中文首字母
function chineseFirst($str) {
	$str= iconv("UTF-8","gb2312", $str);
	if (preg_match("/^[\x7f-\xff]/", $str)) {
		$fchar=ord($str{0});
		if($fchar>=ord("A") and $fchar<=ord("z") )return strtoupper($str{0});
		$a = $str;
		$val=ord($a{0})*256+ord($a{1})-65536;
		if($val>=-20319 and $val<=-20284)return "A";
		if($val>=-20283 and $val<=-19776)return "B";
		if($val>=-19775 and $val<=-19219)return "C";
		if($val>=-19218 and $val<=-18711)return "D";
		if($val>=-18710 and $val<=-18527)return "E";
		if($val>=-18526 and $val<=-18240)return "F";
		if($val>=-18239 and $val<=-17923)return "G";
		if($val>=-17922 and $val<=-17418)return "H";
		if($val>=-17417 and $val<=-16475)return "J";
		if($val>=-16474 and $val<=-16213)return "K";
		if($val>=-16212 and $val<=-15641)return "L";
		if($val>=-15640 and $val<=-15166)return "M";
		if($val>=-15165 and $val<=-14923)return "N";
		if($val>=-14922 and $val<=-14915)return "O";
		if($val>=-14914 and $val<=-14631)return "P";
		if($val>=-14630 and $val<=-14150)return "Q";
		if($val>=-14149 and $val<=-14091)return "R";
		if($val>=-14090 and $val<=-13319)return "S";
		if($val>=-13318 and $val<=-12839)return "T";
		if($val>=-12838 and $val<=-12557)return "W";
		if($val>=-12556 and $val<=-11848)return "X";
		if($val>=-11847 and $val<=-11056)return "Y";
		if($val>=-11055 and $val<=-10247)return "Z";
	} else {
		return false;
	}
}

// 创建First_Letter_Avatar:
$zm_first_letter_avatar = new ZM_First_Letter_Avatar();