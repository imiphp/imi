/**
 * author: lovefc
 * blog: http://lovefc.cn/
 */
jQuery(document).ready(function($){
	var offset = 300,
		offset_opacity = 1000,
		scroll_top_duration = 700,
		back_to_top = $('.fc-top');
	$(window).scroll(function(){
		( $(this).scrollTop() > offset ) ? back_to_top.addClass('fc-is-visible') : back_to_top.removeClass('fc-is-visible fc-fade-out');
		if( $(this).scrollTop() > offset_opacity ) { 
			back_to_top.addClass('fc-fade-out');
		}
	});
	back_to_top.on('click', function(event){
		event.preventDefault();
		$('body,html').animate({
			scrollTop: 0 ,
		 	}, scroll_top_duration
		);
	});
});