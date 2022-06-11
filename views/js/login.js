app.controller('System/login', function($scope, $rootScope, $http) {
	$scope.api_name = 'System/login';
    $scope.search = {};
    
	$rootScope.langs = {};
	$http.get(lang('main')).then(
    	function (respone) {
    		$rootScope.langs = respone.data;
    	}
    );
    
    $scope.login = function () {
        if (typeof($scope.search.loginname) == 'undefined') {
            $scope.search.loginname = $(':input[name=userName]').val();
        }
        if (typeof($scope.search.pwd) == 'undefined') {
            $scope.search.pwd = $(':input[name=password]').val();
        }
    	$http.post(api($scope.api_name), $scope.search).then(function (respone) {
    		if (respone.data.r) {
    			$rootScope.longin_data = respone.data;
    			window.location.href = './';
    		}
    		else {
    			$('#logintip').addClass('text-danger');
    			$scope.tip = $rootScope.langs['error_'+respone.data.error];
    		}
    	});
    }
    
    // 响应登录回车事件
    $scope.loginKeyup = function(e){
        var keycode = window.event?e.keyCode:e.which;
        if (keycode==13) {
            $scope.login();
        }
    };
});