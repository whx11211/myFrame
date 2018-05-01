angular.module('myApp').controller('System/role', function($scope, $rootScope, $http, $filter) {
	$rootScope.api_name = 'System/role';
	$rootScope.set_breadcrumb();
	
	$scope.length_select = $rootScope.get_default_page_number();
	$scope.data = {};
    $scope.search = {};
    $scope.headfunc = {};
    //$scope.headfunc.download = 1;
    $scope.headfunc.manage = 1;
    $scope.headfunc.export = 1;
    
    //查询条件
    $scope.search = {};
    $scope.orderby = {orderby:{id:"asc"}};
    // ====时间范围初始化
    var today = new Date();
    var before = new Date();
    before.setDate(today.getDate()-29);
    $scope.add = {a:'add'};
    
    $scope.langs = $rootScope.langs;
    //加载语言包
	$http.post(lang($scope.api_name)).then(
    	function (respone) {
    		angular.extend($scope.langs, respone.data);
    		// ====gridOptions.columnDefs初始化(语言包加载完成后执行)
            $scope.gridOptions.columnDefs = [
                 $rootScope.ui_grid.get_seq(),
                 $rootScope.ui_grid.get('id', false),
                 $rootScope.ui_grid.get_nosort('roleName'),
                 $rootScope.ui_grid.get_nosort('roleDesc'),
                 //$rootScope.ui_grid.get_nosort('privileges')
            ];
            $scope.gridOptions.columnDefs.push({
                field: 'operation',
                displayName: $scope.langs.operation,
                enableSorting: false,
                minWidth: 80,
                cellTemplate: '<div class="ui-grid-cell-contents ng-binding ng-scope">'
        					+ '<button data-ng-click="grid.appScope.modal_privilege(\'mod\',row.entity)" class="btn btn-xs btn-primary btn-oper" title="{{grid.appScope.langs.privilege_manage}}"><i class="fa fa-stack-overflow"></i></button>'
                			+ '<button data-ng-click="grid.appScope.modal_add(\'mod\', row.entity)" class="btn btn-xs btn-warning btn-oper" title="{{grid.appScope.langs.mod}}"><i class="fa fa-edit"></i></button>'
                			+ '<button data-ng-click="grid.appScope.modal_del(row.entity)" class="btn btn-xs btn-danger btn-oper"  title="{{grid.appScope.langs.del}}"><i class="fa fa-times"></i></button>'
                			+ '</div>'
            });
            
            
            $scope.gridOptions.exportColumnDefs = {
                seq:$rootScope.ui_grid.get_export_seq,
                lastLoginTime:$rootScope.ui_grid.get_export_ts
            }
            $scope.gridOptions.exporterFieldCallback = function( grid, row, col, input ) {
                if( typeof($scope.gridOptions.exportColumnDefs[col.name]) == 'function' ){
                    return $scope.gridOptions.exportColumnDefs[col.name]( grid, row, col, input, $filter );
                }
                else {
                    return input;
                }
            };
            $scope.gridOptions.exporterCsvFilename = $scope.api_name + '.csv';
            
            
    	}
    );
	
	//获取角色信息
	if (typeof($rootScope.admin_roles) == 'undefined') {
		$http.post(api('Common/getRolesInfo')).then(
	    	function (respone) {
	    		if (respone.data.r) {
	    			$rootScope.admin_roles=respone.data.data;
	    		}
	    		else {
	    			$rootScope.show_error(respone.data, $scope.modal_add);
	    		}
	    	}
		);
	}
	
	//获取完整权限菜单
	if (typeof($rootScope.all_menu) == 'undefined') {
		$http.post(api($scope.api_name), {a:'get_all_menu'}).then(function (respone) {
			if (respone.data.r) {
				$rootScope.all_menu = respone.data.data;
			}
		});
	}
	
    // ui-grid
    // ====调整样式的grid高度
    $scope.ui_grid_style = {};
    // ====gridOptions初始化
    $scope.gridOptions = $rootScope.ui_grid.init();
	

    $scope.modal_search = function () {
    	$('#modal_search').modal();
    }
    
    $scope.modal_search_ok = function () {
    	$('#modal_search').modal('hide');
    	$scope.get_data();
    }
    
    $scope.get_data = function (page) {
    	if (!is_int(page) || !page) {
    		page = 1;
    	}
    	var post_data = { page:page, num:$scope.length_select };
    	$http.post(api($scope.api_name), angular.extend(post_data, $scope.search,$scope.orderby)).then(function (respone) {
    		if (respone.data.r) {
    			$scope.data = respone.data.data;
    			console_log($scope.data, '角色数据');
    			// 显示数据绑定
                $scope.ui_grid_style.height = (parseInt($scope.data.items.length) + 1)*30 + 2 + 'px';
                $scope.gridOptions.data = $scope.data.items;
                $scope.r = 1;
    		}
    		else {
    			$rootScope.show_error(respone.data);
    		}
    	});
    }
    $scope.get_data(1);
	
    $scope.jump = function (page) {
    	$scope.get_data(page);
    }

	
    // ====注入事件处理方法
    $scope.gridOptions.onRegisterApi = function( gridApi ) {
        $scope.gridApi = gridApi;//console_log(gridApi);
        //gridApi.expandable.expandAllRows();
        $scope.gridApi.core.on.sortChanged( $scope, $scope.sortChanged );
    };
    // ====外部排序
    // ========开启
    $scope.gridOptions.useExternalSorting = true;
    // ========事件处理
    $scope.sortChanged = function ( grid, sortColumns ) {
        $scope.orderby.orderby = {};
        if (sortColumns.length > 0) {
            angular.forEach(sortColumns, function(data){
                $scope.orderby.orderby[data.field] = data.sort.direction;
            });
        }
        // 重新获取数据
        $scope.get_data();
    };
	
	
    // =================当页数据导出================
    $scope.export = function() {
        $scope.gridApi.exporter.csvExport( 'visible', 'visible');
    }
    // =================./当页数据导出================
    
    //模态框回调
    $('#modal_search').on("show.bs.modal", function(){
        $scope.search_ext_load();
    });
    //查询页面相关插件初始化
    $scope.search_ext_load = function() {
        if (typeof($scope.search_ext_loaded) == 'undefined') {
            
            
            
            
            $scope.search_ext_loaded = true;
        }
    }
    
    //添加/修改模态框唤起
    $scope.modal_add = function(action, obj) {
    	switch (action) {
    		case 'mod':
    			if ($scope.add.a == 'add') {
    				$scope.add_tmp = $scope.add;
    			}
    			$scope.add = angular.copy(obj);
    			$scope.add.a = 'mod';
    			break;
    		case 'add':
    			if ($scope.add.a == 'mod') {
    				$scope.add = $scope.add_tmp;
    			}
    			break;
    	}
    	$('#modal_add').modal('show');
    }
    
    //添加/修改数据提交
    $scope.modal_add_ok = function () {
    	$('#modal_add').modal('hide');
    	$http.post(api($scope.api_name), $scope.add).then(function (respone) {
    		if (respone.data.r) {
    			$rootScope.show_success($scope.add.a, $scope.get_data);
    		}
    		else {
    			$rootScope.show_error(respone.data, $scope.modal_add);
    		}
    	});
    }
    
    //删除模态框
    $scope.modal_del = function (obj) {
    	$scope.del = angular.copy(obj);
    	$scope.del.a = 'del';
    	$('#modal_del').modal('show');
    }
    
    $scope.modal_del_ok = function () {
    	$('#modal_del').modal('hide');
    	$http.post(api($scope.api_name), $scope.del).then(function (respone) {
    		if (respone.data.r) {
    			$rootScope.show_success($scope.del.a, $scope.get_data);
    		}
    		else {
    			$rootScope.show_error(respone.data, $scope.modal_add);
    		}
    	});
    }
    

    //权限修改模态框
    $scope.modal_privilege = function (action, obj) {
    	if (!$scope.modal_privilege_init) {
            $('#pri_manage').tree({
            	accordion: false,
            	animationSpeed: 300,
        	});
            //选择框点击事件
            $('#modal_privilege :input[type=checkbox]').click(function(){
            	//选中/取消父菜单后所有子菜单自动选中/取消
            	$(this).nextAll('ul').find(':input[type=checkbox]').prop('checked', $(this).is(":checked"));
            	
            	if ($(this).is(":checked")) {
            		//选中子菜单后服菜单默认选中
                	$(this).parent().parent().prevAll(':input').prop('checked', true);
                	$(this).parent().parent().parent().parent().prevAll(':input').prop('checked', true);
            	}
            	else {
            		//取消子菜单处理
            		if ($(this).parent().parent().find(':input[type=checkbox]:checked').length==0) {
            			$(this).parent().parent().prevAll(':input').prop('checked', false);
            			if ($(this).parent().parent().parent().parent().find(':input[type=checkbox]:checked').length==0) {
            				$(this).parent().parent().parent().parent().prevAll(':input').prop('checked', false);
            			}
            		}
            	}
            });
            
            $('#modal_privilege .pri_btn').click(function() {
            	if ($(this).parent().hasClass('menu-open')) {
            		$(this).find('i').removeClass('fa-minus-square-o').addClass('fa-plus-square-o');
            	}
            	else {
            		$(this).find('i').removeClass('fa-plus-square-o').addClass('fa-minus-square-o');
            	}
            });
            $scope.modal_privilege_init = true;
    	}
    	switch (action) {
			case 'mod':
		    	$scope.privilege = angular.copy(obj);
		    	$scope.privilege.a = 'mod';
		    	$scope.privilege.privileges = $scope.privilege.privileges.split(',');
		    	$.each($('#modal_privilege :input[type=checkbox]'), function() {
		    		if ($scope.privilege.privileges.indexOf($(this).prop('value')) >= 0) {
		    			$(this).prop('checked', true);
		    		}
		    		else {
		    			$(this).prop('checked', false);
		    		}
		    	});
				break;
    	}
    	$('#modal_privilege').modal('show');
    }
    
    $scope.modal_privilege_ok = function () {
    	$('#modal_privilege').modal('hide');
		var tmp = [];
    	$.each($('#modal_privilege :input[type=checkbox]:checked'), function() {
    		tmp.push($(this).attr('value'));
    	});
    	$scope.privilege.privileges = tmp.toString();
    	$http.post(api($scope.api_name), $scope.privilege).then(function (respone) {
    		if (respone.data.r) {
    			$rootScope.show_success($scope.privilege.a, $scope.get_data);
    		}
    		else {
    			$rootScope.show_error(respone.data, $scope.modal_privilege);
    		}
    	});
    }
});