if((!HasToken() || TokenExpired()) && window.location.href.indexOf("/admin/login.html") <= 0)
		window.location.href = GetLocalUrl("/admin/login.html");

$(function(){
	$(window).on("loadscripts", function(){
		SetupLoginForm($("#login-form"));
		SetupLogoutForm();
		setInterval(Refresh, 5000);
		if(!HasToken() || TokenExpired()) Logout();
		SetupNavBar();
	});
});

function SetupLoginForm(container)
{
	var form = {};
	form.nameField = $(".name", container);
	form.passField = $(".pwd", container);
	form.loginBtn = $(".login", container);
	
	form.loginBtn.click(validate);
	$("input", container).keyup(function (e) {
		if (e.keyCode == 13) validate();
	});
	
	function validate()
	{
		var allReady = true;
		$("input", container).each(function(idx, item){
			if($(item).val().length <= 0 || $(item).val().length > 200) allReady = false;
		});
		if(!allReady) return; //TODO: alert the user
		
		Ajax("User", {"func":"login", "name":form.nameField.val(), "word":form.passField.val()}, function(data){
			SetToken(data);
			window.location.href = GetLocalUrl("/admin/index.html");
		});
	}
}

function SetupLogoutForm()
{
	$(".logout").click(Logout);
}

function SetupNavBar()
{
	var dashLink = GetLocalUrl("admin/index.html");
	var ordersLink = GetLocalUrl("admin/orders.html");
	var inventoryLink = GetLocalUrl("admin/inventory.html");
	$('.nav-links a:last-child').before("<a href='"+dashLink+"' class='local-link'>Dashboard</a>");
	$('.nav-links a:last-child').before("<a href='"+ordersLink+"' class='local-link'>Orders</a>");
	$('.nav-links a:last-child').before("<a href='"+inventoryLink+"' class='local-link'>Inventory</a>");
	$('.nav-links').append("<a class='local-link logout'>Logout</a>");
	$('.nav-links .logout').click(Logout);
}

function SetToken(data)
{
	if(!data || !data[0] || !data[1]) 
		Logout();
	else
	{
		localStorage.setItem("id", data[0]);
		sessionStorage.setItem("tolkien", data[1]);
		sessionStorage.setItem("time", Date.now());
	}
}

function HasToken()
{
	var id = localStorage.getItem("id");
	var tok = sessionStorage.getItem("tolkien");
	var time = sessionStorage.getItem("time");
	
	if(!id || !tok || !time) return false;
	return true;
}

function TokenExpired()
{
	var time = sessionStorage.getItem("time");
	if(!time) return true;
	if(Date.now() - time >= 250000) return true;
	
	return false;
}

function Logout()
{
	Ajax("User", {"func":"login", "authId":localStorage.getItem("id")}, function(){});
	delete localStorage.id;
	delete sessionStorage.tolkien;
	delete sessionStorage.time;
	
	if(window.location.href.indexOf("/admin/login.html") <= 0)
		window.location.href = GetLocalUrl("/admin/login.html");
}

function Refresh()
{
	if(!HasToken()) return;
	if(!TokenExpired()) return;
	
	var id = localStorage.getItem("id");
	var tok = sessionStorage.getItem("tolkien");
	
	Ajax("User", {"func":"refresh", "authId":id, "auth":tok}, function(data){
		SetToken([id, data]);
	});
	
}