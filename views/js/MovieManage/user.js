angular.module('myApp').controller('MovieManage/user', function($scope, $rootScope, $http, $filter) {
    $rootScope.api_name = 'MovieManage/user';
    $rootScope.set_breadcrumb();

    $scope.length_select = $rootScope.get_default_page_number();
    $scope.data = {};
    $scope.search = {};
    $scope.headfunc = {};
    //$scope.headfunc.download = 1;
    $scope.headfunc.manage = 0;
    $scope.headfunc.export = 1;

    //查询条件
    $scope.search = {};
    $scope.orderby = {orderby:{regtime:"desc"}};
    // ====时间范围初始化
    var today = new Date();
    var before = new Date();
    before.setDate(today.getDate()-29);
    //$scope.search.regtime = $filter('date')(before, 'yyyy-MM-dd') + ' - ' + $filter('date')(today, 'yyyy-MM-dd');
    $scope.add = {a:'add'};

    $scope.langs = $rootScope.langs;
    //加载语言包
    $http.get(lang($scope.api_name)).then(
        function (respone) {
            angular.extend($scope.langs, respone.data);
            // ====gridOptions.columnDefs初始化(语言包加载完成后执行)
            $scope.gridOptions.columnDefs = [
                 $rootScope.ui_grid.get_seq(),
                 $rootScope.ui_grid.get('userid', false, 100),
                 $rootScope.ui_grid.get('username', true, 100),
                 $rootScope.ui_grid.get('email', true, 100),
                 $rootScope.ui_grid.get_ts('regtime')
            ];
            $scope.gridOptions.columnDefs.push({
                field: 'operation',
                displayName: $scope.langs.operation,
                enableSorting: false,
                minWidth: 40,
                cellTemplate: '<div class="ui-grid-cell-contents ng-binding ng-scope">'
                             + '<button data-ng-click="grid.appScope.modal_del(row.entity)" class="btn btn-xs btn-danger btn-oper"  title="{{grid.appScope.langs.del}}"><i class="fa fa-times"></i></button>'
                             + '</div>'
            });


            $scope.gridOptions.exportColumnDefs = {
                seq:$rootScope.ui_grid.get_export_seq,
                regtime:$rootScope.ui_grid.get_export_ts,
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

            // 日期插件
            // ====配置变量
            $scope.search_date_id = 'search_regtime';
            // ====初始化
            $rootScope.daterangepicker_init($scope.search_date_id);
            // ====日期变化处理
            $('#'+$scope.search_date_id)
            .on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
                $scope.search.regtime = $(this).val();
            })
            .on('cancel.daterangepicker', function(ev, picker) {
                $(this).val('');
                $scope.search.regtime = $(this).val();
            });


            $scope.search_ext_loaded = true;
        }
    }
    //删除模态框
    $scope.modal_del = function (obj) {
        $scope.del = obj;
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
});

