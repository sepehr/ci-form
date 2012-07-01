<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter sample form definition file.
 *
 * @package		CodeIgniter
 * @author		Sepehr Lajevardi <me@sepehr.ws>
 * @copyright	Copyright (c) 2012 Sepehr Lajevardi.
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		https://github.com/sepehr/ci-form
 * @version 	Version 1.0
 * @filesource
 */

/*
| -------------------------------------------------------------------------
| Example form definition array.
| -------------------------------------------------------------------------
| Possible element types are:
| - text
| - email
| - password
| - url
| - telephone
| - number
| - color
| - search
| - date
| - dropdown
| - button
| - slide
| - hidden
| - fieldset
|
| Possible element attributes are:
| - type            (default: text)
| - label
| - value
| - required        (TRUE|FALSE)
| - required_markup (default: <strong>*</strong>)
| - placeholder
| - icon            (bootstrap icon name)
| - class           (default: form-control)
| - wrapper_class
| - prefix          (markup)
| - suffix          (markup)
| - extra           (attribute string)
| - disabled        (TRUE|FALSE)
| - readonly        (TRUE|FALSE)
| - help            (markup)
| - help_type       (inline|block)
| - error_type      (inline|block)
| - render_class    (custom renderrer class name)
| - render_callback (custom renderrer callback name)
| - form_actions    (wraps buttons in a .form-action element, button only) @TODO Fix to be able to group buttons
| - options         (key array of dropdown options as per required by form_dropdown() helper)
|
| Possible form attributes are:
| - action
| - prefix          (markup)
| - suffix          (markup)
| - attributes      (array)
| - multipart       (TRUE|FALSE)
| - multistep       (TRUE|FALSE) (requires bootstrap-accordion.js)
|
| NOTE: If multistep option is set to TRUE, each step fields should be wrapped into separate fieldsets.
|       Also fieldsets must provide their corresponding accordion attributes as a subarray. e.g.:
|
|		$config['form_name']['form_step1'] = array(
|			'type'      => 'fieldset',
|			// Default accordion tab
|			'active'    => TRUE,
|			'accordion' => array(
|				'title'      => 'Form Step I',
|               'target_id'  => 'step1-accordion-body',
|				'attributes' => array('id' => 'step1-accordion'),
|			),
|		);
|
| Possible fieldset attributes are: (fields should be embedded as subarray elements)
| - legend
| - prefix          (markup)
| - suffix          (markup)
| - attributes      (array)
| - active          (TRUE|FALSE) (multistep only)
| - accordion       (array)      (multistep only)
|
| Possible accordion attributes are: (fields should be embedded as subarray elements)
| - title           (default: fieldset legend)
| - body            (will be set automatically, if multistep)
| - attributes      (array)
| - target_id       (element id of target accordion body without #)
| - nav_buttons     (TRUE|FALSE) (creates navigation buttons for each accordion)
| - nav_next        (element id of next accordion body to goto, if next button clicked)
| - nav_prev        (element id of previous accordion body to goto, if previous button clicked)
| - nav_next_label  (default: Next)
| - nav_prev_label  (default: Previous)
|
*/
$config = array();

$config['form_example'] = array(
	// Form:
	'action'        => '',
	'prefix'        => '',
	'suffix'        => '',
	'multistep'     => TRUE,
	'multistep_nav' => TRUE,
	'multipart'     => FALSE,
	'attributes'    => array(
		'class' => 'login-form',
		'id' => 'jobseeker-login-form'
	),

	// Step1 fieldset:
	'step1' => array(
		'type'   => 'fieldset',
		'legend' => 'Personal information',
		'active' => TRUE,
		'prefix' => '',
		'suffix' => '',
		'attributes' => array(
			'id'    => 'step-1',
			'class' => 'form-step-fieldset',
		),
		'accordion'  => array(
			'title'          => 'Registration Step I',
			'target_id'      => 'step1-accordion-body',
			'nav_buttons'    => TRUE,
			'nav_next'       => 'step2-accordion-body',
			'nav_next_label' => 'Next',
		),

		// Firstname:
		'firstname' => array(
			'type'        => 'text',
			'label'       => 'Firstname',
			'icon'        => 'user',
			'placeholder' => 'e.g. Your firstname',
			'rules'       => 'required|alpha|min_length[3]|max_length[20]',
		),
		// Lastname:
		'lastname' => array(
			'type'        => 'text',
			'label'       => 'Lastname',
			'icon'        => 'user',
			'placeholder' => 'e.g. Your firstname',
			'rules'       => 'required|alpha|min_length[3]|max_length[20]',
		),
		// Dropdown:
		'dropdown' => array(
			'type'        => 'dropdown',
			'label'       => 'Your favorite',
			'icon'        => 'eye-open',
			'value'       => 'test-option1',
			'options'     => array(
				''            => 'Please select',
				'DMT'         => 'C12H16N2',
				'LSD'         => 'C20H25N3O',
				'Psilocybin ' => 'C12H17N2O4P ',
			),
		),
	),

	// Step2 fieldset:
	'step2' => array(
		'type'   => 'fieldset',
		'legend' => 'Account information',
		'prefix' => '',
		'suffix' => '',
		'attributes' => array(
			'id'    => 'step-2',
			'class' => 'form-step-fieldset',
		),
		'accordion'  => array(
			'title'          => 'Registration Step II',
			'target_id'      => 'step2-accordion-body',
			'nav_buttons'    => TRUE,
			'nav_prev'       => 'step1-accordion-body',
			'nav_prev_label' => 'Previous',
		),

		// Email:
		'email' => array(
			'type'        => 'email',
			'label'       => 'E-mail',
			'icon'        => 'envelope',
			'placeholder' => 'e.g. name@example.com',
			'rules'       => 'required|valid_email',
		),
		// Password:
		'pass' => array(
			'type'        => 'password',
			'label'       => 'Password',
			'icon'        => 'lock',
			'placeholder' => 'Your password...',
			'rules'       => 'required|min_length[6]|matches[pass_confirm]'
		),
		// Password confirmation:
		'pass_confirm' => array(
			'type'        => 'password',
			'label'       => 'Confirm password',
			'icon'        => 'ok',
			'placeholder' => 'Confirm password...',
		),
	),

	// Submit:
	'submit' => array(
		'type'         => 'submit',
		'value'        => 'Login',
		'class'        => 'btn btn-primary',
		'form_actions' => TRUE,
	),

);

/* End of file form_example.php */
/* Location: ./application/config/form_example.php */
