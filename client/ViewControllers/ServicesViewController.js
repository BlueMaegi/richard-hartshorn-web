$(function(){

	$(window).on("loadscripts", function(){
		var height = '0';
		$('.service-column').each(function(idx, ele){
			var big = $(ele).outerHeight();
			console.log(big);
			height = Math.max(height, big);
		});
		$('.service-column').height(height);
		console.log(height);
	});

});
