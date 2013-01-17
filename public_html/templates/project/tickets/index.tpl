<section class="tickets">
	<table>
		<tr>
			<th class="empty"></th>
			<th>Title</th>
			<th>Priority</th>
			<th>Status</th>
		</tr>
		{%foreach ticket in tickets}
			<tr class="clickable priority-{%?ticket[priority-lowercase]} status-{%?ticket[status-lowercase]}" data-url="{%?project-url}/ticket/{%?ticket[id]}">
				<td class="id">#{%?ticket[id]}</td>
				<td class="title"><a href="{%?project-url}/ticket/{%?ticket[id]}">{%?ticket[title]}</a></td>
				<td class="priority">{%?ticket[priority]}</td>
				<td class="status">{%?ticket[status]}</td>
			</tr>
		{%/foreach}
	</table>
</section>
