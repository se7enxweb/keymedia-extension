<?php
/**
 * KeyMedia extension information, shown on the about page of the administration.
 *
 * @copyright Copyright (C) 2011 - 2016 Keyteq AS, 1998 - 2026 7x
 * @package keymedia
 */

class keymediaInfo
{
    public static function info()
    {
        return array(
            'Name'      => '<a href="https://github.com/se7enxweb/keymedia-extension">KeyMedia</a>',
            'Version'   => '2.3.2',
            'Author'    => 'Keyteq AS and 7x',
            'Copyright' => 'Copyright &copy; 2011 - 2016 Keyteq AS and 1998 - ' . date( 'Y' ) . ' 7x',
            'License'   => 'Not stated by the original authors; see README.md',
            'Info_url'  => 'https://github.com/se7enxweb/keymedia-extension'
        );
    }
}
