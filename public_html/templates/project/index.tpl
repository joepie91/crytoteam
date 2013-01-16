<aside>
	<section class="download">
		<h3>Downloads</h3>
		{%if isempty|stable-version == false}
			<a href="download/stable" class="download">
				<b class="stable"></b>
				<strong>Latest stable</strong>
				{%?stable-version}
			</a>
		{%/if}
		{%if isempty|experimental-version == false}
			<a href="download/experimental" class="download">
				<b class="experimental"></b>
				<strong>Latest testing</strong>
				{%?experimental-version}
			</a>
		{%/if}
		
		{%if no-downloads == true}
			There are no downloads for this project yet.
		{%/if}
	</section>
	<section class="statistics">
		<h3>Statistics</h3>
		<ul>
			<li><strong>{%?line-count}</strong> lines of code</li>
			<!-- <li><strong>0</strong> commits</li>
			<li><strong>0</strong> contributors</li> -->
			<li><strong>{%?ticket-count}</strong> open tickets</li>
		</ul>
	</section>
	<section class="tickets">
		<h3>Latest tickets</h3>
		<ul>
			{%if isempty|tickets == false}
				{%foreach ticket in tickets}
					<li><strong>{%?ticket[title]}</strong> {%?ticket[date]}</li>
				{%/foreach}
				{%if more-tickets == true}
					<li class="more"><a href="tickets">more...</a></li>
				{%/if}
			{%else}
				No tickets.
			{%/if}
		</ul>
	</section>
</aside>
<section class="intro">
	<h3>Introduction</h3>
	{%?long-description}
</section>
