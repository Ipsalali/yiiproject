var Monitoring = function(options = {}){
				
				this.intervalUpdate = options.intervalUpdate;
				
				this.interval = options.interval ? options.interval : 10000;
				
				this.callback = options.callback;

				this.pageUrl = options.callback;
				
				this.initIntervalUpdate = function(callback){
					if(typeof callback == "function") this.callback = callback;

					if(typeof this.callback == "function")
						this.intervalUpdate = setInterval(this.callback,this.interval);
				};

				this.destroyIntervalUpdate = function(){
					clearInterval(this.intervalUpdate);
				};

			}
var MC = new Monitoring();

$(function(){

				$("#toggle_filtres").click(function(){
					var status = parseInt($(this).attr("data-status")) ? 0 : 1;
					$(this).attr("data-status",status);
					$(".monitor .grid-view table .filters").toggle(100);
				})
				
				var update_table = function(href,pagination){

					var dataFilters = [];
					$(".monitor .filters").find("input,select").each(function(){
						if($(this).val()){
							var param = $(this).attr("name") + "=" + $(this).val();
							dataFilters.push(param);
						}
					});

					if(!pagination && $(".pagination li.active a").length){
						var pag_data = get_pagination_data_from_link($(".pagination li.active a").attr("href"));
						href += pag_data ? "&"+pag_data : "";
					}

					
					$.ajax({
						url:href,
						type:"GET",
						dataType:"json",
						data:dataFilters.join("&"),
						beforeSend:function(){},
						success:function(json){

							if(json.hasOwnProperty("view")){
								
								
								$("#monitor_target").html(json.view);
								
								jQuery('#w0').yiiGridView({"filterUrl":MC.pageUrl,"filterSelector":"#w0-filters input, #w0-filters select"});
								
								if(typeof ALDT == "object" && typeof ALDT.update == "function"){
									ALDT.update();
								}

								if(!parseInt($("#toggle_filtres").attr("data-status"))){
									$(".monitor .filters").hide();
								}
							}

							if(json.hasOwnProperty("date")){
								$("#last_update_time").html(json.date);
							}

						},
						error:function(msg){ console.log(msg);},
						complete:function(){},
					});
				}



				$("#btn_refresh").click(function(event){
					event.preventDefault();

					var href = $(this).attr("href");
					update_table(href,true);
					
				});


				var get_pagination_data_from_link = function(p_link){

					if(!p_link) return false;

					var href = p_link;
					var parts = href.split("?");
					var url = "";
					if(parts.length > 1){
						var data = parts[1];
						
						var page = data.match(/page=\d*/);
						if(page && page.length){
							url += "&"+page[0];
						}
						var per_page = data.match(/per-page=\d*/);
						if(per_page && per_page.length){
							url += "&"+per_page[0];
						}
					}

					return url;

				}

				$("#monitor_target").on("click"," .pagination a",function(event){

					event.preventDefault();

					var href = $(this).attr("href");
					var url = $("#btn_refresh").attr("href");
					var pag_data = get_pagination_data_from_link(href);

					url += pag_data ? "&"+pag_data : ""; 
					update_table(url,true);
				})


				
				
				
				

				$("#monitoring_options").change(function(){
					var sec = parseInt($(this).val());
					MC.destroyIntervalUpdate();
					if(sec){
						MC.interval = sec;
						MC.initIntervalUpdate(function(){
							var href = $("#btn_refresh").attr("href");
							update_table(href);
						});

						window.sessionStorage.setItem("m_interval",sec);
					
					}else{
						window.sessionStorage.removeItem("m_interval");
					}
				})

				var monitoring_enable = window.sessionStorage.getItem("m_interval");

				if(monitoring_enable){
					$("#monitoring_options").val(monitoring_enable);
					$("#monitoring_options").trigger("change");
				}

})