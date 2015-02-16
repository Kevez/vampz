/*
** header.js
*/

app.controller('HeaderCtrl', ['$scope', '$rootScope', '$http', function ($scope, $rootScope, $http) {

	$scope.user = {};

	$scope.getStats = function () {
		console.log('getStats');
		$http.get('php/api/stats').success(function (data) {
			$scope.user = data.user;
		});
	};

	$scope.getStats();

	// Listen for stat changes
	$rootScope.$on('statsUpdated', function (evt, data) { 
    $scope.user = data;
  });

}]);