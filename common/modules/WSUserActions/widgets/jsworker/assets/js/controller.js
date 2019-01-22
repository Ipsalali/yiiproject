var socket_controller = {
	default:function(){
		console.log("default action");
	},
	
	runAction:function(params){
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