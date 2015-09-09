<h2 style="text-align: center">Bandwidth Usage</h2>
<br>
<table class="table table-striped" style="text-align: center;">
	<thead>
	<tr>
		<td>Date</td>
		<td>IN</td>
		<td>OUT</td>
	</tr>
	</thead>
	<tbody>
	{foreach from=$bandwidth key=key item=value}
	<tr>
		{foreach from=$value key=k item=v}
			<td>{$v}</td>
		{/foreach}
	</tr>
	{/foreach}
	</tbody>
</table>

