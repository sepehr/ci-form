<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter Drupal-inspited Form API plus Bootstrap integration
 *
 * @package		CodeIgniter
 * @author		Sepehr Lajevardi <me@sepehr.ws>
 * @copyright	Copyright (c) 2012 Sepehr Lajevardi.
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		https://github.com/sepehr/ci-form
 * @version 	Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

if ( ! function_exists('form'))
{
	/**
	 * Renders a form definition array into HTML using Form library.
	 */
	function form(array $field, $subform = TRUE)
	{
		$CI =& get_instance();
		class_exists('Form') OR $CI->load->library('Form');

		return Form::render($form, $subform);
	}
}