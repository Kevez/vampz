/*
** routes.js
** description: Configure routes/URLs for the application
*/ 

app.config(['$routeProvider', function ($routeProvider) {
	$routeProvider
		.when('/', {
			templateUrl: 'views/stats.html',
			controller: 'StatsCtrl'
		})
		.when('/quests', {
			templateUrl: 'views/quests.html',
			controller: 'QuestsCtrl'
		})
		.when('/fight', {
			templateUrl: 'views/fight.html',
			controller: 'FightCtrl'
		})
		.when('/skills', {
			templateUrl: 'views/skills.html',
			controller: 'SkillsCtrl'
		})
		// .when('/post/:id', {
		// 	templateUrl: 'views/post.html',
		// 	controller: 'PostCtrl'
		// })
		.when('/error', {
			templateUrl: 'views/error.html',
			controller: 'ErrorCtrl'
		})
		.otherwise({
			redirectTo: '/error'
		})
}]);