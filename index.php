<?php

/**
 * @defgroup plugins_generic_plaudit Plaudit Plugin
 */

/**
 * @file plugins/generic/plaudit/index.php
 *
 * Copyright (c) 2022 Lepidus Tecnologia
 * Distributed under the GNU GPL v3. For full terms see LICENSE or https://www.gnu.org/licenses/gpl-3.0.txt.
 *
 * @ingroup plugins_generic_plaudit
 * @brief Wrapper for plaudit plugin.
 *
 */

require_once('PlauditPlugin.inc.php');

return new PlauditPlugin();
