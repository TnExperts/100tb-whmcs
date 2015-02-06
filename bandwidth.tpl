<ul>
	{foreach from=$bandwidth key=key item=value}
		{foreach from=$value key=k item=v}
			<li>{$k}: {$v}</li>
		{/foreach}
	{/foreach}
</ul>
