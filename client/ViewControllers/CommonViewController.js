var ajaxLoadStack = [];

$(function(){
	//if we don't have a cookie yet, create a default one
	//if(GetCookie() == null)
	//	SetCookie({});


	$(window).on("loadscripts", function(){
		var width = 0; 
		$('.navlinks a').each(function(i, ele){
			var textW = $(ele).width();
			var marginW = $(ele).css("margin-right").replace("%", "").replace("px", "");
			width += textW + ($('body').width() * (marginW/100.0));
		});
		
		console.log("total: "+width);
		$('.center-main').not('.navlinks').width(width);
	});

	AutoloadTemplates(); 
	var id = GetUrlParam('id');
	AutoloadDataTemplates(id);
});

function PushPromise()
{
	ajaxLoadStack.push(ajaxLoadStack.length);
}
function PopPromise()
{
	if(ajaxLoadStack.length > 0)
		ajaxLoadStack.pop();
	if(ajaxLoadStack.length <= 0)
		$(window).trigger("loadscripts");
}

function AutoloadTemplates()
{
	var elements = $(".template.autoload");
	$(elements).each(function(idx, ele){
		PushPromise();
		$.get(GetLocalUrl("Templates/"+$(ele).attr("rel")+".html"), function(data) {
			$(ele).replaceWith(data);
			PopPromise();
		});
	});
}

function AutoloadDataTemplates(id)
{
	var elements = $(".data-template.autoload");
	$(elements).each(function(idx, container){
		LoadDataTemplate(container, id)
	});
}

function LoadDataTemplate(container, id)
{
	var authId = localStorage.getItem("id");
	var tok = sessionStorage.getItem("tolkien");
	
	var dataType = $(container).attr("rel");
	var dataEndpoint = dataType.charAt(0).toUpperCase() + dataType.substr(1).toLowerCase();
	var postData = {"func":"get"};
	if(authId && tok)
	{
		postData["authId"] = authId;
		postData["auth"] = tok;
	}
	if(id) postData["id"] = id;
	
	var templateEndpoint = GetLocalUrl("Templates/"+dataType+(id?"":"s")+".html");
	
	Ajax(dataEndpoint, postData, function(data){
	$.get(templateEndpoint, function(template) {
		if(id)
		{
			var p = data[0];
			template = ParseObjectIntoTemplate(p, template);
			$(container).append(template);
		}
		else
		{
			$(data).each(function(idx, p){
				var inner = template;
				inner = ParseObjectIntoTemplate(p, inner);
				$(container).append(inner);
			});
		}
		if(ajaxLoadStack.length)
			PopPromise();
	});});
}

function ParseObjectIntoTemplate(data, template)
{
	for (var property in data) {
		if (data.hasOwnProperty(property)) {
			var reg = new RegExp("\\["+property+"\\]", "g");
			template = template.replace(reg, data[property]);
		}
	}
	
	return template;
}

function Ajax(object, dataSet, callback, errorSelector)
{

	$.ajax({
	  url: GetLocalUrl("DataControllers/"+object+"DataController.php"),
	  method: "POST",
	  data: dataSet,
	  error: function(xhr, options, error){
	  	if(xhr.status == 404)
	  	{
	  		window.location.href = GetLocalUrl("404.html");
	  	}
	  	else if(xhr.status == 403)
	  	{
	  		console.log("403 forbidden. Need to redirect to 403 page.");
	  		//TODO: make 403 page and redirect
	  	}
	  	else if(xhr.status == 400)
	  	{
	  		ShowError("Error: "+error, errorSelector);
	  	}
	  	else
	  	{
	  		ShowError("Something went wrong in processing this page. Please try again later. If this problem persists, please contact us.");
	  	}
	  }
	}).done(function(data){
		//console.log(data);
		if(!data || data.length <= 0)
		{
			callback(false);
			return;
		}
		var result = JSON.parse(data);
		if(result.hasOwnProperty("data")) callback(result.data);
		else callback(result);
	});
}

function ShowError(message, selector)
{
	$('html, body').animate({scrollTop: '0px'}, 300);
	$(".error-message").text(message);
	$(".error-message").slideDown("slow");
	$(selector).addClass("error-red");
}

function RemoveError()
{
	$(".error-message").empty();
	$(".error-message").hide();
	$(".error-red").removeClass("error-red");
}
	
function GetUrlParam(name){
    var url = window.location.href;    
    var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)");
    var results = regex.exec(url);
    
    if (!results) return null;
    if (!results[2]) return '';
    return decodeURIComponent(results[2].replace(/\+/g, " "));
}

function GetLocalUrl(path)
{
	return "http://"+window.location.hostname+":8888/richard-web/"+path;
}	

function GetCookie()
{
	var name = "bucketofsnow=";
    var ca = document.cookie.split(';');
    var result = "";
    for(var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            result = c.substring(name.length, c.length);
        }
    }
    return (result.length > 0)? JSON.parse(result) : null;
}

function SetCookie(value)
{
	var cvalue = JSON.stringify(value);
	var expirationDays = 7;
	var name = "bucketofsnow";
	var d = new Date();
    d.setTime(d.getTime() + (expirationDays*24*60*60*1000));
    var expires = "expires="+d.toUTCString();
    document.cookie = name + "=" + cvalue + "; " + expires;
}

function DestroyCookie()
{
	document.cookie = "bucketofsnow=; expires=Thu, 01 Jan 1970 00:00:00 UTC";
}

