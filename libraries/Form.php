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

/**
 * Form API class for CodeIgniter.
 *
 * @package 	CodeIgniter
 * @subpackage	Libraries
 * @category	Forms
 * @author		Sepehr Lajevardi <me@sepehr.ws>
 * @link		https://github.com/sepehr/ci-bootstap-fapi
 * @todo		- Debug logs
 * 				- Error handling
 *				- Redocument, add usage examples
 * 				- Cleanup! It's very alpha, we need a better structure and more unified API
 * 				- Support for popovers.
 */
class Form {

	/**
	 * Stores the superobject.
	 * @var object
	 */
	private static $CI;

	/**
	 * Stores field defaults.
	 * @var string
	 */
	private static $_form_defaults = array(
		'action'     => '',
		'prefix'     => '',
		'suffix'     => '',
		'multipart'  => FALSE,
		'multistep'  => FALSE,
		'attributes' => array(),
	);

	/**
	 * Stores fieldset defaults.
	 * @var string
	 */
	private static $_fieldset_defaults = array(
		'legend'     => '',
		'prefix'     => '',
		'suffix'     => '',
		'active'     => FALSE,
		'attributes' => array(),
		'accordion'  => array(),
		'type'       => 'fieldset',
	);

	/**
	 * Stores field defaults.
	 * @var string
	 */
	private static $_field_defaults = array(
		'type'            => 'text',
		'required'        => FALSE,
		'icon'            => FALSE,
		'disabled'        => FALSE,
		'readonly'        => FALSE,
		'form_actions'    => FALSE,
		'error_type'      => 'block',
		'help_type'       => 'block',
		'class'           => 'form-control span2',
		'required_markup' => ' <strong>*</strong>',
		'render_class'    => '',
		'render_callback' => '',
		'label'           => '',
		'value'           => '',
		'placeholder'     => '',
		'wrapper_class'   => '',
		'prefix'          => '',
		'suffix'          => '',
		'extra'           => '',
		'help'            => '',
		'error'           => '',
		'help_block'      => '',
		'help_inline'     => '',
		'checked'         => '',
		'selected'        => '',
		'options'         => '',
	);

	/**
	 * Stores input types.
	 * @var string
	 */
	private static $_input_fields = array(
		'checkbox', 'datetime-local',
		'color',    'date',
		'datetime', 'week',
		'email',    'file',
		'hidden',   'image',
		'month',    'number',
		'password', 'radio',
		'range',    'reset',
		'search',   'submit',
		'tel',      'text',
		'time',     'url',
	);

	/**
	 * Stores common fields template prototype.
	 * @var string
	 */
	private static $_default_template = '
	<div id="{wrapper_id}" class="control-group {type}-control-wrapper{wrapper_class}{error}">
		<label class="control-label" for="{id}">{label}{required}</label>
		<div class="controls">
			<div class="input-prepend">
				{icon}
				{prefix}
				<{element} id="{id}" class="{class}" placeholder="{placeholder}" name="{name}" type="{type}" value="{value}" {attributes} {disabled}{readonly}{checked}{selected} />
				{suffix}
				{help_inline}
				{help_block}
			</div> <!-- /.input-prepend -->
		</div> <!-- /.controls -->
	</div> <!-- /.control-group -->
	';

	/**
	 * Stores a field help markup prototype.
	 * @var string
	 */
	private static $_accordion_template = '
	<div {attributes}>
		<div class="accordion-heading">
			<a class="accordion-toggle" data-toggle="collapse" data-parent="#form-accordion" href="#{target_id}">{title}</a>
		</div> <!-- /.accordion-heading -->

		<div id="{target_id}" class="accordion-body collapse{active}">
	        <div class="accordion-inner">
	        	{nav_prev} {body} {nav_next}
	        </div> <!-- /.accordion-inner -->
	    </div> <!-- /.accordion-body -->
	</div> <!-- /.accordion-group -->
	';

	/**
	 * Stores a dropdown template prototype.
	 * @var string
	 */
	private static $_dropdown_template = '
	<div id="{wrapper_id}" class="control-group {type}-control-wrapper{wrapper_class}{error}">
		<label class="control-label" for="{id}">{label}{required}</label>
		<div class="controls">
			<div class="input-prepend">
				{icon}
				{prefix}
				{options}
				{suffix}
				{help_inline}
				{help_block}
			</div> <!-- /.input-prepend -->
		</div> <!-- /.controls -->
	</div> <!-- /.control-group -->
	';

	/**
	 * Stores a button template prototype.
	 * @var string
	 */
	private static $_button_template = '
	<div class="controls{form_actions}">
		<{element} type="submit" class="form-submit btn {class}" {attributes}>{value}</{element}>
	</div> <!-- /.controls -->
	';

	/**
	 * Stores a field icon prototype.
	 * @var string
	 */
	private static $_icon_template = '<span class="add-on"><i class="icon icon-{icon}"></i></span>';

	/**
	 * Stores a field help markup prototype.
	 * @var string
	 */
	private static $_help_template = '<span class="help-{type}">{help}</span>';

	//--------------------------------------------------------------------

	/**
	 * Form API Constructor
	 *
	 * @return void
	 */
	public function __construct()
	{
		// Get CI superobject
		self::$CI =& get_instance();

		// And load the form helper if not yet loaded
		self::$CI->load->helper('form_helper');
	}

	//--------------------------------------------------------------------

	/**
	 * Loads a form definition and returns the rendered output.
	 *
	 * @return string
	 */
	public static function get($form_name, $render = TRUE, $flush = FALSE)
	{
		static $cache = array();

		// Internally cache the form array, since it might be
		// used for several times in one request.
		(isset($cache[$form_name]) AND !$flush) OR $cache[$form_name] = self::load($form_name);

		// Return the form array or rendered HTML, if requested
		return $render ? self::render($cache[$form_name]) : $cache[$form_name];
	}

	//--------------------------------------------------------------------

	/**
	 * Loads a form definition array from a config file.
	 *
	 * @return array
	 */
	public static function load($form_name)
	{
		self::$CI->load->config($form_name);

		$form = self::$CI->config->item(basename($form_name));

		// Allow other parties to manipulate the form array
		class_exists('Events') AND Events::trigger("form_{$form_name}_alter", $form);

		return $form;
	}

	//--------------------------------------------------------------------

	/**
	 * Rendered a form definition array/sub-array into its HTML equivalent.
	 *
	 * @return string
	 */
	public static function render($form, $subform = FALSE)
	{
		$output = '';

		// Apply form defaults
		$fields = $form = array_merge(self::$_form_defaults, $form);

		// Render openning tag
		$subform OR list($output, $fields) = self::_render_form_open($form, $form['multistep']);

		// Render fields and fieldsets
		$output .= self::_render_fields($fields, $form['multistep']);

		// Render closing tag
		$subform OR $output .= self::_render_form_close($form['suffix'], $form['multistep']);

		return $output;
	}

	//--------------------------------------------------------------------

	/**
	 * Validates a form against the rules.
	 *
	 * This might be used instead of the $this->form_validation->[set_rules()|run()];
	 *
	 * @return boolean
	 */
	public static function validate($form_name)
	{
		// Make sure that we have CI form validation class already in place.
		class_exists('CI_Form_validation') OR $this->load->library('form_validation');

		// Load the form array
		$form = self::load($form_name);

		// Pass it to the CI validation
		return self::$CI
			->form_validation
			->set_rules(self::_validate_rules($form))
			->run();
	}

	//--------------------------------------------------------------------

	/**
	 * Renders an array of fields.
	 *
	 * @return string
	 */
	private static function _render_fields($fields, $multistep = FALSE)
	{
		$output = '';

		foreach ($fields as $name => $data)
		{
			if ( ! isset($data['type']))
			{
				continue;
			}

			// Let submit typed inputs to be rendered as buttons
			($data['type'] == 'submit') AND $data['type'] = 'button';

			// 1. User custom renderrer: @TODO

			// 2. Field specific renderrer:
			if (method_exists(get_class(), '_render_' . $data['type']))
			{
				$renderer = '_render_' . $data['type'];
				$output .= self::$renderer($fields[$name], $name, $multistep);
				continue;
			}

			// 3. Default field renderrer:
			$output .= self::_render_field($fields[$name], $name);
		}

		return $output;
	}

	//--------------------------------------------------------------------

	/**
	 * Renders a single element array into HTML.
	 *
	 * @return string
	 */
	private static function _render_field($field, $name = FALSE, $template = FALSE)
	{
		// Setup default values
		$template OR $template = self::$_default_template;
		$name OR $name = isset($field['name']) ? $field['name']: 'no_name_' . rand(1, 10);

		// Merge field defaults
		$field = array_merge(self::$_field_defaults, $field);

		// Add field extras
		$field += array(
			'name'       => $name,
			'id'         => "edit-$name",
			'wrapper_id' => "$name-wrapper",
		);

		// Parse extra attributes
		isset($field['attributes']) AND $field['attributes'] = _parse_form_attributes($field['attributes'], array());

		// Prepare field value
		isset($field['value']) AND $field['value'] = self::prep($field['value']);

		// Field help
		if ($field['help'])
		{
			$field["help_{$field['help_type']}"] = str_replace(array('{type}', '{help}'), array($field['help_type'], $field['help']), self::$_help_template);
		}

		// Field error, errors will overwrite help messages
		if (function_exists('form_error') AND form_error($field['name']))
		{
			$field['error'] = ' error'; // The wrapper flag class
			$field["help_{$field['error_type']}"] = str_replace(array('{type}', '{help}'), array($field['error_type'], form_error($field['name'])), self::$_help_template);
		}

		// Field icon
		$field['icon'] AND $field['icon'] = str_replace('{icon}', $field['icon'], self::$_icon_template);

		// Field element type
		isset($field['element']) OR $field['element'] = in_array($field['type'], self::$_input_fields) ? 'input' : $field['type'];

		// Boolean flags
		$field['readonly'] = $field['readonly'] ? ' readonly' : '';
		$field['disabled'] = $field['disabled'] ? ' disabled' : '';
		$field['required'] = $field['required'] ? $field['required_markup'] : '';

		// Class strings
		// @TODO: Fix form_actions. We should be able to group multiple buttons into one form_actions element
		$field['form_actions']  AND $field['form_actions']  = ' form-actions';
		$field['wrapper_class'] AND $field['wrapper_class'] = ' ' . $field['wrapper_class'];

		// Prep field placeholder map
		foreach ($field as $key => $value)
		{
			$field['{' . $key . '}'] = $value;
			unset($field[$key]);
		}

		return str_replace(array_keys($field), array_values($field), $template);
	}

	//--------------------------------------------------------------------

	/**
	 * Renders a fieldset element and its embedded fields.
	 *
	 * @return string
	 */
	private static function _render_fieldset($fieldset, $name = FALSE, $multistep = FALSE)
	{
		$output = '';

		// Merge fieldset defaults
		$fieldset = array_merge(self::$_fieldset_defaults, $fieldset);

		// Render fieldset prefix
		$fieldset['prefix'] AND $output .= $fieldset['prefix'];

		$output .= '<fieldset ' . _parse_form_attributes($fieldset['attributes'], array()) . '>';

		// Render fieldset legend
		$fieldset['legend'] AND $output .= '<legend>' . $fieldset['legend'] . '</legend>';

		// Unset fieldset attributes
		$_fieldset = $fieldset;
		foreach (self::$_fieldset_defaults as $key => $value)
		{
			unset($fieldset[$key]);
		}

		// Render embedded fields
		$output .= self::render($fieldset, TRUE) . '</fieldset>';

		// Render fieldset suffix
		$_fieldset['suffix'] AND $output .= $_fieldset['suffix'];

		// Render fieldset as a multistep form step, if required
		if ($multistep)
		{
			// Accordion dynamic defaults
			// @TODO Integrate language class
			$accordion_defaults = array(
				'nav_buttons'    => FALSE,
				'body'           => $output,
				'title'          => $_fieldset['legend'],
				'active'         => $_fieldset['active'],
				'target_id'      => $name . '-accordion',
				'attributes'     => array('class' => 'accordion-group', 'id' => $name . '-accordion-wrapper'),
				'nav_next'       => '',
				'nav_prev'       => '',
				'nav_next_label' => 'Next',
				'nav_prev_label' => 'Previous',
			);

			// Merge accordion defaults
			$_fieldset['accordion'] = array_merge($accordion_defaults, $_fieldset['accordion']);

			// Render fieldset as an accordion
			$output = self::_render_accordion($_fieldset['accordion'], $name);
		}

		return $output;
	}

	//--------------------------------------------------------------------

	/**
	 * Renders the passing array into an accordion element.
	 *
	 * @return string
	 */
	private static function _render_accordion($accordion, $name = FALSE)
	{
		// @TODO: Make sure that accordion group class is set

		// Accordion navigation?
		if ($accordion['nav_buttons'])
		{
			foreach (array('prev', 'next') as $nav)
			{
				if (isset($accordion["nav_$nav"]) AND $accordion["nav_$nav"])
				{
					// Render accordion navigation buttons
					$accordion["nav_$nav"] = self::_render_button(array(
						'class'       => "btn btn-info pull-right accordion-$nav",
						'value'       => $accordion["nav_{$nav}_label"],
						'attributes'  => array(
							'data-toggle' => 'collapse',
							'data-parent' => '#form-accordion',
							'data-target' => '#' . $accordion["nav_$nav"],
						),
					), "nav_button_$nav", FALSE, 'a');
				}
			}
		}

		// Active step? add the class
		$accordion['active'] AND $accordion['active'] = ' in';

		return self::_render_field($accordion, $name, self::$_accordion_template);
	}

	//--------------------------------------------------------------------

	/**
	 * Renders a button element.
	 *
	 * @return string
	 */
	private static function _render_button($button, $name = FALSE, $multistep = FALSE, $element = 'button')
	{
		// Rendering A, DIV or SPAN elements as buttons?
		$button['element'] = $element;

		// @TODO: Add inline icon support
		// @TODO: Ensure proper button classes exist

		return self::_render_field($button, $name, self::$_button_template);
	}

	//--------------------------------------------------------------------

	/**
	 * Renders a select element.
	 *
	 * @return string
	 */
	private static function _render_dropdown($dropdown, $name, $multistep = FALSE)
	{
		// Parse dropdown attributes
		$attributes = isset($dropdown['attributes']) ? _parse_form_attributes($dropdown['attributes'], array()) : '';

		// Render dropdown select and option elements
		isset($dropdown['options']) AND
			$dropdown['options'] = form_dropdown($name, $dropdown['options'], $dropdown['value'], $attributes);

		return self::_render_field($dropdown, $name, self::$_dropdown_template);
	}

	//--------------------------------------------------------------------

	/**
	 * Renders a form element openning tag.
	 *
	 * @return array
	 */
	private static function _render_form_open($form, $multistep = FALSE)
	{
		// Open form, multipart if required
		$output = $form['prefix'] . ($form['multipart']
			? form_open_multipart($form['action'], $form['attributes'])
			: form_open($form['action'], $form['attributes']));

		// If the form is multistep, append accordion wrapper
		$multistep AND $output .= '<div class="accordion" id="form-accordion">';

		// Prep form array for field render
		foreach (self::$_form_defaults as $key => $value)
		{
			unset($form[$key]);
		}

		return array($output, $form);
	}

	//--------------------------------------------------------------------

	/**
	 * Renders a form element closing tag.
	 *
	 * @return string
	 */
	private static function _render_form_close($suffix, $multistep = FALSE)
	{
		// If the form is multistep, append accordion wrapper
		$output = $multistep ? '</div>' : '';

		return $output . form_close($suffix);
	}

	//--------------------------------------------------------------------

	/**
	 * Prepares the value for display in the form.
	 *
	 * @return string
	 */
	public static function prep($value)
	{
		// TODO: Repopulate if possible.
		$value = htmlspecialchars($value);
		$value = str_replace(array("'", '"'), array("&#39;", "&quot;"), $value);

		return $value;
	}

	//--------------------------------------------------------------------

	/**
	 * Builds a validation array out of the form array.
	 *
	 * @return array
	 */
	public static function _validate_rules($form)
	{
		$rules = array();

		foreach ($form as $name => $field)
		{
			// Extract rules from fieldsets
			if (isset($field['type']) AND $field['type'] == 'fieldset')
			{
				$rules += self::_validate_rules($field);
				continue;
			}

			// Skip non-fields, or fields with no
			if ( ! is_array($field) OR ! isset($field['type']) OR ! isset($field['rules']))
			{
				continue;
			}

			$rules[] = array(
				'field' => $name,
				'rules' => $field['rules'],
				// Field label is optional, field name will be used instead
				'label' => isset($field['label']) ? $field['label'] : '',
			);
		}

		return $rules;
	}

}
// End of Form class

/* End of file Form.php */
/* Location: ./application/libraries/Form.php */