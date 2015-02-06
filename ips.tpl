<ul>
	{foreach from=$ips key=i item=ip}
		{foreach from=$ip key=k item=v}
			<li>{$k}: {$v}</li>
		{/foreach}
		<br>
	{/foreach}
</ul>