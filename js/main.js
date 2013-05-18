
function get_picture()
{
	$.getJSON('ajax/get_latest_picture.php', function(data) {
	
		if($('#photo_container').data('bg')!=data[0].image)
		{
			$('#photo_container').animate({'opacity': 0},500, function(){ 
				$(this).data('bg',data[0].image).css('background-image', 'url('+data[0].image+')').animate({opacity: 1}, 1500);
			})
			$('#other_pics').empty();
			$.each(data, function(i, photo){
				if(i>0)
				{
					$('#other_pics').append('<li style="background-image: url('+photo.thumb+')"></li>');
				}
				
			})
		}
		
		setTimeout('get_picture()', 1500);
	})

	
}


$(window).ready(function(){
	
	$('#photo_container').height($(window).height());
	
	get_picture();
	
})