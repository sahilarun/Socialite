ALTER TABLE `setting` ADD `post_delete_period` INT NULL DEFAULT NULL AFTER `network_to_use`;
ALTER TABLE `user` ADD `chat_delete_period` INT NULL DEFAULT NULL AFTER `chat_last_time_online`;
ALTER TABLE `user` CHANGE `chat_delete_period` `chat_delete_period` INT(11) NULL DEFAULT '0';
ALTER TABLE `setting` ADD `story_delete_period` INT NOT NULL DEFAULT '0' AFTER `post_delete_period`;


ALTER TABLE `payment` ADD `event_ticket_booking_id` INT NULL DEFAULT NULL AFTER `gift_history_id`;


DROP TABLE IF EXISTS `tv_banner`;
CREATE TABLE IF NOT EXISTS `tv_banner` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(256) NOT NULL,
  `cover_image` varchar(256) NOT NULL,
  `banner_type` varchar(256) NOT NULL,
  `start_time` int(11) NOT NULL,
  `end_time` int(11) NOT NULL,
  `reference_id` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `tv_show`
--

DROP TABLE IF EXISTS `tv_show`;
CREATE TABLE IF NOT EXISTS `tv_show` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(256) NOT NULL,
  `tv_channel_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `language` varchar(100) NOT NULL,
  `age_group` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `image` varchar(256) NOT NULL,
  `status` int(11) NOT NULL,
  `created_at` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `show_time` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `tv_show_episode`
--

DROP TABLE IF EXISTS `tv_show_episode`;
CREATE TABLE IF NOT EXISTS `tv_show_episode` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(256) NOT NULL,
  `tv_show_id` int(11) NOT NULL,
  `image` varchar(256) DEFAULT NULL,
  `video` varchar(256) DEFAULT NULL,
  `episode_period` varchar(50) DEFAULT NULL,
  `status` int(11) NOT NULL,
  `created_at` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


ALTER TABLE user ADD is_login_first_time int(11) NOT NULL;
ALTER TABLE `user` CHANGE `is_login_first_time` `is_login_first_time` INT(11) NOT NULL DEFAULT '0';

ALTER TABLE `setting` ADD `stripe_publishable_key` VARCHAR(256) NULL DEFAULT NULL AFTER `story_delete_period`, ADD `stripe_secret_key` VARCHAR(256) NULL DEFAULT NULL AFTER `stripe_publishable_key`;


DROP TABLE IF EXISTS `language`;
CREATE TABLE IF NOT EXISTS `language` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(256) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `language`
--

INSERT INTO `language` (`id`, `name`) VALUES
(1, 'English'),
(2, 'Punjabi'),
(3, 'Hindi'),
(4, 'Urdo');

ALTER TABLE `chat_message` ADD `is_encrypted` INT NOT NULL DEFAULT '0' AFTER `message`;
ALTER TABLE `post` ADD `audio_id` INT NULL DEFAULT NULL AFTER `post_content_type`, ADD `audio_start_time` INT NULL DEFAULT NULL AFTER `audio_id`, ADD `audio_end_time` INT NULL DEFAULT NULL AFTER `audio_start_time`, ADD `is_add_to_post` INT NOT NULL DEFAULT '0' AFTER `audio_end_time`;
ALTER TABLE `post` CHANGE `type` `type` INT(11) NOT NULL DEFAULT '1' COMMENT '1=normal post, 2=competition, 3=club, 4=reel';

ALTER TABLE `setting`
ADD `razorpay_api_key` text NOT NULL,
ADD `paypal_merchant_id` varchar(256) NOT NULL,
ADD `paypal_public_key` varchar(256) NOT NULL,
ADD `paypal_private_key` varchar(256) NOT NULL,
ADD `is_photo_post` INT(11) NOT NULL,
ADD `is_video_post` INT(11) NOT NULL,
ADD `is_stories` INT(11) NOT NULL,
ADD `is_story_highlights` INT(11) NOT NULL,
ADD `is_chat` INT(11) NOT NULL,
ADD `is_audio_calling` INT(11) NOT NULL,
ADD `is_video_calling` INT(11) NOT NULL,
ADD `is_live` INT(11) NOT NULL,
ADD `is_clubs` INT(11) NOT NULL,
ADD `is_competitions` INT(11) NOT NULL,
ADD `is_events` INT(11) NOT NULL,
ADD `is_staranger_chat` INT(11) NOT NULL,
ADD `is_profile_verification` INT(11) NOT NULL,
ADD `is_light_mode_switching` INT(11) NOT NULL,
ADD `is_watch_tv` INT(11) NOT NULL,
ADD `is_podcasts` INT(11) NOT NULL,
ADD `is_gift_sending` INT(11) NOT NULL,
ADD `is_photo_share` INT(11) NOT NULL,
ADD `is_video_share` INT(11) NOT NULL,
ADD `is_files_share` INT(11) NOT NULL,
ADD `is_gift_share` INT(11) NOT NULL,
ADD `is_audio_share` INT(11) NOT NULL,
ADD `is_drawing_share` INT(11) NOT NULL,
ADD `is_user_profile_share` INT(11) NOT NULL,
ADD `is_club_share` INT(11) NOT NULL,
ADD `is_events_share` INT(11) NOT NULL,
ADD `is_reply` INT(11) NOT NULL,
ADD `is_forward` INT(11) NOT NULL,
ADD `is_star_message` INT(11) NOT NULL;


DROP TABLE IF EXISTS `tv_banner`;
CREATE TABLE IF NOT EXISTS `tv_banner` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(256) NOT NULL,
  `cover_image` varchar(256) NOT NULL,
  `banner_type` varchar(256) NOT NULL,
  `start_time` int(11) NOT NULL,
  `end_time` int(11) NOT NULL,
  `reference_id` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


ALTER TABLE `chat_message` ADD `replied_on_message` TEXT NULL DEFAULT NULL AFTER `message`;
ALTER TABLE `chat_message` ADD `chat_version` VARCHAR(100) NOT NULL DEFAULT '' AFTER `is_user_notify`;
ALTER TABLE `chat_message` CHANGE `chat_version` `chat_version` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;


ALTER TABLE setting ADD is_contact_sharing int(11) NOT NULL , ADD `is_location_sharing` int(11) NOT NULL , ADD `is_polls` int(11) NOT NULL , ADD `is_dating` int(11) NOT NULL , ADD `is_family_link_setup` int(11) NOT NULL , ADD `is_post_promotion` int(11) NOT NULL;


ALTER TABLE `club` ADD `is_request_based` INT NOT NULL DEFAULT '0' AFTER `privacy_type`;


DROP TABLE IF EXISTS `club_invitation_request`;
CREATE TABLE IF NOT EXISTS `club_invitation_request` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `club_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `type` int(11) NOT NULL DEFAULT 1 COMMENT 'invitation=1,request=2',
  `message` text DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 1 COMMENT 'pending =1,cancelled=2 rejected=3, accepted=10',
  `created_at` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

ALTER TABLE setting ADD is_fund_raising int(11) NOT NULL DEFAULT '1' AFTER `is_events_share`;
ALTER TABLE `setting` ADD `agora_app_certificate` VARCHAR(256) NULL DEFAULT NULL AFTER `agora_api_key`;
ALTER TABLE `chat_message` CHANGE `chat_version` `chat_version` INT(11) NULL DEFAULT NULL;


UPDATE `setting` SET `maximum_video_duration_allowed` = '120', `free_live_tv_duration_to_view` = '60', `is_photo_post` = '1', `is_video_post` = '1', `is_stories` = '1', `is_story_highlights` = '1', `is_chat` = '1', `is_audio_calling` = '1', `is_video_calling` = '1', `is_live` = '1', `is_clubs` = '1', `is_competitions` = '1', `is_events` = '1', `is_staranger_chat` = '1', `is_profile_verification` = '1', `is_light_mode_switching` = '1', `is_watch_tv` = '1', `is_podcasts` = '1', `is_gift_sending` = '1', `is_photo_share` = '1', `is_video_share` = '1', `is_files_share` = '1', `is_gift_share` = '1', `is_audio_share` = '1', `is_drawing_share` = '1', `is_user_profile_share` = '1', `is_club_share` = '1', `is_events_share` = '1', `is_fund_raising` = '1', `is_reply` = '1', `is_forward` = '1', `is_star_message` = '1', `is_contact_sharing` = '1', `is_location_sharing` = '1', `is_polls` = '1', `is_dating` = '1', `is_family_link_setup` = '1', `is_post_promotion` = '1' WHERE `setting`.`id` = 1;
