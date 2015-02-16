/*
** header.js
*/

app.controller('HeaderCtrl', ['$scope', '$http', function ($scope, $http) {

	$scope.user = [];

	$scope.getStats = function () {
		$http.get('php/api/stats').success(function (data) {
			console.log(data);

			$scope.user = data.user;

		}).error(function () {
			console.log('An error occurred.');
		});
	};

	$scope.getStats();

}]);