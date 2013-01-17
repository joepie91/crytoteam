<div class="header">
	<h1>Team</h1>
	<h2>{%?project-name}</h2>
	<div class="clear"></div>
</div>
<ul class="menu">
	<li {%if current-page == "overview"}class="active"{%/if}><a href="{%?project-url}">Overview</a></li>
	<li {%if current-page == "downloads"}class="active"{%/if}><a href="{%?project-url}/downloads">Downloads</a></li>
	<li {%if current-page == "code"}class="active"{%/if}><a href="{%?project-url}/code">Code</a></li>
	<li {%if current-page == "tickets"}class="active"{%/if}><a href="{%?project-url}/tickets">Tickets</a></li>
	<li {%if current-page == "forum"}class="active"{%/if}><a href="{%?project-url}/forum">Forum</a></li>
	<li {%if current-page == "contributors"}class="active"{%/if}><a href="{%?project-url}/contributors">Contributors</a></li>
	<li {%if current-page == "invitations"}class="active"{%/if}><a href="{%?project-url}/invitations">Invitations</a></li>
	<li class="clear"></li>
</ul>
<div class="main">
	{%?contents}
	<div class="clear"></div>
</div>
