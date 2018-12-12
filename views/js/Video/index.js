angular.module('myApp').controller('Video/index', function($scope, $rootScope, $http, $filter) {
	$rootScope.api_name = 'Video/index';
	$rootScope.set_breadcrumb();
	
	$scope.length_select = $rootScope.get_default_page_number();
	$scope.data = {};
    $scope.search = {};
    $scope.headfunc = {};
    //$scope.headfunc.download = 1;
    $scope.headfunc.manage = 0;
    $scope.headfunc.export = 0;
    
    //查询条件
    $scope.search = {};
    $scope.orderby = {orderby:{id:"desc"}};
    // ====时间范围初始化
    var today = new Date();
    var before = new Date();
    before.setDate(today.getDate()-29);
    //$scope.search.last_mod_time = $filter('date')(before, 'yyyy-MM-dd') + ' - ' + $filter('date')(today, 'yyyy-MM-dd');
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
                 {
                	 field: 'file_name',
                     displayName: $scope.langs.file_name,
                     minWidth: "300",
                     cellTemplate: '<div class="ui-grid-cell-contents ng-binding ng-scope"  style="white-space:normal">{{row.entity.file_name}}</div>'
                 },
                 {
                     field: 'preview_image',
                     displayName: $scope.langs.preview_image,
                     minWidth: "170",
                     cellTemplate: '<div class="ui-grid-cell-contents"'
                     + ' ui-grid-one-bind-html="row.entity.preview_image"'
                     + ' data-toggle="tooltip" data-placement="auto" data-html="true" '
                     + ' title="<img style=\'max-width:600px;max-height:500px;\' src=\'images/ffmpeg/{{row.entity.file_index}}.png\'/>"'
                     + '</div>'
                 },
                 $rootScope.ui_grid.get('file_size', true, 60),
                 $rootScope.ui_grid.get('duration', true, 60),
                 $rootScope.ui_grid.get('create_time', false, 130),
                 $rootScope.ui_grid.get('last_mod_time', true, 130),
                 $rootScope.ui_grid.get('last_view_time', false, 130),
                 $rootScope.ui_grid.get('view_count', true, 50)
            ];
            $scope.gridOptions.columnDefs.push({
                field: 'operation',
                displayName: $scope.langs.operation,
                enableSorting: false,
                minWidth: 100,
                cellTemplate: '<div class="ui-grid-cell-contents ng-binding ng-scope">'
                			 + '<button data-ng-click="grid.appScope.modal_play(row.entity)" class="btn btn-xs btn-success btn-oper"  title="{{grid.appScope.langs.detail}}"><i class="glyphicon glyphicon-eye-open"></i></button>'
                			 + '<button data-ng-click="grid.appScope.modal_add(\'mod\',row.entity)" class="btn btn-xs btn-warning btn-oper"  title="{{grid.appScope.langs.mod}}"><i class="fa fa-edit"></i></button>'
                			 + '<button data-ng-click="grid.appScope.modal_del(row.entity)" class="btn btn-xs btn-danger btn-oper"  title="{{grid.appScope.langs.del}}"><i class="fa fa-times"></i></button>'
                			 + '</div>'
            });
            
            
            $scope.gridOptions.exportColumnDefs = {
                seq:$rootScope.ui_grid.get_export_seq,
                posttime:$rootScope.ui_grid.get_export_ts,
                lastreply:$rootScope.ui_grid.get_export_ts
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

    if (typeof($rootScope.tags_conf) == 'undefined') {
        $http.post(api($scope.api_name), {a: 'getTagsConf'}).then(
            function (respone) {
                if (respone.data.r) {
                    $rootScope.tags_conf = respone.data.data;
                }
                else {
                    $rootScope.show_error(respone.data);
                }
            }
        );
    }
	
	
    // ui-grid
    // ====调整样式的grid高度
    $scope.ui_grid_style = {};
    // ====gridOptions初始化
    $scope.gridOptions = $rootScope.ui_grid.init();
    $scope.gridOptions.rowHeight = '100px';


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
    			for (var i in $scope.data.items) {
                    $scope.data.items[i].preview_image = '<img src="images/ffmpeg/'+$scope.data.items[i].file_index+'.png" style="max-height: 100%;max-width:100%;"/>';
                }
    			console_log($scope.data, '视频数据');
    			// 显示数据绑定
                $scope.ui_grid_style.height = parseInt($scope.data.items.length)*$scope.gridOptions.rowHeight + 30 + 'px';
                $scope.gridOptions.data = [];
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
            
            // 日期插件
            // ====配置变量
            $scope.search_date_id = 'search_last_mod_time';
            // ====初始化
            $rootScope.daterangepicker_init($scope.search_date_id);
            // ====日期变化处理
            $('#'+$scope.search_date_id)
            .on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
                $scope.search.last_mod_time = $(this).val();
            })
            .on('cancel.daterangepicker', function(ev, picker) {
                $(this).val('');
                $scope.search.last_mod_time = $(this).val();
            });
            
            $('#tags_select2').select2();

            $scope.search_ext_loaded = true;
        }
    }
    //删除模态框
    $scope.modal_del = function (obj) {
    	$scope.del = obj;
    	$scope.del.a = 'del';
    	$scope.modal_del_info = $scope.langs.modal_del_info_detail.replace('%s', obj.file_name);
    	$('#modal_del').modal('show');
    }
    
    $scope.modal_del_ok = function () {
    	$('#modal_del').modal('hide');
    	$http.post(api($scope.api_name), $scope.del).then(function (respone) {
    		if (respone.data.r) {
    			$rootScope.show_success($scope.del.a, $scope.get_data);
    		}
    		else {
    			$rootScope.show_error(respone.data);
    		}
    	});
    }
    //播放模态框
    $('#modal_play').on("hide.bs.modal", function(){
        $scope.video_url = '';
    });
    $scope.play = {};
    $scope.modal_play = function (obj) {
        $scope.play = obj;
    	$http.post(api($scope.api_name), angular.extend({a:'play'}, obj)).then(function (respone) {
    		if (respone.data.r) {
    			$scope.video_url = respone.data.data;
    			$('#modal_play').modal({
                    backdrop: "static",//点击空白处不关闭对话框
                    show:true
                });
    		}
    		else {
    			$rootScope.show_error(respone.data);
    		}
    	});
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
                $rootScope.show_error(respone.data);
            }
        });
    }
});