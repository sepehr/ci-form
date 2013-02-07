#CodeIgniter Form API
The library empowers CodeIgniter with a clean, powerful, drupal-inspired and bootstrap-ready form API. The structure lets you define your form structure, fields and validation rules in separate files, so it's easier to develop and will give you pretty cleaner controller and view files at the end.

The code is very alpha for now and it's a work in progress.

## Features
* Form definition arrays (Drupal-like) as separate config files.
* Form validation integration.
* Support for inline subform definition.
* Support for form specific custom validation errors.
* Support for field data callbacks (Model integration).
* Support for raw makup fields.
* Ability to fetch subviews.
* HTML 5 ready.
* Twitter Bootstrap ready.
* Client-side multistep forms.
* Works great with Modular Extensions and Bonfire's Template library.
* Support for Drupal-like `form_alter` callbacks via CI Events library.
* Many more...

## Installation
Move each file to its corresponding directory in CodeIgniter `application/` directory and you're simply done.

## Basic usage
```php
	/**
	 * Sample controller.
	 */
	class Users extends CI_Controller {

		public function register()
		{
			// Make sure to load/autoload the library
			$this->load->library('Form');

			// Validate form values against its rules
			// You can also specify the module name. e.g. "users/form_example"
			if ( Form::validate('form_example') )
			{
				// Model interaction, etc...

				// Flush form data if required, this will save us
				// from expensive CI self redirects.
				// @TODO: Implement PRG pattern helpers into the library.
				Form::flush();
			}

			// Form is not submitted/validated yet
			else
			{
				// Set form defaults (update forms)
				$user = (array) $this->user_model->get();
				Form::set_defaults($user);

				// Generate the form markup
				$form = Form::get('form_example');

				// And pass it to the view
				// In the view file you just need to echo the $form
				$this->load->view('users_register', array('form' => $form));
			}
		}

	}
```

## Basic form definiton
```php
$config['form_login'] = array(

	//--------------------------------------------------------------------
	// Custom validation error messages
	//--------------------------------------------------------------------
	'errors' => array(
		'required'                    => 'This field is required.',
		'callback__custom_validation' => 'Custom validation error message.',
	),

	//--------------------------------------------------------------------
	// Form definition, support for client-side multistep forms:
	//--------------------------------------------------------------------
	'action'        => '',
	'prefix'        => '',
	'suffix'        => '',
	'multistep'     => TRUE,
	'multistep_nav' => TRUE,
	'multipart'     => FALSE,
	'attributes'    => array(
		'class' => 'register-form',
		'id'    => 'example-register-form'
	),

	//--------------------------------------------------------------------
	// Fieldset/accordion definition:
	//--------------------------------------------------------------------
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

		//--------------------------------------------------------------------
		// Firstname: Sample text field
		//--------------------------------------------------------------------
		'firstname' => array(
			'type'          => 'text',
			'label'         => 'Firstname',
			'icon'          => 'user',
			'placeholder'   => 'e.g. Your firstname',
			'rules'         => 'required|alpha|min_length[3]|max_length[20]',
			// Accepts merkup:
			'after'         => '',
			'before'        => '',
			'prefix'        => '',
			'suffix'        => '',
			// Also accepts field definition arrays:
			'suffix_inline' => '',

		),

		//--------------------------------------------------------------------
		// Lastname: Sample text field
		//--------------------------------------------------------------------
		'lastname' => array(
			'type'        => 'text',
			'label'       => 'Lastname',
			'icon'        => 'user',
			'placeholder' => 'e.g. Your firstname',
			'rules'       => 'required|alpha|min_length[3]|max_length[20]',
		),

		//--------------------------------------------------------------------
		// Subform:
		//--------------------------------------------------------------------
		'credit_subform' => array(
			'type'    => 'subform',
			'subform' => '[my_module/]subform_credit_card'
		),

		//--------------------------------------------------------------------
		// Street address: Inline fields
		//--------------------------------------------------------------------
		'street' => array(
			'type'        => 'text',
			'label'       => 'Street address',
			'icon'        => 'road',
			'placeholder' => 'e.g. 8 High St.',
			'rules'       => 'trim|required',

			// Inline street address 2:
			'suffix_inline'      => array(
				'street2' => array(
					'type'        => 'text',
					'icon'        => 'road',
					'placeholder' => '2nd line...',
					'rules'       => 'trim',
					'class'       => 'span1',
					'inline'      => TRUE,
				),
			),
		),

		//--------------------------------------------------------------------
		// Country: Data callbacks, model interaction:
		//--------------------------------------------------------------------
		'country' => array(
			'type'       => 'dropdown',
			'label'      => 'Country',
			'icon'       => 'map-marker',
			// Custom validation rule
			'rules'      => 'callback__check_country[county]',
			// Model data callback
			'data'       => 'my_model.get_countries',
		),

		//--------------------------------------------------------------------
		// County: Data callbacks, module model interaction with args:
		//--------------------------------------------------------------------
		'county' => array(
			'type'         => 'dropdown',
			'label'        => 'County',
			'icon'         => 'map-marker',
			'rules'        => 'required',
			'data'         => 'my_module/my_model.get_counties(TRUE)',
			'cached_value' => TRUE,
		),

		//--------------------------------------------------------------------
		// Inline views:
		//--------------------------------------------------------------------
		'sectors' => array(
			'type'  => 'markup',
			'view'  => '[my_module/]markup_sectors',
			// Passing data keys, values will be fetched from defaults,
			// We can also set values here:
			'data'  => array('sectors', 'user_sectors'),
			'rules' => 'required|callback__check_sectors',
		),

	), // Step1 fieldset

	// Other fieldsets & fields...
);
```

For more information on form definition array formats, please read the `config/form_example.php` file.

### TODOs
* Debug mode
* Dependent dropdowns
* Custom field renderrer callbacks
* Move templates into config files
* Form caching (CI cache driver integration)
* Restructure as a standalone PHP library.
* Cleanup!
* Support for PRG pattern using CI sessions.
* Add more examples.