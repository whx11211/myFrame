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
                     field: 'path',
                     displayName: $scope.langs.path,
                     minWidth: "300",
                     visible:false,
                     cellTemplate: '<div class="ui-grid-cell-contents ng-binding ng-scope"  style="white-space:normal;word-break: break-all;">{{row.entity.path}}</div>'
                 },
                 {
                	 field: 'file_name',
                     displayName: $scope.langs.file_name,
                     minWidth: "200",
                     cellTemplate: '<div class="ui-grid-cell-contents ng-binding ng-scope"  style="white-space:normal">{{row.entity.file_name}}</div>'
                 },
                 {
                     field: 'preview_image',
                     displayName: $scope.langs.preview_image,
                     enableSorting: false,
                     minWidth: "170",
                     cellTooltip: '111111',
                     cellTemplate: '<div class="ui-grid-cell-contents" '
                     //+ ' data-ng-click="grid.appScope.show_image(row.entity.preview_image_path)"'
                     //+ ' data-toggle="tooltip" data-placement="auto" data-html="true" '
                     //+ ' title="<img style=\'max-width:600px;max-height:500px;\' src=\'images/ffmpeg/{{row.entity.id}}.png\'//>" '
                     + '><img data-ng-src="{{row.entity.preview_image_path}}" style="max-height: 100%;max-width:100%;"/></div>'
                 },
                 {
                     field: 'description',
                     displayName: $scope.langs.description,
                     minWidth: "240",
                     visible:false,
                     cellTemplate: '<div class="ui-grid-cell-contents ng-binding ng-scope"  style="white-space:normal">{{row.entity.description}}</div>'
                 },
                 {
                    field: 'file_size',
                    displayName: $scope.langs.file_size,
                    minWidth: "60",
                    visible:true,
                    cellTemplate: '<div class="ui-grid-cell-contents ng-binding ng-scope"  style="white-space:normal;word-break: break-all;">{{row.entity.file_size}}&nbsp;MB</div>'
                 },
                 {
                    field: 'duration',
                    displayName: $scope.langs.duration,
                    minWidth: "60",
                    visible:true,
                    cellTemplate: '<div class="ui-grid-cell-contents ng-binding ng-scope"  style="white-space:normal;word-break: break-all;">{{row.entity.duration | minute_format}}</div>'
                 },
                 $rootScope.ui_grid.get('create_time', false, 130),
                 $rootScope.ui_grid.get('last_mod_time', false, 130),
                 $rootScope.ui_grid.get('last_view_time', false, 130),
                 $rootScope.ui_grid.get('view_count', false, 50)
            ];
            $scope.gridOptions.columnDefs.push({
                field: 'operation',
                displayName: $scope.langs.operation,
                enableSorting: false,
                minWidth: 100,
                cellTemplate: '<div class="ui-grid-cell-contents ng-binding ng-scope">'
                             + '<button data-ng-click="grid.appScope.modal_play(row.entity, 2)" class="btn btn-xs btn-primary btn-oper"  title="{{grid.appScope.langs.local_play}}"><i class="fa fa-television"></i></button>'
                + '<button data-ng-click="grid.appScope.modal_play(row.entity, 1)" class="btn btn-xs btn-success btn-oper"  title="{{grid.appScope.langs.html_play}}"><i class="glyphicon glyphicon-eye-open"></i></button>'
                + '<button data-ng-if="grid.appScope.user.is_local" data-ng-click="grid.appScope.open_dir(row.entity)" class="btn btn-xs btn-info btn-oper"  title="{{grid.appScope.langs.open_dir}}"><i class="fa fa-folder-open-o"></i></button>'
                + '<button data-ng-click="grid.appScope.modal_add(\'mod\',row.entity)" class="btn btn-xs btn-warning btn-oper"  title="{{grid.appScope.langs.mod}}"><i class="fa fa-edit"></i></button>'
                			 + '<button data-ng-click="grid.appScope.modal_del(row.entity)" class="btn btn-xs btn-danger btn-oper"  title="{{grid.appScope.langs.del}}"><i class="fa fa-times"></i></button>'
                			 + '</div><a></a>'
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

    $scope.get_tags_conf = function() {
        $http.post(api($scope.api_name), {a: 'getTagsConf'}).then(
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
    			for (var i in $scope.data.items) {
                    $scope.data.items[i].preview_image_path = 'images/ffmpeg/'+$scope.data.items[i].id+'.png';
                    $scope.data.items[i].tags = $scope.data.items[i].tags.length>0 ? $scope.data.items[i].tags.split(',') : [];
                }
    			console_log($scope.data, '视频数据');
    			// 显示数据绑定
                $scope.ui_grid_style.height = parseInt($scope.data.items.length)*$scope.gridOptions.rowHeight + 30 + 'px';
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

            $('#search_tags_select2').select2();

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

    $scope.modal_play_obj = {
        play_video_html_sel:document.getElementById('modal_video'),
        is_playing:false,
        is_bind:false,
        is_paused:function() {
            return this.play_video_html_sel.paused;
        },
        toggle:function() {
            if (this.is_paused()) {
                this.play_video_html_sel.play();
            }
            else {
                this.play_video_html_sel.pause();
            }
            this.is_playing = !this.is_paused();
        },
        start:function() {
            this.play_video_html_sel.play();
            this.is_playing = !this.is_paused();
        },
        pause:function() {
            this.play_video_html_sel.pause();
            this.is_playing = !this.is_paused();
        },
        jump:function(s) {
            this.play_video_html_sel.currentTime += s;
        },
        full_screen:function() {
            var ele = this.play_video_html_sel;
            if (ele.requestFullscreen) {
                ele.requestFullscreen();
            } else if (ele.mozRequestFullScreen) {
                ele.mozRequestFullScreen();
            } else if (ele.webkitRequestFullScreen) {
                ele.webkitRequestFullScreen();
            }
        },
        swipe:function(x) {
            var s = parseInt(x/10);
            this.jump(s);
        },
        progress_jump:function() {
            var e = window.event;
            var total_progress_width = $('#modal_progress').width();
            var current_progress_width = e.layerX;
            var current_time = this.play_video_html_sel.duration / total_progress_width * current_progress_width;
            this.play_video_html_sel.currentTime = current_time;
        },
        update_progress:function() {
            $scope.play.played_time = this.currentTime/60;
            $scope.play.progress = this.currentTime/this.duration*100;
            if (this.buffered.length>0) {
                var load = this.buffered.end(this.buffered.length-1);
                $scope.play.load_progress = (load - this.currentTime)/this.duration*100;
            }
            $scope.$apply();
        },
        volume_toggle:function() {
            if (this.play_video_html_sel.muted) {
                this.play_video_html_sel.muted = false;
                $('#model_video_volume_switch').removeClass('glyphicon-volume-off').addClass('glyphicon-volume-up');
            }
            else {
                this.play_video_html_sel.muted = true;
                $('#model_video_volume_switch').removeClass('glyphicon-volume-up').addClass('glyphicon-volume-off');
            }
        },
        volume_jump:function() {
            var e = window.event;
            var total_progress_width = $('#modal_volume_progress').width();
            var current_progress_width = e.layerX;
            var volume = current_progress_width / total_progress_width;
            this.play_video_html_sel.volume = volume;
        },
        init:function() {
            $scope.play.played_time = 0;
            $scope.play.progress = 0;
            $scope.play.load_progress = 0;
            $scope.play.duration2 = $scope.play.duration;
            //$('#modal_video_loading').show();
            //this.play_video_html_sel.poster = 'images/ffmpeg/' + $scope.play.id + '.png';
            if (!this.is_bind) {
                this.play_video_html_sel.addEventListener('timeupdate',this.update_progress);
                this.play_video_html_sel.addEventListener('progress',this.update_progress);
                this.play_video_html_sel.addEventListener('loadedmetadata',function(){
                });
                this.play_video_html_sel.addEventListener('error',function(){
                    //$('#modal_video_loading').hide();
                    $rootScope.show_error($scope.langs.not_support);
                    $scope.$apply();
                });
                this.play_video_html_sel.addEventListener('ended',function(){
                    $scope.modal_play_obj.is_playing = false;
                    $scope.$apply();
                });
                this.play_video_html_sel.addEventListener('canplay',function(){
                    //$('#modal_video_loading').hide();
                });
                this.play_video_html_sel.addEventListener('volumechange',function(){
                    if (!this.muted) {
                        $('#modal_volume_progress_bar').css('width', this.volume*100+'%');
                    }
                    else {
                        $('#modal_volume_progress_bar').css('width', '0%');
                    }
                });
                this.is_bind = true;
            }
        }
    };
    //播放模态框
    $('#modal_play').on("hide.bs.modal", function(){
        $scope.modal_play_obj.pause();
    });
    $('#modal_play').on("shown.bs.modal", function(){
    });
    $scope.play = {};
    $scope.play_type = 1;
    $scope.modal_max_height = parseInt(window.innerHeight - 190);
    $scope.modal_max_height_reset = function(){
        $scope.modal_max_height = parseInt(window.innerWidth - 190);
    }
    window.onorientationchange = $scope.modal_max_height_reset;
    $scope.modal_play = function (obj, type) {
        angular.extend($scope.play, obj);
        $scope.play_type = type;
    	$http.post(api($scope.api_name), angular.extend({a:'play'}, obj)).then(function (respone) {
    		if (respone.data.r) {
    		    console_log(respone.data, '播放信息');
    		    if ($scope.play_type==1) {
    		        if ($scope.video_url != respone.data.data.url_path) {
                        $scope.modal_play_obj.init();
                    }
                    $scope.video_url = respone.data.data.url_path;
                    //var url = 'video.html?video=' + encodeURIComponent($scope.video_url) + '&title=' + encodeURIComponent($scope.play.file_name) + '&poster=' + encodeURIComponent($scope.play.preview_image_path);
                    //window.open(url);
                     $('#modal_play').modal({
                         backdrop: "static",//点击空白处不关闭对话框
                         show: true
                     });
                }
                else if($scope.play_type==2) {
    		        if (typeof(respone.data.data.vlc_play) == 'undefined') {
                        $rootScope.show_error($scope.langs.play_type_2_error);
                    }
                    else {
                        window.location.href=respone.data.data.vlc_play;
                    }
                }
    		}
    		else {
    			$rootScope.show_error(respone.data);
    		}
    	});
    }

    //添加/修改模态框唤起
    $('#modal_add').on("shown.bs.modal", function(){
        $('#add_tags_select2').select2({
            //tags:true
        });
    });
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
        $scope.add_ext_load();
        $('#modal_add').modal('show');
    }
    $scope.add_ext_load = function() {
        if (typeof($scope.add_ext_loaded) == 'undefined') {

            $('#add_tags_select2').select2().on('select2:select', function(e){
                var new_tag_id = e.params.data.id;
                var new_tag=[];
                for (var i in $scope.video_tags_conf) {
                    if ($scope.video_tags_conf[i].tag_id == new_tag_id) {
                        new_tag = $scope.video_tags_conf[i].parent_ids;
                    }
                }

                if (new_tag.length>0) {
                    for (var i in new_tag) {
                        if ($scope.add.tags.indexOf(new_tag[i]) == -1) {
                            $scope.add.tags.push(new_tag[i]);
                        }
                    }
                }
                $(this).val($scope.add.tags).trigger('change');
            }).on('select2:unselect', function(e){

            });

            $scope.add_ext_loaded = true;
        }
    }

    //添加/修改数据提交
    $scope.modal_add_ok = function () {
        $('#modal_add').modal('hide');
        $http.post(api($scope.api_name), $scope.add).then(function (respone) {
            if (respone.data.r) {
                $rootScope.show_success($scope.add.a, $scope.get_data);
                if (respone.data.data.has_new_tag) {
                    $scope.get_tags_conf();
                }
            }
            else {
                $rootScope.show_error(respone.data, $scope.modal_add);
            }
        });
    }

    //新增tag
    $scope.modal_add_tag_add = function () {
        var tag = $scope.add_tag;
        $http.post(api('Video/tag'), {a:'add',getTagConf:1,tag_name:tag}).then(function (respone) {
            if (respone.data.r) {
                $scope.video_tags_conf = respone.data.data.new_conf;
                $scope.add.tags.push(respone.data.data.new_tag_id);

                var new_tag = [];
                for (var i in $scope.video_tags_conf) {
                    if ($scope.video_tags_conf[i].tag_id == respone.data.data.new_tag_id) {
                        new_tag = $scope.video_tags_conf[i].parent_ids;
                    }
                }
                if (new_tag.length>0) {
                    for (var i in new_tag) {
                        if ($scope.add.tags.indexOf(new_tag[i]) == -1) {
                            $scope.add.tags.push(new_tag[i]);
                        }
                    }
                }
                console.log($scope.add.tags);
                $scope.add_tag = '';
            }
            else {
                $rootScope.show_error(respone.data);
            }
        });
    }

    $scope.show_image = function(url) {
        $scope.image_url = url;
        $('#modal_image').modal('show');
    }

    $scope.open_dir = function(obj) {
        window.location.href= 'webbin://opendir/?path=' + encodeURIComponent(obj.path + '\\' + obj.file_name);
    }
});