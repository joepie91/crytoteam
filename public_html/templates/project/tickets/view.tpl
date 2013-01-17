<div class="section ticket-original">
	<h2>{%?title}</h2>
	<div class="metadata">
		<div class="currentdata">
			<div class="priority">
				<span class="key">Priority</span>
				<span class="value">{%?priority}</span>
			</div>
			<div class="status">
				<span class="key">Status</span>
				<span class="value">{%?status}</span>
			</div>
			<div class="owner">
				<span class="key">Owner</span>
				<span class="value">{%?owner}</span>
			</div>
		</div>
		<div class="originaldata">
			<div class="creator">
				<span class="key">Creator</span>
				<span class="value">{%?creator}</span>
			</div>
			<div class="date">
				<span class="key">Date</span>
				<span class="value">2013/01/17 11:02:27</span>
			</div>
		</div>
		<div class="clear"></div>
	</div>
	<div class="body">
		{%?body}
	</div>
</div>
<div class="section ticket-updates">
	{%foreach update in updates}
		{%if update[event] == true}
			<div class="event">
				{%if update[component] == "owner"}
					<span class="author">{%?update[user]}</span> changed the owner to <span class="value">{%?update[operation]}</span>.
				{%elseif update[component] == "status"}
					<span class="author">{%?update[user]}</span> changed the ticket status to <span class="value">{%?update[operation]}</span>.
				{%elseif update[component] == "priority"}
					<span class="author">{%?update[user]}</span> changed the priority to <span class="value">{%?update[operation]}</span>.
				{%/if}
				<div class="date">{%?update[date]}</div>
			</div>
		{%else}
			<div class="message">
				<div class="metadata">
					<span class="author">{%?update[author]}</span>
					<span class="date">{%?update[date]}</span>
				</div>
				
				<p>{%?update[body]}</p>
			</div>
		{%/if}
	{%/foreach}
</div>
