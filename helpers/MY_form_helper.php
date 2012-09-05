<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter Drupal-inspited Form API with seemless HTML5/Bootstrap integration.
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
	 *
	 * @param  array   $field   Form definition array.
	 * @param  boolean $subform Indicates whether it's a subform or not.
	 *
	 * @return string           Themed representation of form.
	 *
	 * @see https://github.com/sepehr/ci-form
	 */
	function form(array $field, $subform = TRUE)
	{
		$CI =& get_instance();
		class_exists('Form') OR $CI->load->library('Form');

		return Form::render($form, $subform);
	}
}

// ------------------------------------------------------------------------


if ( ! function_exists('form_dropdown'))
{
	/**
	 * A fork of core form_dropdown() to support option attributes.
	 *
	 * Sample $options array:
	 * <code>
	 * $options = array(
	 *     // Option with attributes:
	 *     'channel_islands' => array(
	 * 		    // Option value:
	 *		    'option_value' => 'Channel Islands',
	 *		    // Other attributes: (class, id, style, etc.)
	 *		    'class' => 'London',
	 *	    ),
	 *
	 * 		// Oldskool options:
	 * 		'barnet' => 'Barnet',
	 *
	 *	    // ...
	 *	);
	 * </code>
	 *
	 * @param  string $name     Field name.
	 * @param  array  $options  Array of dropdown options.
	 * @param  array  $selected Array of selected values.
	 * @param  string $extra    Extra string to append to <select>.
	 *
	 * @return string           HTML representation of the dropdown.
	 *
	 * @see http://codeigniter.com/forums/viewthread/193529/
	 */
	function form_dropdown($name = '', $options = array(), $selected = array(), $extra = '')
	{
		if ( ! is_array($selected))
		{
			$selected = array($selected);
		}

		// If no selected state was submitted we will attempt to set it automatically
		if (count($selected) === 0)
		{
			// If the form name appears in the $_POST array we have a winner!
			if (isset($_POST[$name]))
			{
				$selected = array($_POST[$name]);
			}
		}

		if ($extra != '') $extra = ' '.$extra;

		$multiple = (count($selected) > 1 && strpos($extra, 'multiple') === FALSE) ? ' multiple="multiple"' : '';

		$form = '<select name="'.$name.'"'.$extra.$multiple.">\n";

		foreach ($options as $key => $val)
		{
			$attrs = '';
			$key   = (string) $key;

			// 1. Handle option attributes
			if (is_array($val) AND isset($val['option_value']))
			{
				// Get attributes, update value
				$attrs = $val;
				$val   = $val['option_value'];
				unset($attrs['option_value']);
				// Theme attributes
				$attrs = _parse_form_attributes($attrs, array());
			}

			// 2. Still an array? It's an optgroups
			if (is_array($val) && ! empty($val))
			{
				$form .= '<optgroup label="'.$key.'">'."\n";

				foreach ($val as $optgroup_key => $optgroup_val)
				{
					$attrs = '';

					// Handle optgroup option attributes
					if (is_array($optgroup_val) AND isset($optgroup_val['option_value']))
					{
						// Get attributes, update value
						$attrs        = $optgroup_val;
						$optgroup_val = $optgroup_val['option_value'];
						unset($attrs['option_value']);
						// Theme attributes
						$attrs = _parse_form_attributes($attrs, array());
					}

					$sel   = in_array($optgroup_key, $selected) ? ' selected="selected"' : '';
					$form .= '<option value="' . $optgroup_key . '"' . $sel . ' ' . $attrs . '>' . (string) $optgroup_val . "</option>\n";
				}

				$form .= '</optgroup>'."\n";
			}

			// 3. Not an array value
			else
			{
				$sel   = in_array($key, $selected) ? ' selected="selected"' : '';
				$form .= '<option value="' . $key . '"' . $sel . ' ' . $attrs . '>' . (string) $val . "</option>\n";
			}
		}

		// Outro!
		$form .= '</select>';

		return $form;
	}
}

/* End of file MY_form_helper.php */
/* Location: ./application/helpers/MY_form_helper.php */