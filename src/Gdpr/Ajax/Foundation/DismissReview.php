<?php

namespace A3020\Gdpr\Ajax\Foundation;

use A3020\Gdpr\Controller\AjaxController;

class DismissReview extends AjaxController
{
    public function view()
    {
        $this->config->save('gdpr.foundation.review.is_dismissed', true);
    }
}
