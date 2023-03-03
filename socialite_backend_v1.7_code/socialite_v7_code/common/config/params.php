<?php
return [

    'bsVersion' => '3.1',
    'bsDependencyEnabled' => false,
    'adminEmail' => 'admin@SocialiFied.co',
    'supportEmail' => 'support@SocialiFied.com',
    'senderEmail' => 'admin@SocialiFied.co',
    'senderName' => 'SocialiFied',
    
    'siteMode' => 1, // 1 for live, 2 for testing , 3 demo
    'siteUrl' => 'http://example.com',// domain here
    'enventoPurchaseCode' => '##########', // envato purchase code
    'storageSystem'=> 1,//  storage system ( local storage =1, AWS S3=2, AZURE=3)
    's3' => [
        'key' => '##############',
        'secret' => '##############',
        'region' => '##############',
        'defaultBucket' => '#########', //name
        'storageUrl'=>'https://[bucket_name].s3.amazonaws.com'  //aws s3 storage url
        
    ],
    'azureFs' => [
        'accountName' => '###########',
        'accountKey' => '##########',
        'container' => '#######'
        
    ],
    
    'testOtp' => '1111',
    'apiKey.firebaseCloudMessaging'=> '##############', 
    'user.passwordResetTokenExpire' => 3600,
    'twilioSid' => '##########', /// 
    'twilioToken' => '#########',
    'smsFromTwilio' =>'#############',/// twilo number

    'pathUploadImageFolder' => 'image',
    'pathUploadCategoryFolder' => 'category',
    'pathUploadUserFolder' => 'user',
    'pathUploadCompetitionFolder' => 'competition',
    'pathUploadCollectionFolder' => 'collection',
    'pathUploadStoryFolder' => 'story',
    'pathUploadChatFolder' => 'chat',
    'pathUploadLiveTvFolder' => 'live-tv',
    'pathUploadGiftFolder' => 'gift',
    'pathUploadVerificationFolder' => 'verification',
    'pathUploadEventFolder' => 'event',
    'pathUploadCouponFolder' => 'coupon',
    'pathUploadEventOrganisorFolder' => 'event/organisor',
    'pathUploadTvShowFolder' => 'tv-show',
    'pathUploadTvShowEpisodeFolder' => 'tv-show-episode',
    'pathUploadReelAudioFolder' => 'reel-audio',
    'pathUploadTvBannerFolder' => 'tv-banner',
    'pathUploadPodcastFolder' => 'podcast',
    'pathUploadPodcastShowFolder' => 'podcast-show',
    'pathUploadPodcastBannerFolder' => 'podcast-banner',
    'pathUploadOrginationFolder' => 'orgination',
    'pathUploadCampaignFolder'=>'campagin',


    
    
    "postPopularityPoint"=> [
        'postView' => 1,
        'postLike' => 2,
        'postShare' => 3,
        'popuplarPointCondition' =>1

    ],
    
    "apiMessage"=> [
        
        "common"=>[
            "recordFound" =>"Record has been found.",
            "noRecord" =>"No record Found.",
            "actionFailed" =>"Action failed! Please try again.",
            "actionSuccess" =>"Action Successfully performed.",
            "notAllowed" =>"You are not authorize to do this action.",
            "listFound" =>"Record list found successfully.",
            "actionAlready" =>"Action already performed.",
        ],
        
        "user"=>[
            "locationUpdate" =>"User location has been updated successfully.",
            "alreadyReported"=>"You have already reported this user, Your request is under review",
            "reportedSuccess"=>"User reported successfully",
            "registerSuccess" =>"Thank you for Register with us, We sent confirmation email to you for verification",
            
            "enableDeliveryRequest" =>"Delivery request trun on successfully.",
            "disableDeliveryRequest" =>"Delivery request trun off successfully.",
            "passwordChanged" =>"Password updated successfully.",
            "sentEmailForgotPassword" =>"Please check your email for further instruction to reset your password.",
            "notRegisterWithUs"=>"User not registered with us.",
            "tokenExpired"=>"Token expired.",
            "emailNotVerified"=>"Your email is not verified yet, We sent confirmation email to you for verification.",
            "loginFailed"=>"Email/password incorrect.",
            
            "optVerifyToChangePassword"=>"OTP verified successfully. Please set your new password",
            "optVerifyFailed"=>"OTP verification failed, Please correct your OTP",
            "optSent"=>"OTP has been send on your connected eamil/phone",
            "otpSentEamilSuccess"=>"OTP has been send on your connected eamil",
            "otpSentMobileSuccess"=>"OTP has been send on your connected mobile number",
            "otpMobileVerified"=>"Your mobile has been verified successfully.",
            "oldPasswordIncorrect"=>"Old password incorrect",
            "enableRoamingSeller" =>"Roaming location enabled successfully.",
            "disableRoamingSeller" =>"Roaming location disabled successfully.",
            "onlineEnable" =>"Status online enabled successfully.",
            "onlineDisable" =>"Status offline enabled successfully.",
            "currenlyOfflineYou" =>"You are offline currenly, Please enable your status as enable to see near by deliveries.",
            "profileUpdated" =>"Profile Updated successfully.",
            "socialAccountConnected" =>"Social account connected successfully",
            "socialAccountRemoved" =>"Social account removed successfully",
            "registerWithSameAccount"=>"You cant remove because you have register with this account on system",
            "usernameAvailable"=>"Username available",
            "walletSubscribed"=>"Wallet subscribed successfully",
            "walletSubscribedAlready"=>"You have already subscribed wallet",
            "termConditionAccepted"=>"Term and condtion accepted successfully",
            "logout"=>"Logout successfully",
            
        ],
        "competition"=>[
            "noRecord" =>"No record Found or competition has no longer available.",
            "notAvailable" =>"Competition has no longer available for post image.",
            "joinCompetition" =>"Please join competition before post an image in competition.",
            "alreadyPosted" =>"You have already posted image in competition.",
            "alreadyJoinedCompetition" =>"You have already joined this competition.",
            "joiningFeeNotAvailable" =>"You have not sufficient coin to join this competition.",
            "joinSuccess" =>"You have joined this competition."
            
            
            
            
            
        ],
        
        "post"=>[
            "listFound"=>"Post list found successfully",
            "postCreateSuccess"=>"Post has been created successfully",
            "postCreateFailed"=>"Post has not created successfully",

            "postLikeSuccess"=>"like successfully",
            "postLikeFailed"=>"Remove like successfully",
            "postLikeAlready"=>"You have already liked this post",

            "postUnlikeSuccess"=>"like removed successfully",
            "postUnlikeFailed"=>"Unlike process failed",
            "commentSuccess"=>"Comment added successfully",
            "postShareSuccess"=>"Post shared successfully",
            "alreadyReported"=>"You have already reported this post, Your request is under review",
            "reportedSuccess"=>"Post reported successfully",
            "deleted"=>"Post deleted successfully"
            
            
            
        ],
        "collection"=>[
            "listFound"=>"Collection list found successfully",
            "created"=>"Collection created successfully",
            "updated"=>"Collection updated successfully",
            "deleted"=>"Collection deleted successfully",
            "addedCollection"=>"Collection added successfully",
            "removedCollection"=>"Collection removed from list successfully",
            "alreadyAddedInList"=>"This post already added in your collection"
        ],
        "highlight"=>[
            "listFound"=>"Highlight list found successfully",
            "created"=>"Highlight created successfully",
            "updated"=>"Highlight updated successfully",
            "deleted"=>"Highlight deleted successfully",
            "addedStory"=>"Story added successfully",
            "removedStory"=>"Highlight removed from list successfully",
            "alreadyAddedInList"=>"This story already added in your Highlight"
        ],
        "payment"=>[
            "amountNotAvailable"=>"Amount is not sufficiant in your wallet to withdrawal",
            "withdrawRequestSuccess"=>"Amount withdrawal request has been genereated successfully",
            "withdrawFailed"=>"Amount withdrawal request failed.",
            "coinNotAvailable"=>"No sufficiant coins in your wallet to redeem.",
            "coinMinRequired"=>"Minimun {{COIN}} coins required to redeem.",
            "coinRedeemSuccess"=>"Coin redeemed successfully.",
            "coinRedeemFailed"=>"Coin redeption failed.",
            
            
        ],
        "supportRequest"=>[
            "created"=>"Support request created successfully"
        ],
        "story"=>[
            "listFound"=>"Story list found successfully",
            "created"=>"Story created successfully",
            "updated"=>"Story updated successfully",
            "deleted"=>"Story deleted successfully"
            
            
        ],
        "blockedUser"=>[
            "listFound"=>"Blocked users list found successfully",
            "blocked"=>"User blocked successfully",
            "unBlocked"=>"User removed from blocked list successfully",
            "alreadyBlocked"=>"User is already in your blocked list"
            
        ],
        "chat"=>[
            "roomCreated"=>"Room created successfully.",
            "roomUpdated"=>"Room updated successfully.",
            "fileUploaded"=>"File uploaded successfully",
            "roomDeleted"=>"Room deleted successfully.",
            "roomChatDeleted"=>"Room chat deleted successfully.",
        ],
        "club"=>[
            "clubCreated"=>"Club created successfully.",
            "clubUpdated"=>"Club updated successfully.",
            "clubDeleted"=>"Club deleted successfully.",
            //"fileUploaded"=>"File uploaded successfully",
            "clubDeleted"=>"Club deleted successfully.",
            "alreadyJoinedClub"=>"You have already joined club",
            "joinSuccess"=>"You have joined club successfully",
            "leftSuccess"=>"You have left the club successfully",
            "removedSuccess"=>"User removed from club successfully",
            "notRemoveClubOwner"=>"Club owner cant left from club",
            "inviteSuccess"=>"User invited successfully",
            "alreadyInvited"=>"User already invited",
            "invitationAccepted"=>"Club invitation accepted successfully.",
            "alreadyJoinRequest" =>"You have already request for join this club",
            "joinRequestSend"=>"Club join request has been submitted successfully",
            "requestAccepted"=>"Club join request accepted successfully",
            "requestBasedClub"=>"You can join club after sending request to join this club",
            
            

            
            
        ],
        "liveTv"=>[
           
            "subscribed" =>"You have successfully subscribed the TV.",
            "alreadySubscribed" =>"You have already subscribed this Live TV.",
            "subscribeFeeNotAvailable" =>"You have not sufficient coin to subscribe this live tv.",
            "noNeedSubscribe"=>"No need to subscribe as this tv is free",
            "alreadyFavorite"=>"You have already added your favorite list",
            "AddFavorite"=>"Added in favorite list successfully",
            "removedFavorite"=>"Removed from favorite list successfully"
        ],
        "podcast"=>[
           
            "subscribed" =>"You have successfully subscribed the Podcast.",
            "alreadySubscribed" =>"You have already subscribed this Podcast.",
            "subscribeFeeNotAvailable" =>"You have not sufficient coin to subscribe this Podcast.",
            "noNeedSubscribe"=>"No need to subscribe as this tv is free",
            "alreadyFavorite"=>"You have already added your favorite list",
            "AddFavorite"=>"Added in favorite list successfully",
            "removedFavorite"=>"Removed from favorite list successfully"
        ],
        "gift"=>[
            "notEnoughBalance" =>"You have not sufficient coin in your account to send this gift.",
            "sent"=>"Gift sent successfully"
        ],
        "userVerification"=>[
            "created" =>"Verification request has been created successfully.",
            "alreadyPendingVerification" =>"Your verification request already pending from admin.",
            "cancelled" =>"Your verification request has been cancelled successfully.",
            
            
        ],
        "event"=>[
            "eventClosed" =>"Event booking has been closed.",
            "buyTicketSuccessfully" =>"Thanks you for booking your ticket.",
            "seatNotAvailable" =>"Seat not available.",
            "cancelled" =>"Your booking has been cancelled successfully.",
            "amountNotAvailable"=>"Amount is not sufficiant in your wallet",
            "alreadyCancelled"=>"This ticket has been already cancelled.",
            "canNotCancelled"=>"This ticket can not be cancelled.",
            "cancelTicketSuccessfully" => "Ticket booking has been cancelled successfully.",
            "canNotGift"=>"This ticket can not be gifted.",
            "ticketGifted"=>"Ticket gifted succcessfully",
            "alreadyGifted"=>"Ticket already has been gifted"
            
            
            
        ],
        "pollQuestion"=>[
            "listFound"=>"Question Answer list found successfully",
            "created"=>"Question created successfully",
            "updated"=>"Question updated successfully",
            "deleted"=>"Question deleted successfully",
            "addedAnswer"=>"Question Answer added successfully",
            "removedQuestion"=>"Question removed from list successfully",
            "alreadyAddedInList"=>"You are already attend."
        ],

    ],
    "pushNotificationMessage"=> [

        
        "newFollower"=>[
            "title" =>"New follower",
            "body" =>"{{USER}} has started following you.",
            "type" =>"1"
        ],
        "newComment"=>[
            "title" =>"{{USER}} commented on your post.",
            "body" =>"comments",
            "type" =>"2"
        ],

        
        "likePost"=>[
            "title" =>"Your post liked",
            "body" =>"{{USER}}  liked your post.",
            "type" =>"3"
        ],
        "newCompetition"=>[
            "title" =>"New Competition",
            "body" =>"{{TITLE}} new competition added.",
            "type" =>"4"
        ],
        "wonCompetition"=>[
            "title" =>"You Won Competition",
            "body" =>"Congratulations, You have won the Competition {{TITLE}}.",
            "type" =>"5"
        ],
        
        "supportRequestReply"=>[
            "title" =>"Support Request replied",
            "body" =>"Your support request replied given by admin.",
            "type" =>"6"
        ],

      
        "mentionUserPost"=>[
            "title" =>"Mention in post",
            "body" =>"You have mentioned in post {{TITLE}}.",
            "type" =>"7"
        ],

        "giftRecieved"=>[
            "title" =>"Gift Recieved",
            "body" =>"You have recieved gift on your {{ON_TYPE}}.",
            "type" =>"8"
        ],
        "verificationApproved"=>[
            "title" =>"Verification approved",
            "body" =>"Congratulations, Your verrification request has been approved.",
            "type" =>"9"
        ],
        "verificationRejected"=>[
            "title" =>"Verification rejected",
            "body" =>"Your verification requested reject.",
            "type" =>"10"
        ],
        "clubInvitation"=>[
            "title" =>"Club Invitation",
            "body" =>"You have got new club invitation.",
            "type" =>"11"
        ],
        "clubJoinRequest"=>[
            "title" =>"Club Join Request",
            "body" =>"You have got new club join request.",
            "type" =>"12"
        ],

        "adApprove"=>[
            "title" =>"Ad Approved",
            "body" =>"Your ad has been approved."
        ],
        "adRejected"=>[
            "title" =>"Ad Rejected",
            "body" =>"Your ad has been rejected."
        ],
        "newOrder"=>[
            "title" =>"New Order",
            "body" =>"New order has been recieved.",
            "type" =>"newOrder"
        ],
        
    ]

    
    


];
