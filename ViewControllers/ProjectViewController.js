$(function(){
	$(window).on('loadscripts', function(){
		$('.project').each(function(){
			var id = $(this).attr("rel");
			var child = $(this).children('.thumbnail');
		
			$(child).css('background-image', 'url("Images/Projects/' + id + '.png")');
			$(child).hover(function(e){
				$(this).css("background-image",e.type === "mouseenter"?
					'url("Images/Projects/' + id + '_color.png")':
					'url("Images/Projects/' + id + '.png")')
			});
		});
	});
});
