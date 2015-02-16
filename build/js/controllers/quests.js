/*
** quests.js
*/

app.controller('QuestsCtrl', ['$scope', '$http', function ($scope, $http) {

	console.log('QuestsCtrl');

	$scope.quests = [
		{ name: 'Quest 1', loot: [5, 10], energy: 1 },
		{ name: 'Quest 2', loot: [5, 10], energy: 1 },
		{ name: 'Quest 3', loot: [5, 10], energy: 1 },
		{ name: 'Quest 4', loot: [5, 10], energy: 1 },
		{ name: 'Quest 5', loot: [5, 10], energy: 1 }
	];

	// $scope.getQuests = function () {
	// 	$http.get('php/api/quests').success(function (data) {
	// 		console.log(data);

	// 		//$scope.user = data.user;

	// 	}).error(function () {
	// 		console.log('An error occurred.');
	// 	});
	// };

	// $scope.getQuests();
	
}]);