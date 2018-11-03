var ajaxLoadStack = [];
var defaultPageSize = 10;

$(function(){
	$(window).resize(setDimensions);

	AutoloadTemplates(); 
	var id = GetUrlParam('id');
	AutoloadDataTemplates(id);
	
	var url = window.location.pathname;
	var filename = url.substring(url.lastIndexOf('/')+1);
	$(window).on("loadscripts", function(){
		setDimensions();
		loadImages();
	
		$('.navlinks a[href="'+filename+'"]').addClass('current');
		if(filename.length <= 0)
			$('.navlinks a[href="index.html"]').addClass('current');
		$('.copyright span').text((new Date()).getFullYear());
		FormatDates($('[data-format="date"]'));
		FormatDates($('[data-format="datetime"]'), true);
	});
	
	$('.load-more').click(function(){
		var pageNum = parseInt($(this).prev().attr("data-page")) + 1;
		$(this).prev().attr("data-page", pageNum);
		LoadDataTemplate($(this).prev());
	})
});

function setDimensions()
{
	var width = 0; 
	$('.navlinks a').each(function(i, ele){
		var textW = $(ele).width();
		var marginW = parseFloat($(ele).css("margin-right").replace("%", "").replace("px", ""));
		var marginL = parseFloat($(ele).css("margin-left").replace("%", "").replace("px", ""));
		width += textW + ($('body').width() * ((marginW + marginL)/100.0));
	});
	
	$('.center-main').not('.navlinks').outerWidth(width + 15);
	
	$('.ratio').each(function(i, ele){
		var ratio = parseFloat($(ele).attr("rel"));

		if(!isNaN(ratio)){
			$(ele).height($(ele).width() * ratio);
		}
	});

}

function loadImages()
{
	var images = new Array()
	var colorImages = new Array();
	for (i = 1; i < 8; i++) { //TODO: GET NUMBER FROM DATABASE
		$('.hidden2').show();
		$('.hidden2').css("background-image",'url("Images/Projects/' + i + '_color.png")');
		$('.hidden2').hide();
	}
}

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
	var pageNum = $(container).attr("data-page");
	var extraParam = $(container).attr("data-param");
	var dataEndpoint = dataType.charAt(0).toUpperCase() + dataType.substr(1).toLowerCase();
	var postData = {"func":"get"};
	if(authId && tok)
	{
		postData["authId"] = authId;
		postData["auth"] = tok;
	}
	if(pageNum)
	{
		postData["page"] = pageNum;
		postData["size"] = defaultPageSize;
	}
	if(id) postData["id"] = id;
	if(extraParam) postData["param"] = extraParam;
	
	var templateEndpoint = GetLocalUrl("Templates/"+dataType+(id?"":"s")+".html");
	
	PushPromise();
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
			
		if(pageNum && $(data).length < defaultPageSize)
			$(container).next('.load-more').remove();	
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

function FormatDates(containers, useTime)
{
	$(containers).each(function(idx, d){
		if(!$(d).text().includes(":"))
			return true;
			
		var date = new Date($(d).text());
		var day = date.getDate().toString();
		var month = date.toLocaleString("en-us", { month: "long" });
		var lastDigit = parseInt(day.substr(day.length - 1));
		var superscript = "th";
		if( lastDigit < 3 )
			superscript = (lastDigit == 1)? "st" : "nd";
		var year = date.getFullYear();
		var hours = date.getHours();
		var ampm = hours >= 12 ? 'pm' : 'am';
  		var minutes = date.getMinutes();
  		
  		hours = hours % 12;
  		if(hours == 0) hours = 12;
  		if(hours < 10) hours = "0"+hours;
  		if(minutes < 10) minutes = "0"+minutes;
  		//if(day < 10) day = "0"+day;
  		if(month < 10) month = "0"+month;
  		
		var formatted = month + " " + day + superscript +", " +year;
		if(useTime)
			formatted += "&emsp;" + hours + ":" + minutes + " "+ ampm;
			
		$(d).html(formatted);
	});
}
