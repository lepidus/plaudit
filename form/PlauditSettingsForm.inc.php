<?php

import('lib.pkp.classes.form.Form');

class PlauditSettingsForm extends Form {

	var $_contextId;
	var $_plugin;

	public function __construct($plugin, $contextId) {
		$this->_contextId = $contextId;
		$this->_plugin = $plugin;
		parent::__construct($plugin->getTemplateResource('settings.tpl'));
	}

    public function fetch($request, $template = null, $display = false) {
		$integrationToken = $this->_plugin->getSetting($this->_contextId, 'integration_token');
		
		$templateMgr = TemplateManager::getManager($request);
		$templateMgr->assign(array(
			'pluginName' => $this->_plugin->getName(),
			'integrationToken' => $integrationToken,
		));

		return parent::fetch($request, $template, $display);
	}

    function readInputData() {
        $this->readUserVars(['integrationToken']);
	}

    public function execute(...$functionArgs) {
		parent::execute(...$functionArgs);

		if(!is_null($this->getData('integrationToken'))){
			$this->_plugin->updateSetting($this->_contextId, 'integration_token', $this->getData('integrationToken'));
		}
	}

}