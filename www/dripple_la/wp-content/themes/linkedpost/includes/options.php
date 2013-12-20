<?php
/* DEFAULT SITE / USER OPTIONS */

define("_CHANNELS","");

//$linkedin_message = array(
//    "subject"   => "Open to giving me your opinion?",
//    "body"      => "Hi [ContactFname],\n\rAre you open to give me your opinion?\n\rI have just posted an Analysis on [DripTitle] in my [DripTopic] blog.\n\rI noticed you're a [ContactIndustry] specialist. Could you give me your opinion?\n\r[DripURL]\n\rThanks,\n\r[MemberFullName]"
//);
$linkedin_message = array(
    "subject"   => "Hi! I'd like to get your opinion!",
    "body"      => "Hi [ContactFname],\n\rHow have you been?\n\rI know you are a [ContactIndustry] specialist.\n\rI wanted to post an Analysis on [DripTitle] in my [DripTopic] blog.\n\rWould you be open to giving me your opinion on it?\n\rI would appreciate it very much!\n\r[MemberFullName]"
);
define("LINKEDIN_MESSAGE",json_encode($linkedin_message));

$API_CONFIG = array(
	'appKey'       => '7526oyd3xlrpgh',
	'appSecret'    => 'eLcWtUtToo82vKvx',
	'callbackUrl'  => NULL 
);

define("LINKEDIN_API_CONFIG",json_encode($API_CONFIG));

$member_tags = array(
	"MemberFullName",
	"MemberFname",
	"MemberLname"	
);
define("MEMBER_TAGS",json_encode($member_tags));

$drip_tags = array(
	"DripURL",
	"DripTitle",
	"DripTopic",	
	"DripChannel"
);
define("DRIP_TAGS",json_encode($drip_tags));

$contact_tags = array(
	"ContactFullName",
	"ContactFname",
	"ContactLname",
	"ContactID",	
	"ContactIndustry"
);
define("CONTACT_TAGS",json_encode($contact_tags));

$search_sources = array(
	"news" 			=> 1,
	"blogs" 		=> 0,
	"twitter" 		=> 0,
	"dripple" 		=> 0
);
define("TOPIC_SEARCH_SOURCES",json_encode($search_sources));

global $current_site;

$witter_api_config = array(
	"CONSUMER_KEY"      => "BkbE7SEAlyDwfsin2yjRrQ",
	"CONSUMER_SECRET"   => "CnhXYi77vBM0eulRRMQh9P20uj09331rWoweHVvcI",
	"OAUTH_CALLBACK"    => "http://".$current_site->domain."/twitter/CB/"
);
define("TWITTER_API_CONFIG",json_encode($witter_api_config));

$facebook_api_config = array(
	"appId"		=> "173362096203111",
	"secret"	=> "4cf2d847cb9aaad71da61e1aa7ca97c5"
);

define("FACEBOOK_API_CONFIG",json_encode($facebook_api_config));

$meter = Array(
	"drip_day"	=> array(
				"monday" 	=> 1,
				"tuesday" 	=> 1,
				"wednesday" => 1,
				"thursday" 	=> 1,
				"friday" 	=> 1,
				"saturday" 	=> 1,
				"sunday" 	=> 1
			),
	"drip_time"	=> array(
				"09:00 am"	=> "09:00 am"
			)
);
define("DEFAULT_METER",json_encode($meter));
?>