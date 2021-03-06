/**
 * 路由
 */

app.config(['$routeProvider','$locationProvider',function ($routeProvider, $locationProvider) {
	$locationProvider.hashPrefix('');
	$routeProvider
	.when('/System/loginList',{
		templateUrl:'pages/System/loginList.html',
     	controller:"System/loginList",
        resolve:{
            deps:["$ocLazyLoad",function($ocLazyLoad){
                return $ocLazyLoad.load("js/System/loginList.js");
            }],
		    moment:["$ocLazyLoad",function($ocLazyLoad){
		        return $ocLazyLoad.load("lib/bower_components/moment/min/moment.min.js").then(function(){
		            return $ocLazyLoad.load('lib/bower_components/bootstrap-daterangepicker/daterangepicker.js');
		        });
		    }],
		    daterangepicker_css:["$ocLazyLoad",function($ocLazyLoad){
		        return $ocLazyLoad.load("lib/bower_components/bootstrap-daterangepicker/daterangepicker.css");
		    }],
        }
     })
	.when('/System/user',{
		templateUrl:'pages//System/user.html',
     	controller:"System/user",
        resolve:{
            deps:["$ocLazyLoad",function($ocLazyLoad){
                return $ocLazyLoad.load("js/System/user.js");
            }]
        }
     })
	.when('/System/role',{
		templateUrl:'pages//System/role.html',
     	controller:"System/role",
        resolve:{
            deps:["$ocLazyLoad",function($ocLazyLoad){
                return $ocLazyLoad.load("js/System/role.js");
            }]
        }
     })
	.when('/MovieManage/user',{
		templateUrl:'pages//MovieManage/user.html',
     	controller:"MovieManage/user",
        resolve:{
            deps:["$ocLazyLoad",function($ocLazyLoad){
                return $ocLazyLoad.load("js/MovieManage/user.js");
            }],
		    moment:["$ocLazyLoad",function($ocLazyLoad){
		        return $ocLazyLoad.load("lib/bower_components/moment/min/moment.min.js").then(function(){
		            return $ocLazyLoad.load('lib/bower_components/bootstrap-daterangepicker/daterangepicker.js');
		        });
		    }],
		    daterangepicker_css:["$ocLazyLoad",function($ocLazyLoad){
		        return $ocLazyLoad.load("lib/bower_components/bootstrap-daterangepicker/daterangepicker.css");
		    }],
        }
     })
	.when('/MovieManage/post',{
		templateUrl:'pages//MovieManage/post.html',
     	controller:"MovieManage/post",
        resolve:{
            deps:["$ocLazyLoad",function($ocLazyLoad){
                return $ocLazyLoad.load("js/MovieManage/post.js");
            }],
		    moment:["$ocLazyLoad",function($ocLazyLoad){
		        return $ocLazyLoad.load("lib/bower_components/moment/min/moment.min.js").then(function(){
		            return $ocLazyLoad.load('lib/bower_components/bootstrap-daterangepicker/daterangepicker.js');
		        });
		    }],
		    daterangepicker_css:["$ocLazyLoad",function($ocLazyLoad){
		        return $ocLazyLoad.load("lib/bower_components/bootstrap-daterangepicker/daterangepicker.css");
		    }],
        }
     })
	.when('/MovieManage/repost',{
		templateUrl:'pages//MovieManage/repost.html',
     	controller:"MovieManage/repost",
        resolve:{
            deps:["$ocLazyLoad",function($ocLazyLoad){
                return $ocLazyLoad.load("js/MovieManage/repost.js");
            }],
		    moment:["$ocLazyLoad",function($ocLazyLoad){
		        return $ocLazyLoad.load("lib/bower_components/moment/min/moment.min.js").then(function(){
		            return $ocLazyLoad.load('lib/bower_components/bootstrap-daterangepicker/daterangepicker.js');
		        });
		    }],
		    daterangepicker_css:["$ocLazyLoad",function($ocLazyLoad){
		        return $ocLazyLoad.load("lib/bower_components/bootstrap-daterangepicker/daterangepicker.css");
		    }],
        }
     })
	.when('/Video/index',{
		templateUrl:'pages/Video/index.html',
		controller:"Video/index",
		resolve:{
			deps:["$ocLazyLoad",function($ocLazyLoad){
				return $ocLazyLoad.load("js/Video/index.js");
			}],
			moment:["$ocLazyLoad",function($ocLazyLoad){
				return $ocLazyLoad.load("lib/bower_components/moment/min/moment.min.js").then(function(){
					return $ocLazyLoad.load('lib/bower_components/bootstrap-daterangepicker/daterangepicker.js');
				});
			}],
			daterangepicker_css:["$ocLazyLoad",function($ocLazyLoad){
				return $ocLazyLoad.load("lib/bower_components/bootstrap-daterangepicker/daterangepicker.css");
			}],
            select2:["$ocLazyLoad",function($ocLazyLoad){
                return $ocLazyLoad.load("lib/bower_components/select2/dist/js/select2.min.js").then(function(){
                    $ocLazyLoad.load('lib/bower_components/select2/dist/js/select2_default.js')
                    return $ocLazyLoad.load('lib/bower_components/select2/dist/css/select2.min.css');
                });
            }],
		}
	})
	.when('/Video/tag',{
		templateUrl:'pages/Video/tag.html',
		controller:"Video/tag",
		resolve:{
			deps:["$ocLazyLoad",function($ocLazyLoad){
				return $ocLazyLoad.load("js/Video/tag.js");
			}],
            select2:["$ocLazyLoad",function($ocLazyLoad){
                return $ocLazyLoad.load("lib/bower_components/select2/dist/js/select2.min.js").then(function(){
                    $ocLazyLoad.load('lib/bower_components/select2/dist/js/select2_default.js')
                    return $ocLazyLoad.load('lib/bower_components/select2/dist/css/select2.min.css');
                });
            }],
		}
	})
	.when('/Image/index',{
		templateUrl:'pages/Image/index.html',
		controller:"Image/index",
		resolve:{
			deps:["$ocLazyLoad",function($ocLazyLoad){
				return $ocLazyLoad.load("js/Image/index.js");
			}],
			moment:["$ocLazyLoad",function($ocLazyLoad){
				return $ocLazyLoad.load("lib/bower_components/moment/min/moment.min.js").then(function(){
					return $ocLazyLoad.load('lib/bower_components/bootstrap-daterangepicker/daterangepicker.js');
				});
			}],
			daterangepicker_css:["$ocLazyLoad",function($ocLazyLoad){
				return $ocLazyLoad.load("lib/bower_components/bootstrap-daterangepicker/daterangepicker.css");
			}],
            select2:["$ocLazyLoad",function($ocLazyLoad){
                return $ocLazyLoad.load("lib/bower_components/select2/dist/js/select2.min.js").then(function(){
                    $ocLazyLoad.load('lib/bower_components/select2/dist/js/select2_default.js')
                    return $ocLazyLoad.load('lib/bower_components/select2/dist/css/select2.min.css');
                });
            }],
		}
	})
	.when('/Image/tag',{
		templateUrl:'pages/Image/tag.html',
		controller:"Image/tag",
		resolve:{
			deps:["$ocLazyLoad",function($ocLazyLoad){
				return $ocLazyLoad.load("js/Image/tag.js");
			}],
            select2:["$ocLazyLoad",function($ocLazyLoad){
                return $ocLazyLoad.load("lib/bower_components/select2/dist/js/select2.min.js").then(function(){
                    $ocLazyLoad.load('lib/bower_components/select2/dist/js/select2_default.js')
                    return $ocLazyLoad.load('lib/bower_components/select2/dist/css/select2.min.css');
                });
            }],
		}
	})
	.when('/welcome',{
		templateUrl:'welcome.html',
     	controller:"welcome",
        resolve:{
            deps:["$ocLazyLoad",function($ocLazyLoad){
                return $ocLazyLoad.load("js/welcome.js");
            }]
        }
     })
	.when('/t/tt/ttt',{
		templateUrl:'welcome.html',
		controller:"welcome",
		resolve:{
			deps:["$ocLazyLoad",function($ocLazyLoad){
				return $ocLazyLoad.load("js/welcome.js");
			}]
		}
	})
     .otherwise({redirectTo:'/welcome'});
	
 }]);
