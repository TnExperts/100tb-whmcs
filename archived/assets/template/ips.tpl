<h2 style="text-align: center">Additional IP Addresses</h2>
<br>
<div id="ipUpdateMessage">
</div>
<table class="table table-striped" style="text-align: center;">
	<thead>
		<tr>
			<td>IP</td>
			<td>PTR</td>
		</tr>
	</thead>
	<tbody>
	{foreach from=$ips key=i item=ip}
	<tr>
		{foreach from=$ip key=k item=v}
			{if $ptrAllowed == 'on' && $k == 'ptr'}
				<td style="width: 50%"><input class="ipAddressPtr" style="width: 95%" type="text" size="60" value="{$v}" /></td>
				<td style="width: 10%">
					<button id="updatePtrRecord" type="submit" class="btn btn-info send ladda-button" data-style="zoom-out" data-size="s"><span class="ladda-label">Update</span></button>
				</td>
			{else}
				<td class="ipAddress" style="width: 40%">{$v}</td>
			{/if}
		{/foreach}
	</tr>
	{/foreach}
	</tbody>
</table>


{literal}
	<script type="text/javascript">
		$(document).ready(function() {
			$("#updatePtrRecord").click( function()
				{
					var messageBox = $('#ipUpdateMessage');
					messageBox.removeClass();

					var ipAddress = $(this).closest("tr").find(".ipAddress").text();
					var ipPtr = $(this).closest("tr").find(".ipAddressPtr").val();
					if (typeof ipPtr == 'undefined' || ipPtr == '') {
						messageBox.addClass('alert alert-warning textcenter').html('<p>Missing PTR value for ' + ipAddress + '</p>');
						return false;
					}

					messageBox.addClass('alert alert-info textcenter').html('<p>Submitting PTR update...</p>');

					var apiKey = "{/literal}{$encodedKey}{literal}";
					var serverId = {/literal}{$serverId}{literal};
					var url = "{/literal}{$systemurl}{literal}";

					$.ajax({
						type: "POST",
						url: url + "modules/servers/servers100tb/assets/php/update.php",
						data: {ipAddress:ipAddress, ipPtr:ipPtr, apiKey:apiKey, serverId:serverId},
						dataType: "json"
					}).done(function( response ) {
						messageBox.removeClass();
						if (response.success) {
							messageBox.addClass('alert alert-success textcenter').html('<p>Successfully updated PTR record of ' + ipAddress + '</p>');
						} else {
							messageBox.addClass('alert alert-error textcenter').html('<p>Error: Failed to update the PTR record of '+ ipAddress +'</p>');
						}
					});
				}
			);
		});
	</script>
{/literal}