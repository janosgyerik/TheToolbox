function initNotificator() {
	return {
		views: [
			{header:"Notificator", id:'menu-header-notificator', views: [
				{title:"Notifications", id:'menu-notificator-list', "class": "NotificatorListView", "script": "notificator.js"}
			]}
		]
	};
}