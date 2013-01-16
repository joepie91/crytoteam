<!doctype html>
<html>
	<head>
		<title>Cryto Team</title>
		<link href='http://fonts.googleapis.com/css?family=Nobile:400,400italic,700,700italic' rel='stylesheet' type='text/css'>
		<link rel="stylesheet" href="/static/style.css">
	</head>
	<body>
		<div class="wrapper">
			<div class="header">
				<h1>Team</h1>
				<h2>{%?project-name}</h2>
				<div class="clear"></div>
			</div>
			<ul class="menu">
				<li class="active"><a href="#">Overview</a></li>
				<li><a href="#">Downloads</a></li>
				<li><a href="#">Code</a></li>
				<li><a href="#">Tickets</a></li>
				<li><a href="#">Forum</a></li>
				<li><a href="#">Contributors</a></li>
				<li><a href="#">Invitations</a></li>
				<li class="clear"></li>
			</ul>
			<div class="main">
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
				<div class="clear"></div>
			</div>
			<div class="footer">
				Cryto Team is a free project management and hosting service for non-profit projects. <a href="/signup">Sign up today!</a>
			</div>
		</div>
	</body>
</html>
