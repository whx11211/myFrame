angular.module('myApp').controller('System/userInfo', function($scope, $rootScope, $http, $timeout) {
	$scope.api_name = 'System/userInfo';

	$scope.user = {};
	$http.post(api($scope.api_name)).then(function (respone) {
		if (respone.data.r) {
            $rootScope.user = respone.data.data;
			console_log($rootScope.user, '用户信息');
		}
		else if (respone.data.error == 1001) {
			$rootScope.goto_login();
		}
	});
	
	//退出登录
	$scope.login_out = function () {
		$http.post(api('System/loginOut')).then(function (respone) {
			if (respone.data.r && respone.data.data) {
				$rootScope.goto_login();
			}
		});
	}
	

	
	//返回登录页面
	$rootScope.goto_login = function () {
		window.location.href = './login.html';
	}
	
	//加载主语言包
	$rootScope.langs = {};
	$http.post(lang('main')).then(
    	function (respone) {
    		$rootScope.langs = respone.data;
    	}
    );
	
	//面包屑 导航栏设置
	$rootScope.navtag = ['welcome'];
	$rootScope.set_breadcrumb = function (api_name) {
		$rootScope.navtag = $rootScope.breadcrumb(api_name);
	}
	$rootScope.breadcrumb = function (api_name, menu) {
		var res = [];
		if (!api_name) {
			api_name = $rootScope.api_name;
		}
		if (api_name == 'welcome') {
			return ['welcome'];
		}
		if (!menu) {
			menu = $rootScope.menu;
		}
		for (var i in menu) {
			if (menu[i].func == api_name) {
				res.push(menu[i].func);
				break;
			}
			else if (typeof(menu[i].sub) != 'undefined') {
				var res2 = $rootScope.breadcrumb(api_name, menu[i].sub);
				if (res2.length > 0) {
					res.push(menu[i].func);
					res = res.concat(res2);
					break;
				}
			}
		}
		return res;
	}
	

    //保留菜单折叠状态（未完成）
	$rootScope.menu_init_mark = false;
	$rootScope.menu_init = function() {
		if (!$rootScope.menu_init_mark) {
		    var url = window.location.href;
		    var menu_href = url.slice(url.indexOf('#'));
		    console.log($(".sidebar-menu li a"));
		    $(".sidebar-menu li a").each(function () { 
		      if ($(this).attr('href') == menu_href && $(this).attr('href') != '#') {
		        $(this).parent().parent().show();
		        $(this).parent().parent().parent().addClass('menu-open');
		      }
		    });
		}
	    $rootScope.menu_init_mark = true;
	}

	
	//页面加载完成后的通用处理
    $scope.$watch('$viewContentLoaded', function() {
        $('body').tooltip({
            selector: "[data-toggle='tooltip']",
            container: 'body'
        });
        
    });

	
	//错误提示模态框
	$rootScope.show_error = function (data, callback) {
		var tip = '';
		if (typeof(data.error) != 'undefined') {
			if (data.error == '1001') {
    			tip = $scope.langs['error_'+data.error];
    			callback = $rootScope.goto_login;
    		}
			else if (typeof($scope.langs['error_'+data.error]) != 'undefined') {
				tip = $scope.langs['error_'+data.error];
				if (typeof(data.msg) != 'undefined') {
					tip += ':' + (typeof($scope.langs[data.msg]) == 'undefined' ? data.msg : $scope.langs[data.msg]);
				}
			}
			else {
				tip = data.error;
			}
		}
		else {
			tip = data;
		}
		$rootScope.modal_error_info = tip;
		//当模态框完全对用户隐藏时触发。
		$('#modal_error').off('hidden.bs.modal').on('hidden.bs.modal', callback);
		$('#modal_error').modal();
	}
	//成功提示模态框
	$rootScope.show_success = function (a, callback) {
		$rootScope.modal_success_info = (typeof($scope.langs[a]) == 'undefined' ? a : $scope.langs[a]) + $scope.langs.success;
		//当模态框完全对用户隐藏时触发。
		$('#modal_success').off('hidden.bs.modal').on('hidden.bs.modal', callback);
		$('#modal_success').modal();
	}
    $('#modal_success').on('shown.bs.modal', function(){
        $timeout(function(){
            $('#modal_success').modal('hide');
        },150);
    });
	
//	//调用 show 方法后触发
//	$('#modal_pay_res').on('show.bs.modal', function () {
//
//	});
//	//当模态框对用户可见时触发
//	$('#modal_pay_res').on('shown.bs.modal', function () {
//
//	});
//	//当调用 hide 实例方法时触发
//	$('#modal_pay_res').on('hide.bs.modal', function () {
//
//	});
//	//当模态框完全对用户隐藏时触发。
//	$('#modal_pay_res').on('hidden.bs.modal', function () {
//		if ($rootScope.modal_error_close_callback) {
//			$rootScope.modal_error_close_callback();
//		}
//	});
	
	//用户信息
	$rootScope.get_admin_users = function (focus) {
		if (typeof($rootScope.admin_users) == 'undefined' || focus) {
	    	$http.post(api('Common/getUsersInfo')).then(function (respone) {
	    		if (respone.data.r) {
	    			$rootScope.admin_users = respone.data.data;
	    			console_log($rootScope.admin_users, '所有用户信息');
	    		}
	    		else {
	    			$rootScope.show_error(respone.data);
	    		}
	    	});
		}
	}
	
	// 全局：分页分类
    $rootScope.page_number = ["10", "25", "50", "100"];
    $rootScope.get_default_page_number = function () {
        var cookie_page_number = $.cookie('data_page_number');
        return parseInt(cookie_page_number) > 0 ? cookie_page_number : $rootScope.page_number[0];
    }
    $rootScope.set_default_page_number = function (page_num) {
        return $.cookie('data_page_number', page_num);
    }
    
    // 全局：angular-ui-grid
    $rootScope.ui_grid = {
        // ====全局：获取当前语言配置
        get_i18n: function() {
        	return langBase;
        },
        // ====全局：获取参数gridOptions初始化配置
        init: function() {
            return {
                // 水平scrollBar
                //enableHorizontalScrollbar: 0,
                // 垂直scrollBar
                enableVerticalScrollbar: 0,
                
                // 不显示表格前面的勾选框，配合HTML属性data-ui-grid-selection
                enableRowHeaderSelection: false,
                
                // 外部排序
                //useExternalSorting: true,
                //onRegisterApi: function( gridApi ) {
                //    $scope.gridApi = gridApi;
                //    $scope.gridApi.core.on.sortChanged( $scope, $scope.sortChanged );
                //    $scope.sortChanged($scope.gridApi.grid, [ $scope.gridOptions.columnDefs[1] ] );
                //},
                
                // 显示grid菜单
                enableGridMenu: true,
                
                //导出配置
                exporterMenuCsv : false,  
                exporterOlderExcelCompatibility: true,
                exporterMenuPdf : false,
            };
        },
        // ====通用获取
        get: function(data_key, visible, minWidth) {
        	return { field: data_key, displayName: $scope.langs[data_key], visible: visible, minWidth:minWidth };
        },
        // ====通用获取（无排序）
        get_nosort: function(data_key, visible, minWidth) {
        	return { field: data_key, displayName: $scope.langs[data_key], visible: visible, enableSorting: false, minWidth:minWidth };
        },
        // ====全局：获取columnDefs[序号]栏位配置
        get_seq: function() {
            return {
                field: 'seq', displayName: $scope.langs['seq'],
                minWidth: 54, pinnable: true, enableSorting: false, enableFiltering: false,
                cellTemplate: '<div class="ui-grid-cell-contents">{{grid.renderContainers.body.visibleRowCache.indexOf(row)+1+(grid.appScope.data.page_current-1)* grid.appScope.data.items_per_page}}</div>'
            };
        },
        // ====全局：获取exportColumnDefs[序号]导出配置
        get_export_seq: function( grid, row, col, input, $filter ) {
            return grid.renderContainers.body.visibleRowCache.indexOf(row)+1+(grid.appScope.data.page_current-1)* grid.appScope.data.items_per_page;
        },
        // ====全局：获取columnDefs的[id流水]栏位配置
        get_id: function(data_key, lang_key, visible) {
            return { field: data_key, displayName: $scope.langs[lang_key], minWidth: 90, visible: visible };
        },
        // ====全局：获取columnDefs的[时间戳]栏位转换配置
        get_ts: function(data_key, visible) {
            return {
                field: data_key, displayName: $scope.langs[data_key], minWidth: 135,
                cellTemplate: '<div class="ui-grid-cell-contents">{{ (row.entity.'+data_key+' | date2:"yyyy-MM-dd HH:mm:ss") }}</div>',
                visible: visible
            };
        },
        // ====全局：获取exportColumnDefs的[时间戳]导出配置
        get_export_ts: function( grid, row, col, input, $filter ) {
            return $filter('date')(input*1000, 'yyyy-MM-dd HH:mm:ss');
        },
        // ====全局：获取columnDefs的[时间戳]栏位转换日期天配置
        get_ts_date: function(data_key, lang_key, visible) {
            return {
                field: data_key, displayName: $scope.langs[lang_key], minWidth: 100,
                cellTemplate: '<div class="ui-grid-cell-contents">{{  row.entity.'+data_key+' | date2:"yyyy-MM-dd" }}</div>',
                visible: visible
            };
        },
        // ====全局：获取columnDefs的[IP]栏位配置
        get_ip: function(data_key, lang_key, visible) {
            return {
                field: data_key, displayName: $scope.langs[lang_key], minWidth: 95, visible: visible
            };
        },
        // ====全局：获取columnDefs的[ADMIN_USERID]栏位配置
        get_user: function(data_key, visible, toolip) {
            $rootScope.get_admin_users();
            return {
                field: data_key,
                displayName: $scope.langs[data_key],
                minWidth: 90,
                cellTemplate: '<div class="ui-grid-cell-contents">{{row.entity.'+data_key+'}} : {{grid.appScope.admin_users[row.entity.'+data_key+']["userName"] }}</div>',
                visible: visible
            };
        },
        // ====全局：获取exportClumnDefs的[ADMIN_USERID]导出配置
        get_export_user: function( grid, row, col, input, $filter ) {
            return input + ':' + grid.appScope.admin_users[input]["userName"];
        },
        // ====全局：获取columnDefs的[PLATFORM]栏位配置
        get_platform: function(data_key, lang_key, visible) {
            $rootScope.getPlatforms();
            return {
                field: data_key, displayName: $scope.langs[lang_key], minWidth: 100,
                cellTemplate: '<div class="ui-grid-cell-contents">{{row.entity.'+data_key+'}} : {{grid.appScope.platforms[row.entity.'+data_key+']["name"] || grid.appScope.langs["plat_0"]}}</div>',
                visible: visible
            };
        },
     // ====全局：获取columnDefs的[PLATFORM]导出配置
        get_export_platform: function( grid, row, col, input, $filter ) {
            return input + ':' + (input != '0' ? grid.appScope.platforms[input]["name"] : grid.appScope.langs["plat_0"]);
        },
    }
    //console_log('$rootScope.ui_grid', $rootScope.ui_grid);
    // 全局：./angular-ui-grid
    
    
    // 全局：正则表达
    $rootScope.regexp = {
        ip: /^[0-9\.]{1,15}$/,
        ip_regexp: /^[0-9\.\*]{1,15}$/,
        daterange: /^([0-9]{3}[1-9]|[0-9]{2}[1-9][0-9]{1}|[0-9]{1}[1-9][0-9]{2}|[1-9][0-9]{3})-(((0[13578]|1[02])-(0[1-9]|[12][0-9]|3[01]))|((0[469]|11)-(0[1-9]|[12][0-9]|30))|(02-(0[1-9]|[1][0-9]|2[0-9])))\s-\s([0-9]{3}[1-9]|[0-9]{2}[1-9][0-9]{1}|[0-9]{1}[1-9][0-9]{2}|[1-9][0-9]{3})-(((0[13578]|1[02])-(0[1-9]|[12][0-9]|3[01]))|((0[469]|11)-(0[1-9]|[12][0-9]|30))|(02-(0[1-9]|[1][0-9]|2[0-9])))$/,
        password: /(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9])([\w\+=~!@#$%^&*]{5,})/,
        safekey: /^[a-zA-Z0-9]*$/,
        platid: /^[1-9]\d*$/,
        
        test: ""
    };
    
    // 全局：dateRangePicker插件相关
    // ====全局：date_range_picker_ranges初始化方法
    $rootScope.getDateRangePickerRanges = function() {
        if (typeof($rootScope.date_range_picker_ranges) == 'undefined') {
            $rootScope.date_range_picker_ranges = {};
            $rootScope.date_range_picker_ranges[$rootScope.langs.date_range_picker_Today] = [moment(), moment()];
            $rootScope.date_range_picker_ranges[$rootScope.langs.date_range_picker_Yesterday] = [moment().subtract(1, 'days'), moment().subtract(1, 'days')];
            $rootScope.date_range_picker_ranges[$rootScope.langs.date_range_picker_Last7Days] = [moment().subtract(6, 'days'), moment()];
            $rootScope.date_range_picker_ranges[$rootScope.langs.date_range_picker_Last30Days] = [moment().subtract(29, 'days'), moment()];
            $rootScope.date_range_picker_ranges[$rootScope.langs.date_range_picker_ThisMonth] = [moment().startOf('month'), moment().endOf('month')];
            $rootScope.date_range_picker_ranges[$rootScope.langs.date_range_picker_LastMonth] = [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')];
            
            console_log($rootScope.date_range_picker_ranges, '日期选择页签：$rootScope.date_range_picker_ranges');
        }
    }
    // ====全局：date_range_picker_locale初始化方法
    $rootScope.getDateRangePickerLocale = function() {
        if (typeof($rootScope.date_range_picker_locale) == 'undefined') {
            $rootScope.date_range_picker_locale = {
                applyLabel : $rootScope.langs.date_range_picker_applyLabel,
                cancelLabel : $rootScope.langs.date_range_picker_cancelLabel2,
                fromLabel : $rootScope.langs.date_range_picker_fromLabel,
                toLabel : $rootScope.langs.date_range_picker_toLabel,
                customRangeLabel : $rootScope.langs.date_range_picker_customRangeLabel,
                daysOfWeek : $rootScope.langs.date_range_picker_daysOfWeek,
                monthNames : $rootScope.langs.date_range_picker_monthNames,
                format: 'YYYY-MM-DD', // 控件中from和to 显示的日期格式  
                firstDay : 1
            };
        }
    }
    // ====全局：daterangepicker时间范围插件初始化方法
    $rootScope.daterangepicker_init = function(element_id) {
        $rootScope.getDateRangePickerRanges();
        $rootScope.getDateRangePickerLocale();
        $('#'+element_id).daterangepicker({
            ranges: $rootScope.date_range_picker_ranges,
            locale : $rootScope.date_range_picker_locale,
            linkedCalendars: false,
            alwaysShowCalendars: false,
            autoUpdateInput: false
        });
    }
    // ./全局：dateRangePicker插件相关
});


angular.module('myApp').controller('System/menu', function($scope, $rootScope, $http) {
	$scope.api_name = 'System/menu';
	$rootScope.menu = {};

	$http.post(api($scope.api_name)).then(function (respone) {
		if (respone.data.r) {
			$rootScope.menu = respone.data.data;
			console_log($rootScope.menu, '菜单');
		}
	});
	
    // 菜单栏sidebar加载完成后处理
    $scope.sidebarmenu_done = function() {
        //console.log('sidebarmenu_done');
        // tree初始化
    	
        $('.sidebar-menu').tree({
        	accordion: false,
        	animationSpeed: 300,
            boxWidgetOptions: {
                boxWidgetIcons: {
                  //Collapse icon
                  collapse: 'fa-minus',
                  //Open icon
                  open: 'fa-plus',
                  //Remove icon
                  remove: 'fa-times'
                }
            }
    	});
        
        // 菜单栏二级菜单展开/折叠点击事件处理
        $('.sidebar-menu').on('click', '.treemenu2', function (event) {
            var isOpen = $(this).parent().hasClass('menu-open');
            var element_i = $(this).find('i');

            if (isOpen) {
                element_i.removeClass().addClass('fa fa-minus-circle');
            }
            else {
                element_i.removeClass().addClass('fa fa-plus-circle');
            }
        });
        
        
        //重置导航栏，防止菜单数据加载比较慢，导致导航栏不正常显示
        $rootScope.set_breadcrumb();
    };
});


