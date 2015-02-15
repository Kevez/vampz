/*
** stats.js
*/

app.controller('StatsCtrl', ['$scope', '$http', function ($scope, $http) {
	console.log('StatsCtrl');

	$scope.level = null;

	$scope.getStats = function () {
		$http.get('php/api/stats').success(function (user) {
			console.log(user);

			$scope.level = user.level

		}).error(function () {
			console.log('An error occurred.');
		});
	};

	$scope.getStats();
	
}]);