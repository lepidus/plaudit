<?php

import('lib.pkp.tests.PKPTestCase');
import('plugins.generic.plaudit.form.PlauditSettingsForm');

class PlauditSettingsFormTest extends PKPTestCase
{
    public function testSettingsRequireASessionConfirmedPost(): void
    {
        $plugin = new class () {
            public function getTemplateResource($name)
            {
                return $name;
            }
        };
        $original = Application::get()->getRequest();
        foreach ([[true, true, true], [false, true, false], [true, false, false]] as [$post, $confirmed, $valid]) {
            $form = new PlauditSettingsForm($plugin, 1);
            $request = $this->createMock(Request::class);
            $request->method('isPost')->willReturn($post);
            $request->method('checkCSRF')->willReturn($confirmed);
            Registry::set('request', $request);
            try {
                $this->assertSame($valid, $form->validate(false));
            } finally {
                Registry::set('request', $original);
            }
        }
    }
}
