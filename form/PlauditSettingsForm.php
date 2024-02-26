<?php

namespace APP\plugins\generic\plaudit\form;

use PKP\form\Form;
use APP\template\TemplateManager;

class PlauditSettingsForm extends Form
{
    public $contextId;
    public $plugin;

    public function __construct($plugin, $contextId)
    {
        $this->contextId = $contextId;
        $this->plugin = $plugin;
        parent::__construct($plugin->getTemplateResource('settings.tpl'));
    }

    public function fetch($request, $template = null, $display = false)
    {
        $integrationToken = $this->plugin->getSetting($this->contextId, 'integration_token');

        $templateMgr = TemplateManager::getManager($request);
        $templateMgr->assign(array(
            'pluginName' => $this->plugin->getName(),
            'integrationToken' => $integrationToken,
        ));

        return parent::fetch($request, $template, $display);
    }

    public function readInputData()
    {
        $this->readUserVars(['integrationToken']);
    }

    public function execute(...$functionArgs)
    {
        parent::execute(...$functionArgs);

        if (!is_null($this->getData('integrationToken'))) {
            $this->plugin->updateSetting($this->contextId, 'integration_token', $this->getData('integrationToken'));
        }
    }
}
