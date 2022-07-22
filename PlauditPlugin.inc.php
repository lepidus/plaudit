<?php

/**
 * @file plugins/generic/plaudit/PlauditPlugin.inc.php
 *
 * Copyright (c) 2022 Lepidus Tecnologia
 * Distributed under the GNU GPL v3. For full terms see LICENSE or https://www.gnu.org/licenses/gpl-3.0.txt.
 *
 * @class PlauditPlugin
 * @ingroup plugins_generic_plauditPlugin
 *
 * @brief Plaudit Plugin
 */

import('lib.pkp.classes.plugins.GenericPlugin');

class PlauditPlugin extends GenericPlugin {
    public function register($category, $path, $mainContextId = NULL) {
		$success = parent::register($category, $path, $mainContextId);
        
        if (!Config::getVar('general', 'installed') || defined('RUNNING_UPGRADE'))
            return true;
        
        if ($success && $this->getEnabled($mainContextId)) {
            HookRegistry::register('Templates::Preprint::Details', array($this, 'addToPreprintDetails'));
            // HookRegistry::register('Templates::Article::Details', array($this, 'addSubmissionDisplay'));
			// HookRegistry::register('Templates::Catalog::Book::Details', array($this, 'addSubmissionDisplay'));
        }
        
        return $success;
    }
	
	public function getDisplayName() {
		return __('plugins.generic.plaudit.displayName');
	}

	public function getDescription() {
		return __('plugins.generic.plaudit.description');
	}

    public function addToPreprintDetails($hookName, $params) {
        $templateMgr = $params[1];
		$output =& $params[2];
		
		$request = Application::get()->getRequest();
		$integrationToken = $this->getSetting($request->getContext()->getId(), 'integration_token');

		$templateMgr->assign('integrationToken', $integrationToken);
        $output .= $templateMgr->fetch($this->getTemplateResource('plauditWidget.tpl'));
        
        return false;
    }

    public function getActions($request, $actionArgs) {
		$actions = parent::getActions($request, $actionArgs);

		if (!$this->getEnabled()) {
			return $actions;
		}

		$router = $request->getRouter();
		import('lib.pkp.classes.linkAction.request.AjaxModal');
		$linkAction = new LinkAction(
			'settings',
			new AjaxModal(
				$router->url(
					$request,
					null,
					null,
					'manage',
					null,
					array(
						'verb' => 'settings',
						'plugin' => $this->getName(),
						'category' => 'generic'
					)
				),
				$this->getDisplayName()
			),
			__('manager.plugins.settings'),
			null
		);

		array_unshift($actions, $linkAction);

		return $actions;
	}
    
    public function manage($args, $request) {
        switch($request->getUserVar('verb')) {
			case 'settings':
                $context = $request->getContext();
                $this->import('form.PlauditSettingsForm');
				$form = new PlauditSettingsForm($this, $context->getId());

                if ($request->getUserVar('save')) {
                    $form->readInputData();
					if($form->validate()){
						$form->execute();
						return new JSONMessage(true);
					}
                    return new JSONMessage(true);
                }
				
                return new JSONMessage(true, $form->fetch($request));
            default:
                return parent::manage($verb, $args, $message, $messageParams);
        }
    }
}