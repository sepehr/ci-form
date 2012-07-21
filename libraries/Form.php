<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter Drupal-inspited Form API plus Bootstrap integration
 *
 * NOTE: This library does not provide utilities to protect forms
 * against CSRF attacks. Make sure that you already turned CI's CSRF
 * protection ON.
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
 * @link		https://github.com/sepehr/ci-form
 * @todo		- Form caching
 * 				- Debug logs
 * 				- Error handling
 *				- Redocument, add usage examples
 * 				- Cleanup! It's very alpha, we need a better structure and more unified API
 * 				- There are a bunch of templates that could be merged together, redundancy sucks!
 * 				- Dependent dropdowns.
 * 				- Ajax API, if possible.
 * 				- Move templates into config files.
 * 				- Make use of set_checkbox(), set_radio() when populating toggles.
 */
class Form {

	/**
	 * Stores the superobject.
	 * @var object
	 */
	private static $CI;

	//--------------------------------------------------------------------

	/**
	 * Stores the form default values.
	 * @var object
	 */
	private static $_values = array();

	//--------------------------------------------------------------------
	// Defaults
	//--------------------------------------------------------------------

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
		'errors'     => array(),
		'attributes' => array(),
	);

	//--------------------------------------------------------------------

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
		'before'     => '',
		'after'      => '',
	);

	//--------------------------------------------------------------------

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
		'inline'          => FALSE,
		'error_type'      => 'block',
		'help_type'       => 'block',
		'class'           => 'form-control span2',
		'required_markup' => ' <strong>*</strong>',
		'attributes'      => array(),
		'input_append'    => '',
		'render_class'    => '',
		'render_callback' => '',
		'label'           => '',
		'value'           => '',
		'placeholder'     => '',
		'wrapper_class'   => '',
		'prefix'          => '',
		'suffix'          => '',
		'suffix_inline'   => '',
		'extra'           => '',
		'help'            => '',
		'error'           => '',
		'help_block'      => '',
		'help_inline'     => '',
		'checked'         => '',
		'selected'        => '',
		'options'         => '',
		'popover'         => '',
		'popover_title'   => '',
		'rules'           => '',
		'before'          => '',
		'after'           => '',
	);

	//--------------------------------------------------------------------

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

	//--------------------------------------------------------------------
	// Templates
	//--------------------------------------------------------------------

	/**
	 * Stores common fields template prototype.
	 * @var string
	 */
	private static $_default_template = '
	{before}
	<div id="{wrapper_id}" class="control-group {type}-control-wrapper{wrapper_class}{error}">
		<label class="control-label" for="{id}">{label}{required}</label>
		<div class="controls">
			<div class="input-prepend{input_append}">
				{icon}
				{prefix}
				<{element} id="{id}" class="{class}" placeholder="{placeholder}" name="{name}" type="{type}" value="{value}" {attributes} {disabled}{readonly}{checked}{selected} />
				{suffix_inline}
				{help_inline}
				{help_block}
			</div> <!-- /.input-prepend -->
			{suffix}
		</div> <!-- /.controls -->
	</div> <!-- /.control-group -->
	{after}
	';

	//--------------------------------------------------------------------

	/**
	 * Stores common inline fields template prototype.
	 * @var string
	 */
	private static $_default_inline_template = '
	{before}
	<div id="{wrapper_id}" class="input-prepend inline-field">
		{icon}
		<{element} id="{id}" class="{class}" placeholder="{placeholder}" name="{name}" type="{type}" value="{value}" {attributes} {disabled}{readonly}{checked}{selected} />
	</div> <!-- /.input-prepend -->
	{after}
	';

	//--------------------------------------------------------------------

	/**
	 * Stores hidden fields template prototype.
	 * @var string
	 */
	private static $_hidden_template = '
	<input type="hidden" name="{name}" value="{value}" />
	';

	//--------------------------------------------------------------------

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

	//--------------------------------------------------------------------

	/**
	 * Stores a dropdown template prototype.
	 * @var string
	 */
	private static $_dropdown_template = '
	{before}
	<div id="{wrapper_id}" class="control-group {type}-control-wrapper{wrapper_class}{error}">
		<label class="control-label" for="{id}">{label}{required}</label>
		<div class="controls">
			<div class="input-prepend{input_append}">
				{icon}
				{prefix}
				{options}
				{suffix_inline}
				{help_inline}
				{help_block}
			</div> <!-- /.input-prepend -->
			{suffix}
		</div> <!-- /.controls -->
	</div> <!-- /.control-group -->
	{after}
	';

	//--------------------------------------------------------------------

	/**
	 * Stores an inline dropdown template prototype.
	 * @var string
	 */
	private static $_dropdown_inline_template = '
	{before}
	<div class="input-prepend inline-field">
		<label for="{id}">{label}{required}</label>
		{icon}
		{options}
	</div>
	{after}
	';

	//--------------------------------------------------------------------

	/**
	 * Stores a button template prototype.
	 * @var string
	 */
	private static $_button_template = '
	{before}
	<div class="controls{form_actions}">
		<{element} id="{id}" class="form-submit btn {class}" {attributes}>{value}</{element}>
	</div> <!-- /.controls -->
	{after}
	';

	//--------------------------------------------------------------------

	/**
	 * Stores an inline button template prototype.
	 * @var string
	 */
	private static $_button_inline_template = '
	{before}
	<{element} id="{id}" class="form-submit btn {class}" style="float:none" {attributes}>{value}</{element}>
	{after}
	';

	//--------------------------------------------------------------------

	/**
	 * Stores a textarea template prototype.
	 * @var string
	 */
	private static $_textarea_template = '
	{before}
	<div id="{wrapper_id}" class="control-group {type}-control-wrapper{wrapper_class}{error}">
		<label class="control-label" for="{id}">{label}{required}</label>
		<div class="controls">
			<div class="input-prepend{input_append}">
				{icon}
				{prefix}
				<textarea id="{id}" class="{class}" placeholder="{placeholder}" name="{name}" type="{type}" {attributes} {disabled}{readonly}>{value}</textarea>
				{suffix_inline}
				{help_inline}
				{help_block}
			</div> <!-- /.input-prepend -->
			{suffix}
		</div> <!-- /.controls -->
	</div> <!-- /.control-group -->
	{after}
	';

	//--------------------------------------------------------------------

	/**
	 * Stores checkboxes/radios template prototype.
	 * @var string
	 */
	private static $_toggles_template = '
	{before}
	<div id="{wrapper_id}" class="control-group {type}-control-wrapper{wrapper_class}{error}">
		<label class="control-label" for="{id}">{label}{required}</label>
		<div class="controls">
			{prefix}
			{options}
			{suffix_inline}
		</div> <!-- /.controls -->
		{suffix}
	</div> <!-- /.control-group -->
	{after}
	';

	//--------------------------------------------------------------------

	/**
	 * Stores an inline checkbox/radio template prototype.
	 * @var string
	 */
	private static $_toggle_template = '
	{before}
	<label class="{toggle} inline">
		<input type="{toggle}" name="{name}" value="{value}" {attributes} {checked} /> {label}
	</label>
	{after}
	';

	//--------------------------------------------------------------------

	/**
	 * Stores a field icon prototype.
	 * @var string
	 */
	private static $_icon_template = '<i class="icon icon-{icon}"></i>';

	//--------------------------------------------------------------------

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
	// API Functions
	//--------------------------------------------------------------------

	/**
	 * Loads a form definition and returns the rendered output.
	 *
	 * @return string
	 */
	public static function get($form_name, $values = FALSE, $render = TRUE, $flush = FALSE)
	{
		static $cache = array();

		// Internally cache the form array, since it might be
		// used for several times in one request.
		(isset($cache[$form_name]) AND !$flush) OR $cache[$form_name] = self::load($form_name, $values);

		// Return the form array or rendered HTML, if requested
		return $render ? self::render($cache[$form_name]) : $cache[$form_name];
	}

	//--------------------------------------------------------------------

	/**
	 * Loads a form definition array from a config file.
	 *
	 * @return array
	 */
	public static function load($form_name, $values = FALSE)
	{
		// Load form definition file
		self::$CI->load->config($form_name);

		// Get the array
		$form = self::$CI->config->item(basename($form_name));

		// Set default field values to be overriden, if required
		self::set_defaults($values);

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
		// Terminate if not an array
		if ( ! is_array($form))
		{
			return FALSE;
		}

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

		// Set custom callbacks validation errors
		if (isset($form['errors']) AND is_array($form['errors']))
		{
			self::set_message($form['errors']);
		}

		$rules = self::_validate_rules($form);

		// Pass it to the CI validation
		return self::$CI
			->form_validation
			->set_rules($rules)
			->run();
	}

	//--------------------------------------------------------------------

	/**
	 * Sets custom callback error message.
	 *
	 * It's identical to CI form validation's set_message(),
	 * it's a wrapper in fact!
	 */
	public static function set_message($callback, $error = FALSE)
	{
		// Check if it's an array of callbacks
		if (is_array($callback))
		{
			foreach ($callback as $name => $error)
			{
				self::set_message($name, $error);
			}
		}

		// Make sure that we have CI form validation class already in place.
		// @TODO: Cache check results.
		class_exists('CI_Form_validation') OR $this->load->library('form_validation');

		self::$CI->form_validation->set_message($callback, $error);
	}

	//--------------------------------------------------------------------

	/**
	 * Flushes form validation field data.
	 *
	 * @return void
	 */
	public static function flush($flush_post = TRUE)
	{
		// Flush $_POST data
		$flush_post AND $_POST = array();

		// Flush form validation data
		if (class_exists('CI_Form_validation'))
		{
			// Here comes the hack!
			unset(self::$CI->form_validation);
		}
	}

	//--------------------------------------------------------------------

	/**
	 * Prepares the value for display in the form.
	 *
	 * @return string
	 */
	public static function value($name, $default = '')
	{
		// $_POST values?
		if (isset($_POST[$name]))
		{
			return form_prep($_POST[$name]);
		}

		// Default values?
		if (isset(self::$_values[$name]))
		{
			return form_prep(self::$_values[$name]);
		}

		return set_value($name, $default);
	}

	//--------------------------------------------------------------------

	/**
	 * Checks checkbox/radio element values.
	 *
	 * @return boolean
	 */
	public static function value_toggled($name, $value, $checked = FALSE)
	{
		$clean = self::clean($name);

		// Check $_POST for single booleans
		if (isset($_POST[$name]))
		{
			return (bool) $_POST[$name];
		}

		// Check $_POST for multiple values
		if (isset($_POST[$clean]) AND is_array($_POST[$clean]))
		{
			return in_array($value, $_POST[$clean]);
		}

		// Check self:$_values for single booleans
		if (isset(self::$_values[$name]))
		{
			return (bool) self::$_values[$name];
		}

		// Check self:$_values for multiple values
		if (isset(self::$_values[$clean]) AND is_array(self::$_values[$clean]))
		{
			return in_array($value, self::$_values[$clean]);
		}

		return $checked;
	}

	//--------------------------------------------------------------------

	/**
	 * Returns form validation errors.
	 *
	 * @return boolean
	 */
	public static function errors()
	{
		return validation_errors();
	}

	//--------------------------------------------------------------------

	/**
	 * Sets form default valurs
	 *
	 * @return void
	 */
	public static function set_defaults($values)
	{
		is_object($values) AND $values = (array) $values;
		is_array($values)  AND self::$_values = $values;
	}

	//--------------------------------------------------------------------

	/**
	 * Cleans string from []s.
	 *
	 * @return string
	 */
	public static function clean($name)
	{
		if (($pos = strpos($name, '[]')) !== FALSE)
		{
			$name = substr($name, 0, $pos);
		}

		return str_replace(array('[', ']'), array('-', ''), $name);
	}

	//--------------------------------------------------------------------
	// Internal Renderrer methods
	//--------------------------------------------------------------------

	/**
	 * Renders an array of fields.
	 *
	 * @return string
	 */
	private static function _render_fields($fields, $multistep = FALSE)
	{
		$output = '';

		if ( ! is_array($fields))
		{
			// Temporary:
			return 'Form::_render_fields(): $fields is not properly formatted array, get some coffee!';
		}

		// Render each field to HTML by calling its corresponding renderrer callback
		foreach ($fields as $name => &$data)
		{
			if ( ! isset($data['type']))
			{
				continue;
			}

			// Let submit typed inputs to be rendered as buttons
			($data['type'] == 'submit') AND $data['type'] = 'button';

			// Prepare checkbox/radiobutton elements, @TODO: Review.
			if ($data['type'] == 'checkbox' OR $data['type'] == 'radio')
			{
				// Preserve element type and make sure that we'll
				// call _render_toggles() to support renderring
				// checkbox/radio groups
				$data['toggle'] = $data['type'];
				$data['type'] = 'toggles';
			}

			// 1. User custom renderrer: @TODO

			// 2. Field specific renderrer:
			if (method_exists(get_class(), '_render_' . $data['type']))
			{
				$renderer = '_render_' . $data['type'];
				$output .= self::$renderer($data, $name, $multistep);
				continue;
			}

			// 3. Default field renderrer:
			$output .= self::_render_field($data, $name);
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
		// Set the correct template
		$template OR $template = isset($field['inline']) ? self::$_default_inline_template : self::$_default_template;

		// Setup field name
		$name OR $name = isset($field['name']) ? $field['name']: 'no_name_' . rand(1, 10);

		// Merge field defaults
		$field = array_merge(self::$_field_defaults, $field);

		// Remove brackets from name attribute, if any
		$clean = self::clean($name);

		// Add field extras
		$field += array(
			'name'       => $name,
			'id'         => "edit-$clean",
			'wrapper_id' => "$clean-wrapper",
		);

		// Field help
		if ($field['help'])
		{
			$field["help_{$field['help_type']}"] = str_replace(array('{type}', '{help}'), array($field['help_type'], $field['help']), self::$_help_template);
		}

		// Field error
		// @TODO: Errors overwrite help messages, fix this behavior
		if (function_exists('form_error') AND form_error($field['name']))
		{
			$field['error'] = ' error'; // The wrapper flag class
			$field["help_{$field['error_type']}"] = str_replace(array('{type}', '{help}'), array($field['error_type'], form_error($field['name'])), self::$_help_template);
		}

		// Field icon
		$field['icon'] AND $field['icon'] = '<span class="add-on">' . self::_render_icon($field['icon']) . '</span>';

		// Field element type
		isset($field['element']) OR
			$field['element'] = in_array($field['type'], self::$_input_fields) ? 'input' : $field['type'];

		// @TODO: Improve this
		// Prepare field value, exclude buttons, checkboxes and radios
		if (isset($field['value']) AND $field['type'] != 'button' AND !isset($field['toggle']))
		{
			$field['value'] = self::value($field['name'], $field['value']);
		}

		// Boolean flags
		$field['readonly'] = $field['readonly'] ? ' readonly' : '';
		$field['disabled'] = $field['disabled'] ? ' disabled' : '';

		// Mark field as required if it's so
		$field['required'] = strpos($field['rules'], 'required') === FALSE ? '' : $field['required_markup'];

		// Class strings
		// @TODO: Fix form_actions. We should be able to group multiple buttons into one form_actions element
		$field['form_actions']  AND $field['form_actions']  = ' form-actions';
		$field['wrapper_class'] AND $field['wrapper_class'] = ' ' . $field['wrapper_class'];

		// Popovers
		$field['popover'] AND $field['attributes'] += array(
			'rel'                 => 'popover',
			'data-content'        => $field['popover'],
			'data-original-title' => $field['popover_title'],
		);

		// Parse extra attributes
		isset($field['attributes']) AND $field['attributes'] = _parse_form_attributes($field['attributes'], array());

		// Parse inline fields in suffix
		if (isset($field['suffix']) AND is_array($field['suffix']))
		{
			$field['suffix'] = self::_render_fields($field['suffix']);
		}

		// As well as in suffix_inline
		if (isset($field['suffix_inline']) AND is_array($field['suffix_inline']))
		{
			$field['suffix_inline'] = self::_render_fields($field['suffix_inline']);
			$field['input_append'] = ' input-append';
		}

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
	 * Renders a a subform array into HTML.
	 *
	 * @return string
	 */
	private static function _render_subform($field, $name = FALSE, $multistep = FALSE)
	{
		// Load subform array
		$subform = self::load($field['subform']);

		return self::_render_fields($subform, FALSE);
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
						'type'        => 'button',
						'element'     => 'a',
						'class'       => "btn btn-info pull-right accordion-$nav",
						'value'       => $accordion["nav_{$nav}_label"],
						'attributes'  => array(
							'data-toggle' => 'collapse',
							'data-parent' => '#form-accordion',
							'data-target' => '#' . $accordion["nav_$nav"],
						),
					), "nav_button_$nav", FALSE);
				}
			}
		}

		// Active step? add the class
		$accordion['active'] AND $accordion['active'] = ' in';

		return self::_render_field($accordion, $name, self::$_accordion_template);
	}

	//--------------------------------------------------------------------

	/**
	 * Renders a textarea element.
	 *
	 * @return string
	 */
	private static function _render_textarea($field, $name = FALSE, $multistep = FALSE)
	{
		return self::_render_field($field, $name, self::$_textarea_template);
	}

	//--------------------------------------------------------------------

	/**
	 * Renders a toggle element (checkbox/radio).
	 *
	 * @return string
	 */
	private static function _render_toggle($field, $name = FALSE, $multistep = FALSE)
	{
		// Checkbox/radio value setup
		if (self::value_toggled($name, $field['value'], $field['checked']))
		{
			$field['checked'] = 'checked';
		}

		return self::_render_field($field, $name, self::$_toggle_template);
	}

	//--------------------------------------------------------------------

	/**
	 * Renders a group of toggle element (checkbox/radio).
	 *
	 * @return string
	 */
	public static function _render_toggles($field, $name = FALSE, $multistep = FALSE)
	{
		$toggles = '';

		// Load checkbox/radio data model if set
		if (isset($field['data']))
		{
			$field['options'] = self::_call_data_model($field['data']);
		}

		// Render each option as a single checkbox/radio element
		foreach ($field['options'] as $value => $label)
		{
			$toggles .= self::_render_toggle(array(
				'type'    => 'toggles',
				'value'   => $value,
				'label'   => $label,
				'toggle'  => $field['toggle'],
				'checked' => isset($field['value']) ? in_array($value, $field['value']) : FALSE,
			), $name);
		}

		$field['options'] = $toggles;
		unset($field['value']);

		return self::_render_field($field, $name, self::$_toggles_template);
	}

	//--------------------------------------------------------------------

	/**
	 * Renders a button element.
	 *
	 * @return string
	 * @todo   Ensure proper button classes
	 */
	private static function _render_button($button, $name = FALSE, $multistep = FALSE)
	{
		// Rendering A, DIV or SPAN elements as buttons?
		isset($button['element']) OR $button['element'] = 'button';

		// Add type if it's a button
		$button['element'] == 'button' AND $button['element'] .= ' type="submit"';

		// Default icon set?
		if (isset($button['icon']))
		{
			$button['icon_prefix'] = $button['icon'];
			unset($button['icon']);
		}

		// Inline icon: prefix
		isset($button['icon_prefix']) AND
			$button['value'] = self::_render_icon($button['icon_prefix']) . $button['value'];

		// Inline icon: suffix
		isset($button['icon_suffix']) AND
			$button['value'] .= self::_render_icon($button['icon_suffix']);

		// Inline element?
		$template = isset($button['inline'])
			? self::$_button_inline_template
			: self::$_button_template;

		return self::_render_field($button, $name, $template);
	}

	//--------------------------------------------------------------------

	/**
	 * Renders a markup element.
	 *
	 * @return string
	 */
	private static function _render_markup($markup, $name = FALSE, $multistep = FALSE, $element = 'button')
	{
		// Load and return the view if it's set to
		if (isset($markup['view']))
		{
			// View data may come in various formats, here they are:
			if (isset($markup['data']) AND is_array($markup['data']))
			{
				// 1. Flat arrays: In this case we should fetch values from form defaults
				foreach ($markup['data'] as $index => $key)
				{
					if (is_numeric($index) AND isset(self::$_values[$key]))
					{
						unset($markup['data'][$index]);
						$markup['data'][$key] = self::$_values[$key];
					}
				}
			}
			// 2. No data set, fetch from defaults if available
			else
			{
				$markup['data'] = isset(self::$_values[$name]) ? array($name => self::$_values[$name]) : '';
			}

			// 3. Otherwise there should be a key/valued array in data
			return self::$CI->load->view($markup['view'], $markup['data'], TRUE);
		}

		// Otherwise check for raw HTML in 'value'
		return isset($markup['value']) ? $markup['value'] : '';
	}

	//--------------------------------------------------------------------

	/**
	 * Renders a select element.
	 *
	 * @return string
	 */
	private static function _render_dropdown($dropdown, $name, $multistep = FALSE)
	{
		// Load dropdown data model if set.
		isset($dropdown['data']) AND $dropdown['options'] = self::_call_data_model($dropdown['data']);

		// Prepare value
		isset($dropdown['value']) OR $dropdown['value'] = '';
		$dropdown['value'] = self::value($name, $dropdown['value']);

		// Handle dropdown attributes
		isset($dropdown['attributes']) OR $dropdown['attributes'] = array();
		$dropdown['attributes'] += array('id' => 'edit-' . self::clean($name));

		// Helps overcome dependent dropdowns issue on prepopulated options
		(isset($dropdown['cached_value']) AND $dropdown['cached_value'])
			AND $dropdown['attributes'] += array('cached_value' => $dropdown['value']);

		// Parse attributes into string
		$attributes = _parse_form_attributes($dropdown['attributes'], array());

		// Handle multiselect dropdowns
		$helper = (isset($dropdown['multiselect']) AND $dropdown['multiselect']) ? 'form_multiselect' : 'form_dropdown';

		// Render dropdown select and option elements
		isset($dropdown['options']) AND
			$dropdown['options'] = $helper($name, $dropdown['options'], $dropdown['value'], $attributes);

		// Inline element?
		$template = isset($dropdown['inline'])
			? self::$_dropdown_inline_template
			: self::$_dropdown_template;

		return self::_render_field($dropdown, $name, $template);
	}

	//--------------------------------------------------------------------

	/**
	 * Renders an icon element.
	 *
	 * @return string
	 */
	public static function _render_icon($icon)
	{
		return str_replace('{icon}', $icon, self::$_icon_template);
	}

	//--------------------------------------------------------------------

	/**
	 * Renders a hidden element.
	 *
	 * @return string
	 */
	public static function _render_hidden($field, $name, $multistep = FALSE)
	{
		$hidden = str_replace('{name}', $name, self::$_hidden_template);
		return str_replace('{value}', $field['value'], $hidden);
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
	// Misc Helpers
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
				$rules = array_merge($rules, self::_validate_rules($field));
				continue;
			}

			// Extract rules from subforms
			if (isset($field['type']) AND $field['type'] == 'subform')
			{
				$rules = array_merge($rules, self::_validate_rules(self::load($field['subform'])));
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

	//--------------------------------------------------------------------

	/**
	 * Calls form field data model method.
	 */
	private static function _call_data_model($data)
	{
		// Get model data
		list($model, $method) = explode('.', $data);
		$model_name = basename($model);

		// Get method args, if any
		preg_match('#\((.*?)\)#', $method, $match);
		$arg    = isset($match[1]) ? $match[1] : FALSE;
		strpos($method, '(') !== FALSE AND $method = strstr($method, '(', TRUE);

// var_dump($data, 'model: '.$model_name, 'merhod: '.$method, 'arg: '.$arg);

		// Typecast boolean args
		if (strtolower($arg) == 'true' OR strtolower($arg) == 'false')
		{
			$arg = (bool) $arg;
		}
// var_dump('booled arg:', $arg);
		// Load model, if not available
		isset(self::$CI->$model_name) OR self::$CI->load->model($model, $model_name);

		// Call the data method of provided model
		// This will overwrite field "options" property
		return method_exists(self::$CI->$model_name, $method)
			? self::$CI->$model_name->$method($arg)
			: array();
	}

	//--------------------------------------------------------------------

}
// End of Form class

/* End of file Form.php */
/* Location: ./application/libraries/Form.php */