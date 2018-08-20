$(function(){
	$(window).resize(setDimensions);
	setDimensions();
});

function setDimensions()
{
	var logoRatio = 0.1639;
	$('#logo').height($('#logo').width() * logoRatio);
}
