/*
** quests.js
*/

app.controller('QuestsCtrl', ['$scope', '$rootScope', '$http', function ($scope, $rootScope, $http) {

	$scope.quest = null;
	$scope.quests = null;
	$scope.questCompleted = false;
	$scope.loading = false;

	$scope.getQuests = function () {
		$http.get('php/api/quests').success(function (data) {
			console.log(data);
			$scope.quests = data;
		});
	};

	$scope.do = function (id) {
		console.log('doQuest', id);

		$scope.questCompleted = false;
		$scope.loading = true;

		$http.get('php/api/quests/do.php').success(function (data) {
		
			$scope.quest = data.quest; 
			$scope.questCompleted = true;
			$scope.loading = false;

			$rootScope.$emit('statsUpdated', data.user);

			// shift the viewport to the top
			document.body.scrollTop = document.documentElement.scrollTop = 0;
		});

	};

	$scope.getQuests();
	
}]);