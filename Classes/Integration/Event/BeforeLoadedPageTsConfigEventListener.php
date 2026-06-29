<?php
namespace FluidTYPO3\Flux\Integration\Event;

/*
 * This file is part of the FluidTYPO3/Flux project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Flux\Utility\VersionUtility;
use TYPO3\CMS\Core\TypoScript\IncludeTree\Event\BeforeLoadedPageTsConfigEvent;

/**
 * TYPO3 v14 no longer reads $GLOBALS['TYPO3_CONF_VARS']['BE']['defaultPageTSconfig']
 * (Breaking #105377). Flux writes its "new content element" wizard groups there while
 * registering content types (see ContentTypeBuilder), so this listener re-injects that
 * string into the v14 page TSconfig loading; otherwise all Flux content elements fall
 * into the default "plugins" wizard group instead of their flux:form.option.group groups.
 *
 * On TYPO3 < 14 core still reads the global itself, so this is a no-op there to avoid
 * double registration. The event class only exists from TYPO3 v13 (Feature #101818).
 */
class BeforeLoadedPageTsConfigEventListener
{
    public function injectFluxPageTsConfig(BeforeLoadedPageTsConfigEvent $event): void
    {
        if (VersionUtility::isCoreBelow14()) {
            return;
        }
        $pageTsConfig = $GLOBALS['TYPO3_CONF_VARS']['BE']['defaultPageTSconfig'] ?? '';
        if (is_string($pageTsConfig) && $pageTsConfig !== '') {
            $event->addTsConfig($pageTsConfig);
        }
    }
}
