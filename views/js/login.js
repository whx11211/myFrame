app.controller('System/login', function($scope, $rootScope, $http) {
	$scope.api_name = 'System/login';
    $scope.serach = {};
    
	$rootScope.langs = {};
	$http.post(lang('main')).then(
    	function (respone) {
    		$rootScope.langs = respone.data;
    	}
    );
    
    $scope.login = function () {
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