<?php

namespace APP\plugins\generic\plaudit\form;

use PKP\form\Form;
use APP\template\TemplateManager;
use APP\plugins\generic\plaudit\classes\APIKeyEncryption;

class PlauditSettingsForm extends Form
{
    public $contextId;
    public $plugin;
    private $encrypter;

    public function __construct($plugin, $contextId)
    {
        $this->contextId = $contextId;
        $this->plugin = $plugin;
        $this->encrypter = new APIKeyEncryption();

        parent::__construct($plugin->getTemplateResource('settings.tpl'));
    }

    public function fetch($request, $template = null, $display = false)
    {
        $integrationToken = $this->plugin->getSetting($this->contextId, 'integration_token');
        if (!empty($integrationToken) && $this->encrypter->textIsEncrypted($integrationToken)) {
            $integrationToken = $this->encrypter->decryptString($integrationToken);
        }

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

        $integrationToken = $this->getData('integrationToken');
        if (!is_null($integrationToken)) {
            $integrationToken = $this->encrypter->encryptString($integrationToken);
            $this->plugin->updateSetting($this->contextId, 'integration_token', $integrationToken);
        }
    }
}
