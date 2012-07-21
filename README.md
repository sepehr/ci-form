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
			// You can also specify the module name. e.g. "users/form_register"
			if ( Form::validate('form_example') )
			{
				// Model interaction here...
				// And maybe redirect user...
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
	// Errors:
	'errors' => array(
		'callback__custom_validation' => 'Custom validation error message.',
	),

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

	), // Step1 fieldset

	// Other fieldsets & fields...
);
```

For more information on form definition array formats, please read the `config/form_example.php` file.

### TODOs
* Debug mode
* Dependent dropdowns
* Move templates into config files
* Form caching (CI cache driver integration)