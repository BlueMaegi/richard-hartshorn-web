$(function(){
	$('.section-title').click(function(){
		var child = $(this).parent().next('.block');
		
		if($(child).attr("data-state") == "open")
			CollapseBlock(child)
		else if($(child).attr("data-state") == "closed")
			ExpandBlock(child);
	});
});

function ExpandBlock(element)
{
	$(element).attr("data-state", "animating");
	$(element).prev().children('.section-title span').html("&#9660;");
	
	var h = $(element).attr("data-size");
	$(element).animate({ height: h}, 500, function() {
		$(element).children().show();
		$(element).attr("data-state", "open");
  	});	
}

function CollapseBlock(element)
{
	$(element).attr("data-state", "animating");
	$(element).attr("data-size", $(element).outerHeight());
	$(element).prev().children('.section-title span').html("&#9654;");

	$(element).animate({ height: "15px"}, 500, function() {
		$(element).children().hide();
		$(element).attr("data-state", "closed");
  	});	
}