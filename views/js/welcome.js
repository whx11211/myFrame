angular.module('myApp').controller('welcome', function($scope, $rootScope, $http, $filter) {
	$rootScope.api_name="welcome";
	$rootScope.set_breadcrumb("welcome");
});