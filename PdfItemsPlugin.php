<?php
/**
 * PdfItems
 *
 * Provide the ability to generate items in PDF
 *
 * @copyright Copyright 2011-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 * @package PdfItemPlugin
 */

define('PDF_ITEMS_DIR', dirname(__FILE__));

require_once PDF_ITEMS_DIR . '/helpers/Pdf_Items.php';
require_once PDF_ITEMS_DIR . '/libraries/fpdf-181/fpdf.php';


/**
 * The PdfItems plugin.
 * @package Omeka\Plugins\PdfItems
 */
class PdfItemsPlugin extends Omeka_Plugin_AbstractPlugin
{
    /**
     * @var array Hooks for the plugin.
     */
    protected $_hooks = array(
        'public_items_show',
        'define_routes'
    );


    /**
     * @var array Filters for the plugin.
     */
    protected $_filters = array(
        
    );


    /**
     * Manage display of "add/remove to cart" links
     */
    public function hookPublicItemsShow($args) {

        if (is_admin_theme()) return;

        $html  = '<div class="pdf-items">';
        $html .= '<a target="_blank" href="'.url('items/pdf/124').'">'.__('Export to PDF').'</a>';
        $html .= '</div>';
        echo $html;
    }


    /**
     * Add the routes
     *
     * @param Zend_Controller_Router_Rewrite $router
     */
    public function hookDefineRoutes($args)
    {
        // Don't add these routes on the admin side to avoid conflicts.
        if (is_admin_theme()) return;

        // Include routes file
        $router = $args['router'];
        $router->addConfig(new Zend_Config_Ini(PDF_ITEMS_DIR . DIRECTORY_SEPARATOR . 'routes.ini', 'routes'));
    }



}




