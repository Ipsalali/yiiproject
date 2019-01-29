var socket_controller = {
	default:function(){
		console.log("default action");
	},
	
	runAction:function(params){
		// console.log(params);
		// console.log("runAction");
		wSocket.tableName = params.hasOwnProperty("tableName") ? params.tableName : "";
		wSocket.resourse_id = params.hasOwnProperty("resourse_id") ? params.resourse_id : 0;
		wSocket.user_id = params.hasOwnProperty("user_id") ? params.user_id : 0;
		wSocket.event = params.hasOwnProperty("event") ? params.event : "";
		wSocket.registerUrl = params.hasOwnProperty("registerUrl") ? params.registerUrl : "";
		wSocket._csrf = params.hasOwnProperty("_csrf") ? params._csrf : "";
		wSocket.listen();
	},

	stopAction:function(params){
		wSocket.stop();
	},

	changeAction:function(params){
		wSocket.setChanges(params);
	},




	commitchangesAction:function(json){

		if(typeof json == "object" && json.hasOwnProperty("params")){
			
			wSocket.setChanges(json.params);
			
		}
		postMessage(json);
	},

	exec : function(action,params){

		if(!wSocket) return;

		var method = action+"Action";
		if(socket_controller.hasOwnProperty(method) && typeof socket_controller[method] == "function"){
			return socket_controller[method](params);
		}else{
			return socket_controller.default();
		}

	},
}