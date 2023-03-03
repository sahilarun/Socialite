<aside class="main-sidebar">

    <section class="sidebar">

      
        <?= dmstr\widgets\Menu::widget(
            [
                'options' => ['class' => 'sidebar-menu tree',  'data-widget'=> 'tree'],
                'items' => [
                    
                    ['label' => 'Dashboard', 'icon' => 'ion fa-tachometer',  'aria-hidden'=>"true", 'url' => Yii::$app->homeUrl],
                    ['label' => 'Administrators', 'icon' => 'user',  'aria-hidden'=>"true", 'url' => ['/administrator']],
                    

                    [
                        'label' => 'Users',
                        'icon' => 'users',
                        'url' => '#',
                        'items' => [
                            ['label' => 'Users', 'icon' => 'users',  'aria-hidden'=>"true", 'url' => ['/user']],
                            ['label' => 'Reported User', 'icon' => 'ion fa-bell',  'aria-hidden'=>"true", 'url' => ['/user/reported-user']],
                            ['label' => 'User Verification', 'icon' => 'users',  'aria-hidden'=>"true", 'url' => ['/user-verification']],
                           
                            
                        ],
                    ],
                    [
                        'label' => 'Post',
                        'icon' => 'fas fa-bullhorn',
                        'url' => '#',
                        'items' => [
                            ['label' => 'Post', 'icon' => 'fas fa-bullhorn',  'aria-hidden'=>"true", 'url' => ['/post']],
                            ['label' => 'Reported Post', 'icon' => 'ion fa-bell',  'aria-hidden'=>"true", 'url' => ['/post/reported-post']],
                           
                            
                        ],
                    ],
                    
                    [
                        'label' => 'Competition',
                        'icon' => 'fas fa-bullhorn',
                        'url' => '#',
                        'items' => [
                            ['label' => 'Create Competition', 'icon' => 'fas fa-bullhorn',  'aria-hidden'=>"true", 'url' => ['/competition/create']],
                            ['label' => 'Competition', 'icon' => 'fas fa-bullhorn',  'aria-hidden'=>"true", 'url' => ['/competition']],
                            
                            
                        ],
                    ],
                    [
                        'label' => 'Club',
                        'icon' => 'fas fa-bullhorn',
                        'url' => '#',
                        'items' => [
                            ['label' => 'Club', 'icon' => 'fas fa-bullhorn',  'aria-hidden'=>"true", 'url' => ['/club']],
                            ['label' => 'Club Categories', 'icon' => 'list-alt',  'aria-hidden'=>"true", 'url' => ['/club-category']],
                            
                        ],
                    ],
                   

                    /*['label' => 'Support Request', 'icon' => 'fas fa-ticket',  'aria-hidden'=>"true", 'url' => ['/support-request']],*/
                    [
                        'label' => 'Payment',
                        'icon' => 'fas fa-money',
                        'url' => '#',
                        'items' => [
                            ['label' => 'Payment Received', 'icon' => 'fas fa-money',  'aria-hidden'=>"true", 'url' => ['/payment']], 
                            ['label' => 'Payment Request', 'icon' => 'fas fa-money',  'aria-hidden'=>"true", 'url' => ['/withdrawal-payment']],
                            ['label' => 'Payout', 'icon' => 'fas fa-money',  'aria-hidden'=>"true", 'url' => ['/withdrawal-payment','type'=>'completed']],        
                        ],
                    ],
                    ['label' => 'Packages', 'icon' => 'fas fa-money',  'aria-hidden'=>"true", 'url' => ['/package']],

                    
                    

                    [
                        'label' => 'Tv Channel',
                        'icon' => 'fas fa-bullhorn',
                        'url' => '#',
                        'items' => [
                            ['label' => 'Tv Channel', 'icon' => 'fas fa-bullhorn',  'aria-hidden'=>"true", 'url' => ['/live-tv']],
                            ['label' => 'Tv Channel Categories', 'icon' => 'list-alt',  'aria-hidden'=>"true", 'url' => ['/live-tv-category']],
                            [
                                'label' => 'Tv Show',
                                'icon' => 'fas fa-bullhorn',
                                'url' => '#',
                                'items' => [
                                    ['label' => 'Tv Show', 'icon' => 'list-alt',  'aria-hidden'=>"true", 'url' => ['/tv-show']],
                                    ['label' => 'Tv Show Categories', 'icon' => 'list-alt',  'aria-hidden'=>"true", 'url' => ['/category','type'=>3]],
                                    
                                ],
                               
                            ],
                            ['label' => 'Tv Banner', 'icon' => 'fas fa-bullhorn',  'aria-hidden'=>"true", 'url' => ['/tv-banner']],
                        ],
                    ],

                  
                    [
                        'label' => 'Gift',
                        'icon' => 'fas fa-bullhorn',
                        'url' => '#',
                        'items' => [
                            ['label' => 'Gift', 'icon' => 'fas fa-bullhorn',  'aria-hidden'=>"true", 'url' => ['/gift']],
                            ['label' => 'Gitf Categories', 'icon' => 'list-alt',  'aria-hidden'=>"true", 'url' => ['/gift-category']],
                            
                        ],
                    ],


                    [
                        'label' => 'FAQs',
                        'icon' => 'fas fa-question-circle',
                        'url' => '#',
                        'items' => [
                            ['label' => 'FAQ', 'icon' => 'fas fa-question-circle',  'aria-hidden'=>"true", 'url' => ['/faq']],
                            // ['label' => 'Gitf Categories', 'icon' => 'list-alt',  'aria-hidden'=>"true", 'url' => ['/gift-category']],
                            
                        ],
                    ],
                   // ['label' => 'Organization', 'icon' => 'users',  'aria-hidden'=>"true", 'url' => ['/orginazition']],
                  
                    
                  
                    
                    [
                        'label' => 'Setting',
                        'icon' => 'ion fa-wrench',
                        'url' => '#',
                        'items' => [
                            ['label' => 'Contact Information', 'icon' => 'ion fa-wrench',  'aria-hidden'=>"true", 'url' => ['/setting']],
                            ['label' => 'General Setting', 'icon' => 'ion fa-wrench',  'aria-hidden'=>"true", 'url' => ['/setting/general-information']],
                            ['label' => 'Payment Setting', 'icon' => 'ion fa-wrench',  'aria-hidden'=>"true", 'url' => ['/setting/payment']],
                            ['label' => 'Social Links', 'icon' => 'ion fa-wrench',  'aria-hidden'=>"true", 'url' => ['/setting/social-links']],
                            ['label' => 'App Settings', 'icon' => 'ion fa-wrench',  'aria-hidden'=>"true", 'url' => ['/setting/app-setting']],
                            ['label' => 'Feature Availability', 'icon' => 'ion fa-wrench',  'aria-hidden'=>"true", 'url' => ['/setting/feature']],
                          
                          
                            
                        ],
                    ],


                   /*   ['label' => 'Audio', 'icon' => 'fas fa-bullhorn',  'aria-hidden'=>"true", 'url' => ['/audio']],
                   ['label' => 'Sub Categories', 'icon' => 'list-alt',  'aria-hidden'=>"true", 'url' => ['/categorysub']],
                    
                    [
                        'label' => 'Membership',
                        'icon' => 'shopping-bag',
                        'url' => '#',
                        'items' => [
                            ['label' => 'Packages', 'icon' => 'fas fa-money', 'url' => ['/package'],],
                            ['label' => 'Promotional Banners', 'icon' => 'fas fa-money', 'url' => ['/promotional-banner'],],
                            
                        ],
                    ],
                    [
                        'label' => 'Ads',
                        'icon' => 'fas fa-bullhorn',
                        'url' => '#',
                        'items' => [
                            
                            ['label' => 'Active Ads', 'icon' => 'fas fa-bullhorn', 'url' => ['/ad','type'=>'active'],],
                            ['label' => 'Pending Ads', 'icon' => 'fas fa-bullhorn', 'url' => ['/ad','type'=>'pending'],],
                            ['label' => 'All Ads', 'icon' => 'fas fa-bullhorn', 'url' => ['/ad','type'=>'all'],],
                            ['label' => 'Expired Ads', 'icon' => 'fas fa-bullhorn', 'url' => ['/ad','type'=>'expire'],],
                            
                            
                        ],
                    ],
                    ['label' => 'Reported Ads', 'icon' => 'ion fa-bell',  'aria-hidden'=>"true", 'url' => ['/ad/reported-ads']],
                    ['label' => 'Banner', 'icon' => 'picture-o',  'aria-hidden'=>"true", 'url' => ['/banner']],
                    ['label' => 'Message', 'icon' => 'fas fa-commenting-o',  'aria-hidden'=>"true", 'url' => ['/message']],
                    ['label' => 'Payment', 'icon' => 'fas fa-money',  'aria-hidden'=>"true", 'url' => ['/payment']],
                    
                    
                    ['label' => 'Setting', 'icon' => 'ion fa-wrench',  'aria-hidden'=>"true", 'url' => ['/setting']],
                    */
                    /*
                    ['label' => 'Gii', 'icon' => 'file-code-o', 'url' => ['/gii']],
                    ['label' => 'Debug', 'icon' => 'dashboard', 'url' => ['/debug']],
                    ['label' => 'Login', 'url' => ['site/login'], 'visible' => Yii::$app->user->isGuest],
                    [
                        'label' => 'Some tools',
                        'icon' => 'share',
                        'url' => '#',
                        'items' => [
                            ['label' => 'Gii', 'icon' => 'file-code-o', 'url' => ['/gii'],],
                            ['label' => 'Debug', 'icon' => 'dashboard', 'url' => ['/debug'],],
                            [
                                'label' => 'Level One',
                                'icon' => 'circle-o',
                                'url' => '#',
                                'items' => [
                                    ['label' => 'Level Two', 'icon' => 'circle-o', 'url' => '#',],
                                    [
                                        'label' => 'Level Two',
                                        'icon' => 'circle-o',
                                        'url' => '#',
                                        'items' => [
                                            ['label' => 'Level Three', 'icon' => 'circle-o', 'url' => '#',],
                                            ['label' => 'Level Three', 'icon' => 'circle-o', 'url' => '#',],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],*/
                ],
            ]
        ) ?>

    </section>

</aside>
