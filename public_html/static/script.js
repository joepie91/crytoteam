/*
 * Cryto Team is more free software. It is licensed under the WTFPL, which
 * allows you to do pretty much anything with it, without having to
 * ask permission. Commercial use is allowed, and no attribution is
 * required. We do politely request that you share your modifications
 * to benefit other developers, but you are under no enforced
 * obligation to do so :)
 * 
 * Please read the accompanying LICENSE document for the full WTFPL
 * licensing text.
 */

$(function(){
	$('.clickable').click(function(event)
	{
		if($(this).data('url'))
		{
			url = $(this).data('url');
			
			if(event.which == 1)
			{
				if($(this).hasClass('external'))
				{
					window.open(url);
				}
				else
				{
					window.location = url;
				}
				
				event.stopPropagation();
				return false;
			}
			else if(event.which == 2)
			{
				window.open(url);
				event.stopPropagation();
				return false;
			}
		}
	});     
});
