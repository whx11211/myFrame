angular.module('myApp').controller('Video/tag', function($scope, $rootScope, $http, $filter) {
    $rootScope.api_name = 'Video/tag';
    $rootScope.set_breadcrumb();

    $scope.length_select = $rootScope.get_default_page_number();
    $scope.data = {};
    $scope.search = {};
    $scope.headfunc = {};
    //$scope.headfunc.download = 1;
    $scope.headfunc.manage = 1;
    $scope.headfunc.export = 1;
    $scope.headfunc.del_path = 1;

    //查询条件
    $scope.search = {};
    $scope.orderby = {orderby:{tag_id:'desc'}};
    // ====时间范围初始化
    var today = new Date();
    var before = new Date();
    before.setDate(today.getDate()-29);
    $scope.add = {a:'add'};

    $scope.get_tags_conf = function() {
        $http.post(api('Video/index'), {a: 'getTagsConf'}).then(
            function (respone) {
                if (respone.data.r) {
                    $rootScope.video_tags_conf = respone.data.data;
                }
                else {
                    $rootScope.show_error(respone.data);
                }
            }
        );
    }

    if (typeof($rootScope.video_tags_conf) == 'undefined' || $rootScope.video_tags_conf_refresh == true) {
        $scope.get_tags_conf();
        $rootScope.video_tags_conf_refresh = false;
    }

    $scope.langs = $rootScope.langs;
    //加载语言包
    $http.get(lang($scope.api_name)).then(
        function (respone) {
            angular.extend($scope.langs, respone.data);
            // ====gridOptions.columnDefs初始化(语言包加载完成后执行)
            $scope.gridOptions.columnDefs = [
                 $rootScope.ui_grid.get_seq(),
                 $rootScope.ui_grid.get('tag_id',false),
                 $rootScope.ui_grid.get('tag_name'),
                 $rootScope.ui_grid.get('path', false, 300),
                {
                    field: 'parent_id',
                    displayName: $scope.langs.parent,
                    minWidth: "80",
                    visible:true,
                    cellTemplate: '<div class="ui-grid-cell-contents ng-binding ng-scope"  style="white-space:normal;word-break: break-all;">{{row.entity.parent_id | get_name_by_id:grid.appScope.video_tags_conf:"tag_id":"tag_name" }}</div>'
                },
                 $rootScope.ui_grid.get('video_count'), 
                 $rootScope.ui_grid.get('search_count'),
                $rootScope.ui_grid.get('create_time', true, 130),
            ];
            $scope.gridOptions.columnDefs.push({
                field: 'operation',
                displayName: $scope.langs.operation,
                enableSorting: false,
                minWidth: 75,
                cellTemplate: '<div class="ui-grid-cell-contents ng-binding ng-scope">'
                             + '<button data-ng-click="grid.appScope.modal_add(\'mod\', row.entity)" class="btn btn-xs btn-warning btn-oper" title="{{grid.appScope.langs.mod}}"><i class="fa fa-edit"></i></button>'
                             + '<button data-ng-click="grid.appScope.modal_del(row.entity)" class="btn btn-xs btn-danger btn-oper"  title="{{grid.appScope.langs.del}}"><i class="fa fa-times"></i></button>'
                             + '</div>'
            });


            $scope.gridOptions.exportColumnDefs = {
                seq:$rootScope.ui_grid.get_export_seq,
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


    // ui-grid
    // ====调整样式的grid高度
    $scope.ui_grid_style = {};
    $scope.ui_grid_style_reply = {};
    // ====gridOptions初始化
    $scope.gridOptions = $rootScope.ui_grid.init();
    $scope.gridOptionsReply = $rootScope.ui_grid.init()


    $scope.modal_search = function () {
        $('#modal_search').modal();
    }

    $scope.modal_search_ok = function () {
        $('#modal_search').modal('hide');
        $scope.get_data(1);
    }

    $scope.get_data = function (page) {
        if (!is_int(page) || !page) {
            page = 1;
            if (typeof($scope.data.page_current) != 'undefined') {
                page = $scope.data.page_current;
            }
        }
        var post_data = { page:page, num:$scope.length_select };
        $http.post(api($scope.api_name), angular.extend(post_data, $scope.search,$scope.orderby)).then(function (respone) {
            if (respone.data.r) {
                $scope.data = respone.data.data;
                console_log($scope.data, '帖子数据');
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
        $scope.get_data(1);
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

            $('#search_parent_select2').select2({allowClear:true});

            $scope.search_ext_loaded = true;
        }
    }
    //删除模态框
    $scope.modal_del = function (obj) {
        $scope.del = obj;
        $scope.del.a = 'del';
        $scope.modal_del_info = $scope.langs.modal_del_info_detail.replace('%s', obj.tag_name);
        $('#modal_del').modal('show');
    }

    $scope.modal_del_ok = function () {
        $('#modal_del').modal('hide');
        $http.post(api($scope.api_name), $scope.del).then(function (respone) {
            if (respone.data.r) {
                $rootScope.show_success($scope.del.a, $scope.get_data);
                $rootScope.video_tags_conf_refresh = true
            }
            else {
                $rootScope.show_error(respone.data);
            }
        });
    }
    //添加/修改模态框唤起
    $scope.modal_add= function(action, obj) {
        switch (action) {
            case 'mod':
                if ($scope.add.a== 'add') {
                    $scope.add_tmp= $scope.add;
                }
                $scope.add= angular.copy(obj);
                $scope.add.a= 'mod';
                break;
            case 'add':
                if ($scope.add.a== 'mod') {
                    $scope.add= $scope.add_tmp;
                }
                break;
        }
        $scope.add_ext_load();
        $('#modal_add').modal('show');
    }

    //添加/修改数据提交
    $scope.modal_add_ok= function () {
        $('#modal_add').modal('hide');
        $http.post(api($scope.api_name), $scope.add).then(function (respone) {
            if (respone.data.r) {
                $rootScope.show_success($scope.add.a, function() {
                    $scope.get_data();
                    $scope.get_tags_conf();
                });
                $scope.add.tag_name = '';
                $scope.add.parent = 0;
                $scope.add.path = '';
                $rootScope.video_tags_conf_refresh = true
            }
            else {
                $rootScope.show_error(respone.data, $scope.modal_add);
            }
        });
    }

    //删除文件夹模态框
    $scope.modal_del_path = function (obj) {
        $scope.del_path = {a:'delPath',path:''};
        $('#modal_del_path').modal('show');
    }

    $scope.modal_del_path_ok = function () {
        $('#modal_del_path').modal('hide');
        $http.post(api($scope.api_name), $scope.del_path).then(function (respone) {
            if (respone.data.r) {
                $rootScope.show_success('del', $scope.get_data);
                $rootScope.video_tags_conf_refresh = true
            }
            else {
                $rootScope.show_error(respone.data);
            }
        });
    }

    $scope.add_ext_load = function() {
        if (typeof($scope.add_ext_loaded) == 'undefined') {

            $('#add_parent_select2').select2({allowClear:true});

            $scope.add_ext_loaded = true;
        }
    }

    $('#modal_add').on("shown.bs.modal", function(){
        $('#add_parent_select2').select2({
            //tags:true
            allowClear:true
        });
    });
});

