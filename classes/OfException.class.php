<?php
// vim: set et ts=4 sw=4 fdm=marker:
/**
 * ...
 *
 * @author      Mickaël Guérin & Lauréline Provost
 * @copyright   The OpenFlyers Group
 * @license     GNU GPL
 */

// {{{ class OfException

class OfException extends Exception {
    // {{{ $cat

    private $cat;

    // }}}
    // {{{ constructor()

    public function __construct($_message, $_cat='', $_code = 0) {
        parent::__construct($_message, $_code);
        $this->cat = $_cat;
    }

    // }}}
    // {{{ getCat()

    public function getCat() {
        return $this->cat;
    }

    // }}}
    // {{{ printError()

    public function printError() {
        echo '<br/><div class="error">'.parent::getMessage().'</div>';
    }

    // }}}
}

// }}}

?>
