<script>
	$(function() {ldelim}
		$('#plauditSettingsForm').pkpHandler('$.pkp.controllers.form.AjaxFormHandler');
	{rdelim});
</script>

<div id="plnSettings">
    <div id="description">{translate key="plugins.generic.plaudit.settings.description"}</div>
    <br>
	<form class="pkp_form" id="plauditSettingsForm" method="post" action="{url router=$smarty.const.ROUTE_COMPONENT op="manage" category="generic" plugin=$pluginName verb="settings" save=true}">
		{include file="controllers/notification/inPlaceNotification.tpl" notificationId="plauditSettingsFormNotification"}

		{fbvFormArea id="plauditSettingsFormArea"}
			
            {fbvFormSection title="plugins.generic.plaudit.integrationToken" required="true"}
                {fbvElement type="text" id="integrationToken" required="true" value=$integrationToken maxlength="256" size=$fbvStyles.size.MEDIUM}
            {/fbvFormSection}

			{fbvFormButtons id="plauditSettingsFormSubmit" submitText="common.save" hideCancel=true}
		{/fbvFormArea}
	</form>
</div>