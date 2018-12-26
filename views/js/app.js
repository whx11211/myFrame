var app = angular.module('myApp', [
   'ngRoute',
   'oc.lazyLoad',
   'ui.grid', 'ui.grid.selection', 'ui.grid.edit', 'ui.grid.exporter', 'ui.grid.pagination', 'ui.grid.resizeColumns', 'ui.grid.autoResize', 'ui.grid.moveColumns', 'ui.grid.pinning',
    'ngTouch'
   ]);

app.config(function($httpProvider) {
	$httpProvider.defaults.headers.put['Content-Type'] = 'application/x-www-form-urlencoded';
	$httpProvider.defaults.headers.post['Content-Type'] = 'application/x-www-form-urlencoded';    // Override $http service's default transformRequest
    $httpProvider.defaults.transformRequest = [function(data) {
        /**
         * The workhorse; converts an object to x-www-form-urlencoded serialization.
         * @param {Object} obj
         * @return {String}
         */
        var param = function(obj) {
            var query = '';
            var name, value, fullSubName, subName, subValue, innerObj, i;

            for (name in obj) {
                value = obj[name];

                if (value instanceof Array) {
                    for (i = 0; i < value.length; ++i) {
                        subValue = value[i];
                        fullSubName = name + '[' + i + ']';
                        innerObj = {};
                        innerObj[fullSubName] = subValue;
                        query += param(innerObj) + '&';
                    }
                } else if (value instanceof Object) {
                    for (subName in value) {
                        subValue = value[subName];
                        fullSubName = name + '[' + subName + ']';
                        innerObj = {};
                        innerObj[fullSubName] = subValue;
                        query += param(innerObj) + '&';
                    }
                } else if (value !== undefined && value !== null) {
                    query += encodeURIComponent(name) + '='
                            + encodeURIComponent(value) + '&';
                }
            }

            return query.length ? query.substr(0, query.length - 1) : query;
        };

        return angular.isObject(data) && String(data) !== '[object File]'
                ? param(data)
                : data;
    }];
});

//ng-repeate完成后扩展指令
//<div ng-repeat="i in arr" repeat-finish="repeatDone();">
app.directive('repeatFinish',function(){
 return {
     link: function(scope,element,attr){
         //console_log('ng-repeat(index)', scope.$index);
         if(scope.$last == true){
             scope.$eval( attr.repeatFinish );
         }
     }
 }
});

app.directive('touchSwipe',['$swipe',function($swipe){
//横向滑动事件
    return {
        restrict:'EA',
        link:function(scope,ele,attr,ctrl){
            var startX,startY,locked=false;
            $swipe.bind(ele,{
                'start':function(coords){
                    startX = coords.x;
                    startY = coords.y;
                },
                'move':function(coords){
                },
                'end':function(coords){

                    var deltaX = coords.x - startX;
                    if (Math.abs(deltaX) > 100) {
                        var method = attr.touchSwipe;
                        scope.$apply(method.replace('param_x', deltaX));
                    }
                },
                'cancel':function(coords){
                }
            });
        }
    }
}
]).directive('touchLong',['$swipe',function($swipe){
//长按事件
    return {
        restrict:'EA',
        link:function(scope,ele,attr,ctrl){
            var locked=false,startTime;
            $swipe.bind(ele,{
                'start':function(coords){
                    startTime = Date.parse(new Date());
                    locked = false;
                },
                'move':function(coords){
                },
                'end':function(coords){
                    var deltaTime = Date.parse(new Date()) - startTime;
                    if (deltaTime > 500) {
                        var method = attr.touchLong;
                        scope.$apply(method.replace('param_t', deltaTime));
                    }
                },
                'cancel':function(coords){
                }
            });
        }
    }
}
]).directive('touchDouble',['$swipe',function($swipe){
//双击事件
    return {
        restrict:'EA',
        link:function(scope,ele,attr,ctrl){
            var locked=false,lastTime=0;
            $swipe.bind(ele,{
                'start':function(coords){
                },
                'move':function(coords){
                },
                'end':function(coords){
                    var nowTime = Date.parse(new Date());
                    var deltaTime = nowTime-lastTime;
                    if (lastTime && deltaTime<800) {
                        lastTime = 0;
                        var method = attr.touchDouble;
                        scope.$apply(method.replace('param_t', deltaTime));
                    }
                    else {
                        lastTime = nowTime;
                    }
                },
                'cancel':function(coords){
                }
            });
        }
    }
}
]);

//用法 {{ key | get_name_by_id:conf:key_name:val_name}}
app.filter('get_name_by_id', function(){
    return function(key, conf, key_name, val_name) {
        if (typeof(key_name) == 'undefined') {
            if (typeof(val_name) == 'undefined') {
                return conf[key];
            }
            else {
                return conf[key][val_name];
            }
        }
        else if (typeof(val_name) != 'undefined') {
            for (var i in conf) {
                if (conf[i][key_name] == key) {
                    return conf[i][val_name];
                }
            }
        }

        return '';
    };
});
app.filter('date2', function() { //可以注入依赖
    return function(text, format) {
    	if (text == 0) {
    		return '';
    	}
    	newDate = new Date(text*1000);
        var date = {
                "M+": newDate.getMonth() + 1,
                "d+": newDate.getDate(),
                "H+": newDate.getHours(),
                "m+": newDate.getMinutes(),
                "s+": newDate.getSeconds(),
                "q+": Math.floor((newDate.getMonth() + 3) / 3),
                "S+": newDate.getMilliseconds()
         };
         if (/(y+)/i.test(format)) {
                format = format.replace(RegExp.$1, (newDate.getFullYear() + '').substr(4 - RegExp.$1.length));
         }
         for (var k in date) {
                if (new RegExp("(" + k + ")").test(format)) {
                       format = format.replace(RegExp.$1, RegExp.$1.length == 1
                              ? date[k] : ("00" + date[k]).substr(("" + date[k]).length));
                }
         }
        return format;
    }
});

app.filter('minute_format', function() { //可以注入依赖
    return function(text) {
        if (typeof(text) == 'undefined') {
            return '--:--';
        }
        var a = text.toString().split('.');
        var min = a[0] > 9 ? a[0] : ('0' + a[0]);
        var s = Math.round((text - a[0])*60);
        s = s >9 ? s : ('0'+s);
        return min + ':' + s;
    }
});

//ui-grid鼠标下滚失效解决方案1
(function () {
  'use strict';
  angular.module('myApp')
    .config(function ($provide) {
      $provide.decorator('Grid', function ($delegate,$timeout) {
        $delegate.prototype.renderingComplete = function(){
          if (angular.isFunction(this.options.onRegisterApi)) {
            this.options.onRegisterApi(this.api);
          }
          this.api.core.raise.renderingComplete( this.api );
          $timeout(function () {
           var $viewport =  $('.ui-grid-render-container');
            ['touchstart', 'touchmove', 'touchend','keydown', 'wheel', 'mousewheel', 'DomMouseScroll', 'MozMousePixelScroll'].forEach(function (eventName) {
              $viewport.unbind(eventName);
            });
          }.bind(this));
        };
        return $delegate;
      });
    });

})();