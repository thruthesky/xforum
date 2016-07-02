<?php
/**
 * @file init.php
 *
 */



/**
 * HTTP Query Input check and filter input.
 *
 * @todo count number of inputs for a specific work and if it does not match, display error.
 */

if ( in('forum') == 'list' && in('slug') == null ) {
    wp_die("Slug is not provided");
}
