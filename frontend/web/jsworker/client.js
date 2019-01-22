if(!window.Worker){ 
	worker = null;
	console.log('Browser don`t support the Worker');
	throw new Error();
}
if(worker){ throw new Error();  }
		
		

var indicator = document.createElement("div");
		
document.body.appendChild(indicator);
var s = document.createElement("span");
s.innerText = "*";
indicator.appendChild(s);

var indicate = function(){
	indicator.style.opacity = parseInt(indicator.style.opacity) == 1 ? 0 : 1;
}

var worker = new Worker("/jsworker/worker.js");
		

worker.onmessage = function (event){
	indicate();
	var data = event.data;
	if(typeof data == "object"){

	}
	console.log("Сообщение из потока воркера в основной");
	console.log(event.data);
};

		

chatState.startClient = function(params){

	if(!worker){return;}
				
	//if(typeof params == "object"){
		//chatState.fixChanges(params);
	//}
				
	worker.postMessage({action:"run",params:this.params});
}


chatState.stopClient = function(params){

	if(!worker){ return;}

	worker.postMessage({action:"stop",params:{}});
	
}

//chatState.startClient({});