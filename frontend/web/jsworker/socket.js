var socket = {
	resourse : null,
	waited : 0,

	_csrf:"",
	tableName:null,
    resourse_id:null,
    user_id:null,
    event:"update",
    registerUrl:"",
    resetUrl:"",
	
	listen : function(){
		if(!this.resourse){
			console.log("Клиент запущен!");
			this.resourse = setInterval(this.ping,2000);
		}
	},
	

	stop: function(){
		clearInterval(this.resourse);
		this.resourse = null;
	},



	beforeSend: function(){
		this.waited = true;
	},
	

	success :function(json){
		console.log("success");
		console.log(json);
		this.response.json = json;
		this.response.status = 1;
		this.response.submit();
	},
	

	error :function(msg){
		console.log("msg");
		console.log(msg);
		this.response.text = msg;
	},
	

	complete :function(){
		this.waited = false;
	},


	ping: function(){
		socket.checkAction();
	},
	
	checkAction:function(){
		if(socket.waited) return;
		
		var ajax = new XMLHttpRequest();

		var params = {
			tableName:socket.tableName,
			resourse_id:socket.resourse_id,
			user_id:socket.user_id,
			event:socket.event,
			_csrf:socket._csrf
		};
		
		// var strParams = JSON.stringify(params);

		var strParams = "table_name="+params.tableName+"&_csrf="+params._csrf+"&record_id="+params.resourse_id
						+"&user_id="+params.user_id+"&event="+params.event;
		
		var host = socket.registerUrl;

		var hostWithParams = host;

		ajax.open("POST",hostWithParams);
		
		ajax.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
		ajax.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
		//ajax.setRequestHeader('Content-type', 'application/json; charset=utf-8');

		ajax.onload = function (e) {
		    if(ajax.readyState != 4) return;

			if(ajax.status != 200){
				if(typeof socket.error == 'function'){
					socket.error(ajax.status + " : "+ajax.statusText);
				}
			}else{
				if(typeof socket.success == 'function'){
					socket.success(ajax.responseText);
				}
			}

			if(typeof socket.complete == 'function'){
				socket.complete();
			}
		};
		
		if(typeof socket.beforeSend == 'function'){
			socket.beforeSend();
		}
		
		
		ajax.send(strParams);
	},



	response :{
		text : "",
		json : {},
		status : 0,
		submit : function(){
			//отправляем данные  в основной поток 

			if(this.json.hasOwnProperty("action")){
				socket_controller.exec(this.json.action,this.json);
			}
			
		}
	},
}