<?php

namespace A3020\Gdpr\Listener\OnPageOutput;

use A3020\Gdpr\Tracking\Code;
use Concrete\Core\Page\Page;

class DisableTracking
{
    /**
     * @var Code
     */
    private $tracking;

    public function __construct(Code $tracking)
    {
        $this->tracking = $tracking;
    }

    public function handle($event)
    {
        if (!$this->tracking->has()) {
            return;
        }

        /** @var Page $page */
        $page = Page::getCurrentPage();
        if ($page->isAdminArea()) {
            // Otherwise the 'Tracking Codes' page wouldn't show the tracking codes.
            return;
        }

        $contents = $event->getArgument('contents');

        $contents = str_replace($this->tracking->header(), '', $contents);
        $contents = str_replace($this->tracking->footer(), '', $contents);

        $event->setArgument('contents', $contents);
    }
}
