<?php
/**
 * Automatically loads the specified file.
 *
 * @package EMST
 */

namespace EMST;

function _emst_autoload() {
    // Construct the iterator
    $it = new \RecursiveDirectoryIterator( __DIR__ );

    // Loop through files
    foreach ( new \RecursiveIteratorIterator( $it ) as $file ) {
        if ( $file->getExtension() == 'php' ) {
            include_once( $file );
        }
    }
}

_emst_autoload();
