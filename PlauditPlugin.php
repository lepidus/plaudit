<?php

/**
 * @file plugins/generic/plaudit/PlauditPlugin.inc.php
 *
 * Copyright (c) 2022 - 2024 Lepidus Tecnologia
 * Distributed under the GNU GPL v3. For full terms see LICENSE or https://www.gnu.org/licenses/gpl-3.0.txt.
 *
 * @class PlauditPlugin
 * @ingroup plugins_generic_plauditPlugin
 *
 * @brief Plaudit Plugin
 */

namespace APP\plugins\generic\plaudit;

use PKP\plugins\GenericPlugin;
use APP\core\Application;
use PKP\plugins\Hook;
use PKP\linkAction\LinkAction;
use PKP\linkAction\request\AjaxModal;
use PKP\core\JSONMessage;
use APP\plugins\generic\plaudit\form\PlauditSettingsForm;

class PlauditPlugin extends GenericPlugin
{
    public function register($category, $path, $mainContextId = null)
    {
        $success = parent::register($category, $path, $mainContextId);

        if (Application::isUnderMaintenance()) {
            return true;
        }

        if ($success && $this->getEnabled($mainContextId)) {
            Hook::add('Templates::Preprint::Details', [$this, 'addSubmissionDetails']);
            Hook::add('Templates::Catalog::Book::Details', [$this, 'addSubmissionDetails']);
            Hook::add('Templates::Article::Details', [$this, 'addSubmissionDetails']);
        }

        return $success;
    }

    public function getDisplayName()
    {
        return __('plugins.generic.plaudit.displayName');
    }

    public function getDescription()
    {
        return __('plugins.generic.plaudit.description');
    }

    public function addSubmissionDetails($hookName, $params)
    {
        $templateMgr = $params[1];
        $output = & $params[2];

        $request = Application::get()->getRequest();
        $integrationToken = $this->getSetting($request->getContext()->getId(), 'integration_token');

        if ($integrationToken) {
            $templateMgr->assign('integrationToken', $integrationToken);
            $output .= $templateMgr->fetch($this->getTemplateResource('plauditWidget.tpl'));
        }

        return false;
    }

    public function getActions($request, $actionArgs)
    {
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

    public function manage($args, $request)
    {
        switch ($request->getUserVar('verb')) {
            case 'settings':
                $context = $request->getContext();
                $form = new PlauditSettingsForm($this, $context->getId());

                if ($request->getUserVar('save')) {
                    $form->readInputData();
                    if ($form->validate()) {
                        $form->execute();
                        return new JSONMessage(true);
                    }
                }

                return new JSONMessage(true, $form->fetch($request));
            default:
                return parent::manage($verb, $args, $message, $messageParams);
        }
    }
}
