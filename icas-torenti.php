<?php
/**
 * @package Icas-Torenti
*/
/*
Plugin Name: ICAS Torenti
Plugin URI: http://icas.ro/serban-davidescu
Description: Plugin pentru gestiunea torentilor
Version: 1.0
Author: Alex Petroni
License: GPLv2 or later
Text Domain: icas
*/

/*
 This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

defined ( 'ICAS_AREA_TAX_DEEP' ) or define( 'ICAS_AREA_TAX_DEEP', 7 );
defined ( 'ICAS_PLUGIN_DIR' ) or define( 'ICAS_PLUGIN_DIR', plugin_dir_path(__FILE__) );
defined ( 'ICAS_PLUGIN_URL' ) or define( 'ICAS_PLUGIN_URL', plugins_url('icas-torenti') );

defined ( 'ICAS_PLUGIN_VERSION' ) or define( 'ICAS_PLUGIN_VERSION', "1.0" );


register_activation_hook(__FILE__, 'ap_icas_install');

function ap_icas_install(){
	include plugin_dir_path(__FILE__).'install/install-plugin.php';
	
	add_option( 'icas-activated', 'j' );	
}

register_deactivation_hook(__FILE__, 'ap_icas_deactivation');

function ap_icas_deactivation(){
	
}


if( defined( 'LOAD_TRANS' ) ){
	include plugin_dir_path(__FILE__).'install/load-transversals.php';
	add_action( 'admin_init', 'ap_icas_add_transversals' );
}
if( defined( 'LOAD_LONGITUDINALS' ) ){
	include plugin_dir_path(__FILE__).'install/load-longitudinals.php';
	add_action( 'admin_init', 'ap_icas_add_longitudinals' );
}

// add custom post types
include   plugin_dir_path(__FILE__).'includes/functions.php';
include   plugin_dir_path(__FILE__).'includes/post-types.php';
include   plugin_dir_path(__FILE__).'includes/taxonomies.php';

include   plugin_dir_path(__FILE__).'includes/ajax.php';

// add main classe
include   plugin_dir_path(__FILE__).'includes/class-icas-construction.php';
include   plugin_dir_path(__FILE__).'includes/class-icas-construction-sector.php';

// =======================================================================
//						ADMIN AREA
// =======================================================================
if( is_admin() ) :

include ICAS_PLUGIN_DIR.'includes/admin/class-icas-admin.php';
new Icas_Admin();

else :
// =======================================================================
//						FRONT AREA
// =======================================================================
include   plugin_dir_path(__FILE__).'includes/shortcodes.php';
include   plugin_dir_path(__FILE__).'includes/form-elements.php';
include   plugin_dir_path(__FILE__).'includes/form-processor.php';
include   plugin_dir_path(__FILE__).'includes/template-redirect.php';


include   plugin_dir_path(__FILE__).'includes/class-icas.php';
new Icas();
endif;