/*
** stats.js
*/

app.factory('UserFactory', function () {

	var user = {};

	return {
		getUser: function () {
			return user;
		},
		setUser: function (obj) {
			console.log(obj);
			user = obj
		}
	}
});

app.controller('StatsCtrl', ['$scope', '$http', 'UserFactory', function ($scope, $http, UserFactory) {
	console.log('StatsCtrl', UserFactory);	
}]);