/*
** fight.js
*/

app.controller('FightCtrl', ['$scope', '$rootScope', '$http', function ($scope, $rootScope, $http) {

	$scope.summary = null;
	$scope.players = null;
	$scope.fightCompleted = false;
	$scope.loading = false;

	$scope.getPlayers = function () {
		$http.get('php/api/fight').success(function (data) {
			console.log(data);
			$scope.players = data;
		});
	};

	$scope.do = function (id) {
		console.log('fight', id);

		$scope.fightCompleted = false;
		$scope.loading = true;

		$http.get('php/api/fight/do.php').success(function (data) {
		
			$scope.summary = data.summary; 
			$scope.fightCompleted = true;
			$scope.loading = false;

			$rootScope.$emit('statsUpdated', data.user);

			// shift the viewport to the top
			document.body.scrollTop = document.documentElement.scrollTop = 0;
		});

	};

	$scope.getPlayers();
	
}]);