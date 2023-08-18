<?php

namespace A3020\Gdpr\Html;

use A3020\Gdpr\Traits\PackageTrait;

class FontAwesomeIcon
{
    use PackageTrait;
    
    public function warning($tooltip = null)
    {
        if ($this->isVersion9()) {
            return $this->getIcon('fas fa-exclamation-circle', $tooltip);
        }

        return $this->getIcon('fa fa-warning', $tooltip);
    }

    public function check($tooltip = null)
    {
        if ($this->isVersion9()) {
            return $this->getIcon('fas fa-check-circle', $tooltip);
        }

        return $this->getIcon('fa fa-check', $tooltip);
    }

    public function externalLink($tooltip = null)
    {
        if ($this->isVersion9()) {
            return $this->getIcon('fas fa-external-link-alt', $tooltip);
        }

        return $this->getIcon('fa fa-external-link', $tooltip);
    }

    public function question($tooltip = null)
    {
        if ($this->isVersion9()) {
            return $this->getIcon('fas fa-question-circle', $tooltip);
        }

        return $this->getIcon('fa fa-question-circle', $tooltip);
    }

    public function getIcon($class, $tooltip = null)
    {
        if ($tooltip) {
            return sprintf('<i class="text-muted launch-tooltip %s" title="%s"></i>', $class, $tooltip);
        }

        return sprintf('<i class="%s"></i>', $class);
    }
}