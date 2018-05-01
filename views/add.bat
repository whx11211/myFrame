::新功能添加辅助脚本

chcp 65001
@echo off
echo Hello!



::初始化配置
echo Please input class name:
set/p class=
echo.

echo Please input method name:
set/p method=
echo.

echo Please input time field, if not exist you can input nothing:
set/p time_field=
echo.

echo Please input other display fields.
echo e.g. id,username or id username:
set/p fields=
echo.

echo Please input other search fields.
echo e.g. id,username or id username:
set/p s_fields=

echo.
echo Need add and modify? y or no
set/p need_add_mod=

if not defined need_add_mod set need_add_mod=n

echo.
echo.

set func=%class%/%method%

::js文件生成
set js_dir=js/%class%
set js_file=%js_dir%/%method%.js
IF NOT EXIST "%js_dir%" MD "%js_dir%"

echo angular.module(^'myApp^').controller(^'%func%^'^, function($scope^, $rootScope^, $http^, $filter) {>%js_file%
echo     $rootScope.api_name ^= ^'%func%^'^;>>%js_file%
echo     $rootScope.set_breadcrumb()^;>>%js_file%
echo.>>%js_file%
echo     $scope.length_select ^= $rootScope.get_default_page_number()^;>>%js_file%
echo     $scope.data ^= {}^;>>%js_file%
echo     $scope.search ^= {}^;>>%js_file%
echo     $scope.headfunc ^= {}^;>>%js_file%
echo     //$scope.headfunc.download ^= 1^;>>%js_file%
if %need_add_mod%==y (echo     $scope.headfunc.manage ^= 1^;>>%js_file%) else echo     $scope.headfunc.manage ^= 0^;>>%js_file%
echo     $scope.headfunc.export ^= 1^;>>%js_file%
echo.>>%js_file%
echo     //查询条件>>%js_file%
echo     $scope.search ^= {}^;>>%js_file%
if defined time_field echo     $scope.orderby ^= {orderby:{%time_field%:"desc"}}^;>>%js_file%
echo     // ^=^=^=^=时间范围初始化>>%js_file%
echo     var today ^= new Date()^;>>%js_file%
echo     var before ^= new Date()^;>>%js_file%
echo     before.setDate(today.getDate()-29)^;>>%js_file%
if defined time_field  echo     //$scope.search.%time_field% ^= $filter(^'date^')(before^, ^'yyyy-MM-dd^') + ^' - ^' + $filter(^'date^')(today^, ^'yyyy-MM-dd^')^;>>%js_file%
echo     $scope.add ^= {a:^'add^'}^;>>%js_file%
echo.>>%js_file%
echo     $scope.langs ^= $rootScope.langs^;>>%js_file%
echo     //加载语言包>>%js_file%
echo     $http.post(lang($scope.api_name)).then(>>%js_file%
echo         function (respone) {>>%js_file%
echo             angular.extend($scope.langs^, respone.data)^;>>%js_file%
echo             // ^=^=^=^=gridOptions.columnDefs初始化(语言包加载完成后执行)>>%js_file%
echo             $scope.gridOptions.columnDefs ^= [>>%js_file%
echo                  $rootScope.ui_grid.get_seq()^,>>%js_file%
if defined fields for %%i in (%fields%) do echo                  $rootScope.ui_grid.get(^'%%i^'),>>%js_file% 
if defined time_field echo                  $rootScope.ui_grid.get_ts(^'%time_field%^'),>>%js_file%
echo             ]^;>>%js_file%
echo             $scope.gridOptions.columnDefs.push({>>%js_file%
echo                 field: ^'operation^'^,>>%js_file%
echo                 displayName: $scope.langs.operation^,>>%js_file%
echo                 enableSorting: false^,>>%js_file%
echo                 minWidth: 40^,>>%js_file%
echo                 cellTemplate: ^'^<div class^="ui-grid-cell-contents ng-binding ng-scope"^>^'>>%js_file%
if %need_add_mod%==y echo                              + ^'^<button data-ng-click^="grid.appScope.modal_add(\'mod\', row.entity)" class^="btn btn-xs btn-warning btn-oper" title^="{{grid.appScope.langs.mod}}"^>^<i class^="fa fa-edit"^>^</i^>^</button^>^'>>%js_file%
echo                              + ^'^<button data-ng-click^="grid.appScope.modal_del(row.entity)" class^="btn btn-xs btn-danger btn-oper"  title^="{{grid.appScope.langs.del}}"^>^<i class^="fa fa-times"^>^</i^>^</button^>^'>>%js_file%
echo                              + ^'^</div^>^'>>%js_file%
echo             })^;>>%js_file%
echo.>>%js_file%
echo.>>%js_file%
echo             $scope.gridOptions.exportColumnDefs ^= {>>%js_file%
echo                 seq:$rootScope.ui_grid.get_export_seq^,>>%js_file%
if defined time_field  echo                 %time_field%:$rootScope.ui_grid.get_export_ts^,>>%js_file%
echo             }>>%js_file%
echo             $scope.gridOptions.exporterFieldCallback ^= function( grid^, row^, col^, input ) {>>%js_file%
echo                 if( typeof($scope.gridOptions.exportColumnDefs[col.name]) ^=^= ^'function^' ){>>%js_file%
echo                     return $scope.gridOptions.exportColumnDefs[col.name]( grid^, row^, col^, input^, $filter )^;>>%js_file%
echo                 }>>%js_file%
echo                 else {>>%js_file%
echo                     return input^;>>%js_file%
echo                 }>>%js_file%
echo             }^;>>%js_file%
echo             $scope.gridOptions.exporterCsvFilename ^= $scope.api_name + ^'.csv^'^;>>%js_file%
echo.>>%js_file%
echo         }>>%js_file%
echo     )^;>>%js_file%
echo.>>%js_file%
echo.>>%js_file%
echo     // ui-grid>>%js_file%
echo     // ^=^=^=^=调整样式的grid高度>>%js_file%
echo     $scope.ui_grid_style ^= {}^;>>%js_file%
echo     $scope.ui_grid_style_reply ^= {}^;>>%js_file%
echo     // ^=^=^=^=gridOptions初始化>>%js_file%
echo     $scope.gridOptions ^= $rootScope.ui_grid.init()^;>>%js_file%
echo     $scope.gridOptionsReply ^= $rootScope.ui_grid.init()>>%js_file%
echo.>>%js_file%
echo.>>%js_file%
echo     $scope.modal_search ^= function () {>>%js_file%
echo         $(^'#modal_search^').modal()^;>>%js_file%
echo     }>>%js_file%
echo.>>%js_file%
echo     $scope.modal_search_ok ^= function () {>>%js_file%
echo         $(^'#modal_search^').modal(^'hide^')^;>>%js_file%
echo         $scope.get_data()^;>>%js_file%
echo     }>>%js_file%
echo.>>%js_file%
echo     $scope.get_data ^= function (page) {>>%js_file%
echo         if (^!is_int(page) ^|^| ^!page) {>>%js_file%
echo             page ^= 1^;>>%js_file%
echo         }>>%js_file%
echo         var post_data ^= { page:page^, num:$scope.length_select }^;>>%js_file%
echo         $http.post(api($scope.api_name)^, angular.extend(post_data^, $scope.search^,$scope.orderby)).then(function (respone) {>>%js_file%
echo             if (respone.data.r) {>>%js_file%
echo                 $scope.data ^= respone.data.data^;>>%js_file%
echo                 console_log($scope.data^, ^'帖子数据^')^;>>%js_file%
echo                 // 显示数据绑定>>%js_file%
echo                 $scope.ui_grid_style.height ^= (parseInt($scope.data.items.length) + 1)*30 + 2 + ^'px^'^;>>%js_file%
echo                 $scope.gridOptions.data ^= $scope.data.items^;>>%js_file%
echo                 $scope.r ^= 1^;>>%js_file%
echo             }>>%js_file%
echo             else {>>%js_file%
echo                 $rootScope.show_error(respone.data)^;>>%js_file%
echo             }>>%js_file%
echo         })^;>>%js_file%
echo     }>>%js_file%
echo     $scope.get_data(1)^;>>%js_file%
echo.>>%js_file%
echo     $scope.jump ^= function (page) {>>%js_file%
echo         $scope.get_data(page)^;>>%js_file%
echo     }>>%js_file%
echo.>>%js_file%
echo.>>%js_file%
echo     // ^=^=^=^=注入事件处理方法>>%js_file%
echo     $scope.gridOptions.onRegisterApi ^= function( gridApi ) {>>%js_file%
echo         $scope.gridApi ^= gridApi^;//console_log(gridApi)^;>>%js_file%
echo         //gridApi.expandable.expandAllRows()^;>>%js_file%
echo         $scope.gridApi.core.on.sortChanged( $scope^, $scope.sortChanged )^;>>%js_file%
echo     }^;>>%js_file%
echo     // ^=^=^=^=外部排序>>%js_file%
echo     // ^=^=^=^=^=^=^=^=开启>>%js_file%
echo     $scope.gridOptions.useExternalSorting ^= true^;>>%js_file%
echo     // ^=^=^=^=^=^=^=^=事件处理>>%js_file%
echo     $scope.sortChanged ^= function ( grid^, sortColumns ) {>>%js_file%
echo         $scope.orderby.orderby ^= {}^;>>%js_file%
echo         if (sortColumns.length ^> 0) {>>%js_file%
echo             angular.forEach(sortColumns^, function(data){>>%js_file%
echo                 $scope.orderby.orderby[data.field] ^= data.sort.direction^;>>%js_file%
echo             })^;>>%js_file%
echo         }>>%js_file%
echo         // 重新获取数据>>%js_file%
echo         $scope.get_data()^;>>%js_file%
echo     }^;>>%js_file%
echo.>>%js_file%
echo.>>%js_file%
echo     // ^=^=^=^=^=^=^=^=^=^=^=^=^=^=^=^=^=当页数据导出^=^=^=^=^=^=^=^=^=^=^=^=^=^=^=^=>>%js_file%
echo     $scope.export ^= function() {>>%js_file%
echo         $scope.gridApi.exporter.csvExport( ^'visible^'^, ^'visible^')^;>>%js_file%
echo     }>>%js_file%
echo     // ^=^=^=^=^=^=^=^=^=^=^=^=^=^=^=^=^=./当页数据导出^=^=^=^=^=^=^=^=^=^=^=^=^=^=^=^=>>%js_file%
echo.>>%js_file%
echo     //模态框回调>>%js_file%
echo     $(^'#modal_search^').on("show.bs.modal"^, function(){>>%js_file%
echo         $scope.search_ext_load()^;>>%js_file%
echo     })^;>>%js_file%
echo     //查询页面相关插件初始化>>%js_file%
echo     $scope.search_ext_load ^= function() {>>%js_file%
echo         if (typeof($scope.search_ext_loaded) ^=^= ^'undefined^') {>>%js_file%
echo.>>%js_file%
if defined time_field 	echo             // 日期插件>>%js_file%
if defined time_field 	echo             // ^=^=^=^=配置变量>>%js_file%
if defined time_field 	echo             $scope.search_date_id ^= ^'search_%time_field%^'^;>>%js_file%
if defined time_field 	echo             // ^=^=^=^=初始化>>%js_file%
if defined time_field 	echo             $rootScope.daterangepicker_init($scope.search_date_id)^;>>%js_file%
if defined time_field 	echo             // ^=^=^=^=日期变化处理>>%js_file%
if defined time_field 	echo             $(^'#^'+$scope.search_date_id)>>%js_file%
if defined time_field 	echo             .on(^'apply.daterangepicker^'^, function(ev^, picker) {>>%js_file%
if defined time_field 	echo                 $(this).val(picker.startDate.format(^'YYYY-MM-DD^') + ^' - ^' + picker.endDate.format(^'YYYY-MM-DD^'))^;>>%js_file%
if defined time_field 	echo                 $scope.search.%time_field% ^= $(this).val()^;>>%js_file%
if defined time_field 	echo             })>>%js_file%
if defined time_field 	echo             .on(^'cancel.daterangepicker^'^, function(ev^, picker) {>>%js_file%
if defined time_field 	echo                 $(this).val(^'^')^;>>%js_file%
if defined time_field 	echo                 $scope.search.%time_field% ^= $(this).val()^;>>%js_file%
if defined time_field 	echo             })^;>>%js_file%
echo.>>%js_file%
echo.>>%js_file%
echo             $scope.search_ext_loaded ^= true^;>>%js_file%
echo         }>>%js_file%
echo     }>>%js_file%
echo     //删除模态框>>%js_file%
echo     $scope.modal_del ^= function (obj) {>>%js_file%
echo         $scope.del ^= obj^;>>%js_file%
echo         $scope.del.a ^= ^'del^'^;>>%js_file%
echo         $(^'#modal_del^').modal(^'show^')^;>>%js_file%
echo     }>>%js_file%
echo.>>%js_file%
echo     $scope.modal_del_ok ^= function () {>>%js_file%
echo         $(^'#modal_del^').modal(^'hide^')^;>>%js_file%
echo         $http.post(api($scope.api_name)^, $scope.del).then(function (respone) {>>%js_file%
echo             if (respone.data.r) {>>%js_file%
echo                 $rootScope.show_success($scope.del.a^, $scope.get_data)^;>>%js_file%
echo             }>>%js_file%
echo             else {>>%js_file%
echo                 $rootScope.show_error(respone.data^, $scope.modal_add)^;>>%js_file%
echo             }>>%js_file%
echo         })^;>>%js_file%
echo     }>>%js_file%
echo     //添加/修改模态框唤起>>%js_file%
echo     $scope.modal_add^= function(action^, obj) {>>%js_file%
echo         switch (action) {>>%js_file%
echo             case 'mod':>>%js_file%
echo                 if ($scope.add.a^== 'add') {>>%js_file%
echo                     $scope.add_tmp^= $scope.add^;>>%js_file%
echo                 }>>%js_file%
echo                 $scope.add^= angular.copy(obj)^;>>%js_file%
echo                 $scope.add.a^= 'mod'^;>>%js_file%
echo                 break^;>>%js_file%
echo             case 'add':>>%js_file%
echo                 if ($scope.add.a^== 'mod') {>>%js_file%
echo                     $scope.add^= $scope.add_tmp^;>>%js_file%
echo                 }>>%js_file%
echo                 break^;>>%js_file%
echo         }>>%js_file%
echo         $('#modal_add').modal('show')^;>>%js_file%
echo     }>>%js_file%
echo.>>%js_file%
echo     //添加/修改数据提交>>%js_file%
echo     $scope.modal_add_ok^= function () {>>%js_file%
echo         $('#modal_add').modal('hide')^;>>%js_file%
echo         $http.post(api($scope.api_name)^, $scope.add).then(function (respone) {>>%js_file%
echo             if (respone.data.r) {>>%js_file%
echo                 $rootScope.show_success($scope.add.a^, $scope.get_data)^;>>%js_file%
echo             }>>%js_file%
echo             else {>>%js_file%
echo                 $rootScope.show_error(respone.data^, $scope.modal_add)^;>>%js_file%
echo             }>>%js_file%
echo         })^;>>%js_file%
echo     }>>%js_file%
echo })^;>>%js_file%
echo.>>%js_file%
        

echo %js_file% is ok
echo.

::html文件生成
set html_dir=pages/%class%
set html_file=%html_dir%/%method%.html
IF NOT EXIST "%html_dir%" MD "%html_dir%"

echo ^<^!-- 数据显示box --^>>%html_file%
echo ^<div class^="row" data-ng-show^="r"^>>>%html_file%
echo     ^<div class^="col-sm-10 col-sm-offset-1"^>>>%html_file%
echo         ^<div class^="box"^>>>%html_file%
echo             ^<div class^="box-header" data-ng-include^="'pages/common/box-header.html'"^>>>%html_file%
echo                 ^<^!-- pages/common/box-header.html --^>>>%html_file%
echo             ^</div^>   >>%html_file%
echo.>>%html_file%
echo             ^<div class^="box-body" data-ng-include^="'pages/common/box-body.html'"^>>>%html_file%
echo                 ^<^!-- pages/common/box-body.html --^>>>%html_file%
echo             ^</div^>>>%html_file%
echo.>>%html_file%
echo             ^<div class^="box-footer" data-ng-include^="'pages/common/box-footer.html'"^>>>%html_file%
echo                 ^<^!-- pages/common/box-footer.html --^>>>%html_file%
echo             ^</div^>>>%html_file%
echo         ^</div^>>>%html_file%
echo     ^</div^>>>%html_file%
echo ^</div^>>>%html_file%
echo.>>%html_file%
echo.>>%html_file%
echo ^<^!-- 模态框（删除） --^>>>%html_file%
echo ^<div data-ng-include^="'pages/common/modal-del.html'"^>^</div^>>>%html_file%
echo.>>%html_file%
echo ^<^!-- 模态框（查找） --^>>>%html_file%
echo ^<div class^="modal fade" id^="modal_search" name^="modal_search" role^="dialog" aria-labelledby^="modal_search" aria-hidden^="true"^>>>%html_file%
echo     ^<div class^="modal-dialog"^>>>%html_file%
echo         ^<div class^="modal-content"^>>>%html_file%
echo             ^<div class^="modal-header"^>>>%html_file%
echo                 ^<button type^="button" class^="close" data-dismiss^="modal" aria-hidden^="true"^>×^</button^>>>%html_file%
echo                 ^<h4 class^="modal-title" id^="modal_search_title"^>{{ langs.modal_search_header }}^</h4^>>>%html_file%
echo             ^</div^>>>%html_file%
echo             ^<div class^="modal-body"^>>>%html_file%
echo                 ^<form class^="form-horizontal" role^="form" name^="modal_search_form" novalidate^="novalidate"^>>>%html_file%
echo                     ^<div class^="row"^>>>%html_file%
echo.>>%html_file%

if defined time_field 	echo                         ^<^!-- time --^>>>%html_file%
if defined time_field 	echo                         ^<div class^="form-group col-sm-10 col-sm-offset-1"^>>>%html_file%
if defined time_field 	echo                             ^<label class^="col-sm-4 col-sm-offset-1 control-label"^>{{ langs.%time_field% }}：^</label^>>>%html_file%
if defined time_field 	echo                             ^<div class^="col-sm-7"^>>>%html_file%
if defined time_field 	echo                                 ^<div class^="input-group"^>>>%html_file%
if defined time_field 	echo                                     ^<div class^="input-group-addon"^>>>%html_file%
if defined time_field 	echo                                         ^<i class^="fa fa-clock-o"^>^</i^>>>%html_file%
if defined time_field 	echo                                     ^</div^>>>%html_file%
if defined time_field 	echo                                     ^<input type^="text" class^="form-control pull-left" id^="search_%time_field%" name^="search_%time_field%" data-ng-change^="modal_search_title_not_submit()" data-ng-model^="search.%time_field%"^>>>%html_file%
if defined time_field 	echo                                 ^</div^>>>%html_file%
if defined time_field 	echo                             ^</div^>>>%html_file%
if defined time_field 	echo                         ^</div^>>>%html_file%
if defined time_field 	echo.>>%html_file%
if defined time_field 	echo.>>%html_file%

if defined s_fields for %%i in (%s_fields%) do (
	echo                         ^<^!-- %%i --^>>>%html_file%
	echo                         ^<div class^="form-group col-sm-10 col-sm-offset-1"^>>>%html_file%
	echo                             ^<label class^="col-sm-4 col-sm-offset-1 control-label"^>{{ langs.%%i }}：^</label^>>>%html_file%
	echo                             ^<div class^="col-sm-7"^>>>%html_file%
	echo                                 ^<input type^="text" class^="form-control" id^="search_%%i" name^="search_%%i" data-ng-model^="search.%%i" placeholder^="{{langs.like_query}}"^>>>%html_file%
	echo                             ^</div^>>>%html_file%
	echo                         ^</div^>>>%html_file%
)

echo                     ^</div^>>>%html_file%
echo                 ^</form^>>>%html_file%
echo             ^</div^>>>%html_file%
echo             ^<div class^="modal-footer"^>>>%html_file%
echo                 ^<button type^="submit" class^="btn btn-primary" data-ng-click^="modal_search_ok()"^>{{ langs.modal_search_ok }}^</button^>>>%html_file%
echo                 ^<button type^="button" class^="btn btn-default" data-dismiss^="modal"^>{{ langs.modal_close_btn }}^</button^>>>%html_file%
echo             ^</div^>>>%html_file%
echo         ^</div^>^<^!-- /.modal-content --^>>>%html_file%
echo     ^</div^>^<^!-- /.modal --^>>>%html_file%
echo ^</div^>>>%html_file%

if %need_add_mod%==y (
	echo.>>%html_file%
	echo ^<^!-- 模态框（新增/修改） --^>>>%html_file%
	echo ^<div class="modal fade" id="modal_add" name="modal_add" role="dialog" aria-labelledby="modal_add" aria-hidden="true"^>>>%html_file%
	echo     ^<div class="modal-dialog"^>>>%html_file%
	echo         ^<div class="modal-content"^>>>%html_file%
	echo             ^<div class="modal-header"^>>>%html_file%
	echo                 ^<button type="button" class="close" data-dismiss="modal" aria-hidden="true"^>^&times^;^</button^>>>%html_file%
	echo                 ^<h4 class="modal-title" id="modal_search_title"^>{{ langs[add.a] }}^</h4^>>>%html_file%
	echo             ^</div^>>>%html_file%
	echo             ^<div class="modal-body"^>>>%html_file%
	echo                 ^<form class="form-horizontal" role="form" name="modal_search_form" novalidate="novalidate"^>>>%html_file%
	echo                     ^<div class="row"^>>>%html_file%
	
	if defined fields for %%i in (%fields%) do (
		echo.>>%html_file%
		echo                         ^<^!-- %%i --^>>>%html_file%
		echo                         ^<div class="form-group col-sm-10 col-sm-offset-1"^>>>%html_file%
		echo                             ^<label class="col-sm-4 col-sm-offset-1 control-label"^>{{ langs.%%i }}：^</label^>>>%html_file%
		echo                             ^<div class="col-sm-7"^>>>%html_file%
		echo                                 ^<input type="text" class="form-control tooltip-show" id="add_%%i" name="add_%%i" data-ng-model="add.%%i" data-toggle="tooltip" title="{{langs.%%i_title}}" /^>>>%html_file%
		echo                             ^</div^>>>%html_file%
		echo                         ^</div^>>>%html_file%
		echo.>>%html_file%
	)

	echo                     ^</div^>>>%html_file%
	echo                 ^</form^>>>%html_file%
	echo             ^</div^>>>%html_file%
	echo             ^<div class="modal-footer"^>>>%html_file%
	echo                 ^<button type="submit" class="btn btn-primary" data-ng-click="modal_add_ok()"^>{{ langs.submit }}^</button^>>>%html_file%
	echo                 ^<button type="button" class="btn btn-default" data-dismiss="modal"^>{{ langs.modal_close_btn }}^</button^>>>%html_file%
	echo             ^</div^>>>%html_file%
	echo         ^</div^>^<^!-- /.modal-content --^>>>%html_file%
	echo     ^</div^>^<^!-- /.modal --^>>>%html_file%
	echo ^</div^>>>%html_file%
)


echo %html_file% is ok
echo.

::json文件生成
set json_dir=lang/zh-cn/%class%
set json_file=%json_dir%/%method%.json
IF NOT EXIST "%json_dir%" MD "%json_dir%"
echo {>%json_file%
if defined fields for %%i in (%fields%) do echo     "%%i":"%%i",>>%json_file% 
if defined time_field echo     "%time_field%":"%time_field%",>>%json_file%
echo     "test":"">>%json_file%
echo }>>%json_file%

echo %json_file% is ok
echo.

echo OK ^^_^^
echo.
pause


