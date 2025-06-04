<?php

namespace Frontend\Form;

use Cake\Form\Form;
use Cake\Form\Schema;
use Cake\Validation\Validator;
use Cake\Datasource\ConnectionManager;
use Cake\Core\Configure;

// require
switch(Configure::read('newsletter.type')){
    case "tm5":
    case "tm6":
    case "maileon":
        require_once(ROOT . DS .  'vendor' . DS  . Configure::read('newsletter.type') . DS . 'interface.php');
        break;
}

class FrontendForm extends Form
{

    var $type;
    var $captcha;
    var $connection;
    var $interface;

    public function __construct($type, $captcha = false)
    {
        $this->type = $type;
        $this->captcha = $captcha;
        $this->connection = ConnectionManager::get('default');

        // init interface
        switch(Configure::read('newsletter.type')){
            case "tm5":
            case "tm6":
            case "maileon":
                $cn = '\\' . strtoupper(Configure::read('newsletter.type')) . 'Interface';
                if(class_exists(strtoupper(Configure::read('newsletter.type')) . 'Interface')){
                    $this->interface = new $cn(Configure::read());
                }else{
                    echo __METHOD__ . ": interface class not found!"; exit;
                }
                break;
        }

    }

    protected function _buildSchema(Schema $schema)
    {
        return $this->{'_buildSchema' . ucfirst($this->type) }($schema);
    }

    protected function _buildValidator(Validator $validator)
    {
        return $this->{'_buildValidator' . ucfirst($this->type) }($validator);
    }

    protected function _execute(array $data)
    {
        return true;
    }

    // request form

    protected function _buildSchemaRequest(Schema $schema)
    {
        return $schema
            ->addField('arrival', ['type' => 'string'])
            ->addField('departure', ['type' => 'string'])
            ->addField('salutation', ['type' => 'select'])
            ->addField('title', ['type' => 'string'])
            ->addField('firstname', ['type' => 'string'])
            ->addField('lastname', ['type' => 'string'])
            ->addField('email', ['type' => 'string'])
            ->addField('address', ['type' => 'string'])
            ->addField('zip', ['type' => 'string'])
            ->addField('country', ['type' => 'string'])
            ->addField('phone', ['type' => 'string'])
            ->addField('message', ['type' => 'text'])
            ->addField('newsletter', ['type' => 'checkbox'])
            ->addField('privacy', ['type' => 'checkbox'])
            ->addField('captcha', ['type' => 'string']);
    }

    protected function _buildValidatorRequest(Validator $validator)
    {
        $validator
            ->notEmpty('arrival', __d('fe', 'The arrival date is required'))
            ->notEmpty('departure', __d('fe', 'The departure date is required'))
            ->notEmpty('salutation', __d('fe', 'Please select a salutation'))
            ->notEmpty('firstname', __d('fe', 'A firstname is required'))
            ->notEmpty('lastname', __d('fe', 'A lastname is required'))
            ->notEmpty('email', __d('fe', 'An email address is required'))
            ->add('email', 'format', [
                'rule' => 'email',
                'message' => __d('fe', 'A valid email address is required'),
            ])
            ->add('privacy', 'check', [
                'rule' => [$this, 'privacy'],
                'message' => __d('fe', 'You must agree to the data protection plan')
            ])
            ->notEmpty('captcha', __d('fe', 'Incorrect answer'))
            ->add('captcha', 'check', [
                'rule' => [$this, 'captcha'],
                'message' => __d('fe', 'Incorrect answer')
            ]);

        // ages
        $ageValidator = new Validator();
        $ageValidator
            ->notEmpty('age', __d('fe', 'Please enter the children age'))
            ->add('age', 'check', [
                'rule' => 'naturalNumber',
                'message' => __d('fe', 'Invalid number')
            ]);
        $validator->addNestedMany('ages', $ageValidator);

        // rooms
        $roomValidator = new Validator();
        $roomValidator
        ->allowEmpty('adults')
        ->add('adults', 'check', [
            'rule' => 'numeric',
            'pass' => [true],
            'message' => __d('fe', 'Invalid number')
        ])
        ->allowEmpty('children')
        ->add('children', 'check', [
            'rule' => 'numeric',
            'message' => __d('fe', 'Invalid number')
        ]);

        // childages
        $childAgeValidator = new Validator();
		$childAgeValidator->notEmpty('age', __d('fe', 'The childage is required'));
		$roomValidator->addNestedMany('ages', $childAgeValidator);

		$validator->addNestedMany('rooms', $roomValidator);

        return $validator;
    }

	//table reservation form
    protected function _buildSchemaTable(Schema $schema)
    {
        return $schema
            ->addField('salutation', ['type' => 'select'])
            ->addField('title', ['type' => 'string'])
            ->addField('firstname', ['type' => 'string'])
            ->addField('lastname', ['type' => 'string'])
            ->addField('email', ['type' => 'string'])
            ->addField('address', ['type' => 'string'])
            ->addField('zip', ['type' => 'string'])
            ->addField('country', ['type' => 'string'])
            ->addField('phone', ['type' => 'string'])
            ->addField('date', ['type' => 'string'])
            ->addField('time', ['type' => 'string'])
            ->addField('persons', ['type' => 'select'])
            ->addField('occasion', ['type' => 'string'])
            ->addField('wishes', ['type' => 'text'])
            ->addField('newsletter', ['type' => 'checkbox'])
            ->addField('privacy', ['type' => 'checkbox'])
            ->addField('captcha', ['type' => 'string']);
    }

    protected function _buildValidatorTable(Validator $validator)
    {
        return $validator
            ->notEmpty('salutation', __d('fe', 'Please select a salutation'))
            ->notEmpty('firstname', __d('fe', 'A firstname is required'))
            ->notEmpty('lastname', __d('fe', 'A lastname is required'))
            ->notEmpty('email', __d('fe', 'An email address is required'))
            ->add('email', 'format', [
                'rule' => 'email',
                'message' => __d('fe', 'A valid email address is required'),
            ])
            ->notEmpty('phone', __d('fe', 'A phone number is required'))
            ->notEmpty('date', __d('fe', 'A date is required'))
            ->notEmpty('time', __d('fe', 'A time is required'))
            ->add('time', 'format', [
                'rule' => 'time',
                'message' => __d('fe', 'Please enter the time in the following format: 08:45')
            ])
            ->notEmpty('persons', __d('fe', 'Please select the number of persons'))
            ->add('privacy', 'check', [
                'rule' => [$this, 'privacy'],
                'message' => __d('fe', 'You must agree to the data protection plan')
            ])
            ->notEmpty('captcha', __d('fe', 'Incorrect answer'))
            ->add('captcha', 'check', [
                'rule' => [$this, 'captcha'],
                'message' => __d('fe', 'Incorrect answer')
            ]);

    }

    // brochure form

    protected function _buildSchemaBrochure(Schema $schema)
    {
        return $schema
            ->addField('salutation', ['type' => 'select'])
            ->addField('title', ['type' => 'string'])
            ->addField('firstname', ['type' => 'string'])
            ->addField('lastname', ['type' => 'string'])
            ->addField('email', ['type' => 'string'])
            ->addField('address', ['type' => 'string'])
            ->addField('zip', ['type' => 'string'])
            ->addField('city', ['type' => 'string'])
            ->addField('country', ['type' => 'string'])
            ->addField('phone', ['type' => 'string'])
            ->addField('interests', ['type' => 'checkbox'])
            ->addField('message', ['type' => 'text'])
            ->addField('newsletter', ['type' => 'checkbox'])
            ->addField('privacy', ['type' => 'checkbox'])
            ->addField('captcha', ['type' => 'string']);
    }

    protected function _buildValidatorBrochure(Validator $validator)
    {
        return $validator
            ->notEmpty('salutation', __d('fe', 'Please select a salutation'))
            ->notEmpty('firstname', __d('fe', 'A firstname is required'))
            ->notEmpty('lastname', __d('fe', 'A lastname is required'))
            ->notEmpty('email', __d('fe', 'An email address is required'))
            ->add('email', 'format', [
                'rule' => 'email',
                'message' => __d('fe', 'A valid email address is required'),
            ])
            ->notEmpty('address', __d('fe', 'An address is required'))
            ->notEmpty('zip', __d('fe', 'A zip code is required'))
            ->notEmpty('city', __d('fe', 'A city is required'))
            ->notEmpty('country', __d('fe', 'A country is required'))
            ->notEmpty('interests', __d('fe', 'At least one option is required'))
            ->add('privacy', 'check', [
                'rule' => [$this, 'privacy'],
                'message' => __d('fe', 'You must agree to the data protection plan')
            ])
            ->notEmpty('captcha', __d('fe', 'Incorrect answer'))
            ->add('captcha', 'check', [
                'rule' => [$this, 'captcha'],
                'message' => __d('fe', 'Incorrect answer')
            ]);

    }

    // callback form

    protected function _buildSchemaCallback(Schema $schema)
    {
        return $schema
            ->addField('name', ['type' => 'string'])
            ->addField('phone', ['type' => 'string'])
            ->addField('message', ['type' => 'text'])
            ->addField('date', ['type' => 'string'])
            ->addField('time', ['type' => 'string'])
            ->addField('privacy', ['type' => 'checkbox'])
            ->addField('captcha', ['type' => 'string']);
    }

    protected function _buildValidatorCallback(Validator $validator)
    {
        return $validator
            ->notEmpty('name', __d('fe', 'A name is required'))
            ->notEmpty('phone', __d('fe', 'A phone number is required'))
            ->notEmpty('date', __d('fe', 'A date is required'))
            ->notEmpty('time', __d('fe', 'A time is required'))
            ->add('time', 'format', [
                'rule' => 'time',
                'message' => __d('fe', 'Please enter the time in the following format: 08:45')
            ])
            ->add('privacy', 'check', [
                'rule' => [$this, 'privacy'],
                'message' => __d('fe', 'You must agree to the data protection plan')
            ])
            ->notEmpty('captcha', __d('fe', 'Incorrect answer'))
            ->add('captcha', 'check', [
                'rule' => [$this, 'captcha'],
                'message' => __d('fe', 'Incorrect answer')
            ]);

    }

    // coupon form

    protected function _buildSchemaCoupon(Schema $schema)
    {
        return $schema
            ->addField('salutation', ['type' => 'select'])
            ->addField('title', ['type' => 'string'])
            ->addField('firstname', ['type' => 'string'])
            ->addField('lastname', ['type' => 'string'])
            ->addField('email', ['type' => 'string'])
            ->addField('address', ['type' => 'string'])
            ->addField('zip', ['type' => 'string'])
            ->addField('city', ['type' => 'string'])
            ->addField('country', ['type' => 'string'])
            ->addField('phone', ['type' => 'string'])
            ->addField('message', ['type' => 'text'])
            ->addField('salutation_recipient', ['type' => 'select'])
            ->addField('title_recipient', ['type' => 'string'])
            ->addField('firstname_recipient', ['type' => 'string'])
            ->addField('lastname_recipient', ['type' => 'string'])
            ->addField('arrival', ['type' => 'string'])
            ->addField('departure', ['type' => 'string'])
            ->addField('adults', ['type' => 'string'])
            ->addField('children', ['type' => 'string'])
            ->addField('comment', ['type' => 'text'])
            ->addField('value', ['type' => 'string'])
            ->addField('newsletter', ['type' => 'checkbox'])
            ->addField('privacy', ['type' => 'checkbox'])
            ->addField('captcha', ['type' => 'string']);
    }

    protected function _buildValidatorCoupon(Validator $validator)
    {
        return $validator
            ->notEmpty('salutation', __d('fe', 'Please select a salutation'))
            ->notEmpty('firstname', __d('fe', 'A firstname is required'))
            ->notEmpty('lastname', __d('fe', 'A lastname is required'))
            ->notEmpty('email', __d('fe', 'An email address is required'))
            ->add('email', 'format', [
                'rule' => 'email',
                'message' => __d('fe', 'A valid email address is required'),
            ])
            ->notEmpty('message', __d('fe', 'A message is required'))
            ->add('message', 'length', [
                'rule' => ['minLength', 10],
                'message' => __d('fe', 'Your message must be at least %s characters long', 10)
            ])
            ->notEmpty('salutation_recipient', __d('fe', 'Please select a salutation'))
            ->notEmpty('firstname_recipient', __d('fe', 'A firstname is required'))
            ->notEmpty('lastname_recipient', __d('fe', 'A lastname is required'))
            ->notEmpty('coupon_type', __d('fe', 'A type is required'))

            // option 1
            ->notEmpty('arrival', __d('fe', 'An arrival date is required'), function($context){
                return array_key_exists('coupon_type', $context['data']) && $context['data']['coupon_type'] == 'vacation' ? true : false;
            })
            ->notEmpty('departure', __d('fe', 'A departure date is required'), function($context){
                return array_key_exists('coupon_type', $context['data']) && $context['data']['coupon_type'] == 'vacation' ? true : false;
            })
            ->notEmpty('adults', __d('fe', 'The number of adults is required'), function($context){
                return array_key_exists('coupon_type', $context['data']) && $context['data']['coupon_type'] == 'vacation' ? true : false;
            })
            ->add('adults', 'check', [
                'rule' => 'naturalNumber',
                'pass' => [true],
                'message' => __d('fe', 'Invalid number')
            ])
            ->allowEmpty('children')
            ->add('children', 'check', [
                'rule' => 'naturalNumber',
                'message' => __d('fe', 'Invalid number')
            ])

            // option 2
            ->notEmpty('value', __d('fe', 'A value is required'), function($context){
                return array_key_exists('coupon_type', $context['data']) && $context['data']['coupon_type'] == 'value' ? true : false;
            })
            ->add('value', 'format', [
                'rule' => 'money',
                'message' => 'Please provide a valid number',
                'on' => function ($context) {
                    return array_key_exists('coupon_type', $context['data']) && $context['data']['coupon_type'] == 'value' ? true : false;
                }
            ])

            ->add('privacy', 'check', [
                'rule' => [$this, 'privacy'],
                'message' => __d('fe', 'You must agree to the data protection plan')
            ])
            ->notEmpty('captcha', __d('fe', 'Incorrect answer'))
            ->add('captcha', 'check', [
                'rule' => [$this, 'captcha'],
                'message' => __d('fe', 'Incorrect answer')
            ]);

    }

    // contact form

    protected function _buildSchemaContact(Schema $schema)
    {
        return $schema
            ->addField('salutation', ['type' => 'select'])
            ->addField('title', ['type' => 'string'])
            ->addField('firstname', ['type' => 'string'])
            ->addField('lastname', ['type' => 'string'])
            ->addField('email', ['type' => 'string'])
            ->addField('address', ['type' => 'string'])
            ->addField('zip', ['type' => 'string'])
            ->addField('country', ['type' => 'string'])
            ->addField('phone', ['type' => 'string'])
            ->addField('message', ['type' => 'text'])
            ->addField('newsletter', ['type' => 'checkbox'])
            ->addField('privacy', ['type' => 'checkbox'])
            ->addField('captcha', ['type' => 'string']);
    }

    protected function _buildValidatorContact(Validator $validator)
    {
        return $validator
            // ->notEmpty('salutation', __d('fe', 'Please select a salutation'))
            ->notEmpty('firstname', __d('fe', 'A firstname is required'))
            ->notEmpty('lastname', __d('fe', 'A lastname is required'))
            ->notEmpty('email', __d('fe', 'An email address is required'))
            ->add('email', 'format', [
                'rule' => 'email',
                'message' => __d('fe', 'A valid email address is required'),
            ])
            // ->notEmpty('phone', __d('fe', 'A phone number is required'))
            ->notEmpty('message', __d('fe', 'A message is required'))
            ->add('message', 'length', [
                'rule' => ['minLength', 10],
                'message' => __d('fe', 'Your message must be at least %s characters long', 10)
            ])
            ->add('privacy', 'check', [
                'rule' => [$this, 'privacy'],
                'message' => __d('fe', 'You must agree to the data protection plan')
            ])
            ->notEmpty('captcha', __d('fe', 'Incorrect answer'))
            ->add('captcha', 'check', [
                'rule' => [$this, 'captcha'],
                'message' => __d('fe', 'Incorrect answer')
            ]);

    }

    // job form

    protected function _buildSchemaJob(Schema $schema)
    {
        return $schema
            ->addField('position', ['type' => 'string'])
            ->addField('salutation', ['type' => 'select'])
            ->addField('title', ['type' => 'string'])
            ->addField('firstname', ['type' => 'string'])
            ->addField('lastname', ['type' => 'string'])
            ->addField('birthday', ['type' => 'string'])
            ->addField('citizenship', ['type' => 'string'])
            ->addField('email', ['type' => 'string'])
            ->addField('address', ['type' => 'string'])
            ->addField('zip', ['type' => 'string'])
            ->addField('country', ['type' => 'string'])
            ->addField('phone', ['type' => 'string'])
            ->addField('education', ['type' => 'text'])
            ->addField('references', ['type' => 'text'])
            ->addField('languages', ['type' => 'string'])
            ->addField('message', ['type' => 'text'])
            ->addField('privacy', ['type' => 'checkbox'])
            ->addField('captcha', ['type' => 'string']);
    }

    protected function _buildValidatorJob(Validator $validator)
    {
        return $validator
            ->notEmpty('position', __d('fe', 'A job is required'))
            ->notEmpty('salutation', __d('fe', 'Please select a salutation'))
            ->notEmpty('firstname', __d('fe', 'A firstname is required'))
            ->notEmpty('lastname', __d('fe', 'A lastname is required'))
            ->notEmpty('birthday', __d('fe', 'A birthday is required'))
            ->notEmpty('citizenship', __d('fe', 'A citizenship is required'))
            ->notEmpty('email', __d('fe', 'An email address is required'))
            ->add('email', 'format', [
                'rule' => 'email',
                'message' => __d('fe', 'A valid email address is required'),
            ])
            ->notEmpty('education', __d('fe', 'Info about education is required'))
            ->notEmpty('references', __d('fe', 'References are required'))
            ->notEmpty('languages', __d('fe', 'Languages are required'))
            ->notEmpty('message', __d('fe', 'A message is required'))
            ->add('privacy', 'check', [
                'rule' => [$this, 'privacy'],
                'message' => __d('fe', 'You must agree to the data protection plan')
            ])
            ->notEmpty('captcha', __d('fe', 'Incorrect answer'))
            ->add('captcha', 'check', [
                'rule' => [$this, 'captcha'],
                'message' => __d('fe', 'Incorrect answer')
            ]);

    }

    // last-minute

    protected function _buildSchemaLastminute(Schema $schema)
    {
        return $schema
            ->addField('id', ['type' => 'hidden'])
            ->addField('room', ['type' => 'hidden'])
            ->addField('period', ['type' => 'select'])
            ->addField('salutation', ['type' => 'select'])
            ->addField('title', ['type' => 'string'])
            ->addField('firstname', ['type' => 'string'])
            ->addField('lastname', ['type' => 'string'])
            ->addField('email', ['type' => 'string'])
            ->addField('address', ['type' => 'string'])
            ->addField('zip', ['type' => 'string'])
            ->addField('country', ['type' => 'string'])
            ->addField('phone', ['type' => 'string'])
            ->addField('message', ['type' => 'text'])
            ->addField('newsletter', ['type' => 'checkbox'])
            ->addField('privacy', ['type' => 'checkbox'])
            ->addField('captcha', ['type' => 'string']);
    }

    protected function _buildValidatorLastminute(Validator $validator)
    {
        return $validator
            ->notEmpty('id', __d('fe', 'An offer is required'))
            ->notEmpty('room', __d('fe', 'A room is required'))
            ->notEmpty('period', __d('fe', 'A period is required'))
            ->notEmpty('salutation', __d('fe', 'Please select a salutation'))
            ->notEmpty('firstname', __d('fe', 'A firstname is required'))
            ->notEmpty('lastname', __d('fe', 'A lastname is required'))
            ->notEmpty('email', __d('fe', 'An email address is required'))
            ->add('email', 'format', [
                'rule' => 'email',
                'message' => __d('fe', 'A valid email address is required'),
            ])
            ->notEmpty('message', __d('fe', 'A message is required'))
            ->add('message', 'length', [
                'rule' => ['minLength', 10],
                'message' => __d('fe', 'Your message must be at least %s characters long', 10)
            ])
            ->add('privacy', 'check', [
                'rule' => [$this, 'privacy'],
                'message' => __d('fe', 'You must agree to the data protection plan')
            ])
            ->notEmpty('captcha', __d('fe', 'Incorrect answer'))
            ->add('captcha', 'check', [
                'rule' => [$this, 'captcha'],
                'message' => __d('fe', 'Incorrect answer')
            ]);

    }

    // newsletter subscribe

    protected function _buildSchemaNewsletterSubscribe(Schema $schema)
    {
        return $schema
            ->addField('salutation', ['type' => 'select'])
            ->addField('firstname', ['type' => 'string'])
            ->addField('lastname', ['type' => 'string'])
            ->addField('email', ['type' => 'string'])
            ->addField('interests', ['type' => 'checkbox'])
            ->addField('privacy', ['type' => 'checkbox'])
            ->addField('captcha', ['type' => 'string']);
    }

    protected function _buildValidatorNewsletterSubscribe(Validator $validator)
    {
        return $validator
            ->notEmpty('salutation', __d('fe', 'Please select a salutation'))
            ->notEmpty('firstname', __d('fe', 'A firstname is required'))
            ->notEmpty('lastname', __d('fe', 'A lastname is required'))
            ->notEmpty('email', __d('fe', 'An email address is required'))
            ->add('email', 'format', [
                'rule' => 'email',
                'message' => __d('fe', 'A valid email address is required'),
            ])
            ->add('email', 'nonExistent', [
                'rule' => [$this, 'newsletterNonExistent'],
                'message' => __d('fe', 'Email address already registered')
            ])
            ->notEmpty('interests', __d('fe', 'At least one option is required'))
            ->add('privacy', 'check', [
                'rule' => [$this, 'privacy'],
                'message' => __d('fe', 'You must agree to the data protection plan')
            ])
            ->notEmpty('captcha', __d('fe', 'Incorrect answer'))
            ->add('captcha', 'check', [
                'rule' => [$this, 'captcha'],
                'message' => __d('fe', 'Incorrect answer')
            ]);

    }

    // newsletter unsubscribe

    protected function _buildSchemaNewsletterUnsubscribe(Schema $schema)
    {
        return $schema
            ->addField('email', ['type' => 'string'])
            ->addField('privacy', ['type' => 'checkbox'])
            ->addField('captcha', ['type' => 'string']);
    }

    protected function _buildValidatorNewsletterUnsubscribe(Validator $validator)
    {
        return $validator
            ->notEmpty('email', __d('fe', 'An email address is required'))
            ->add('email', 'format', [
                'rule' => 'email',
                'message' => __d('fe', 'A valid email address is required'),
            ])
            ->add('email', 'existent', [
                'rule' => [$this, 'newsletterExistent'],
                'message' => __d('fe', 'Email address not registered')
            ])
            ->add('privacy', 'check', [
                'rule' => [$this, 'privacy'],
                'message' => __d('fe', 'You must agree to the data protection plan')
            ])
            ->notEmpty('captcha', __d('fe', 'Incorrect answer'))
            ->add('captcha', 'check', [
                'rule' => [$this, 'captcha'],
                'message' => __d('fe', 'Incorrect answer')
            ]);

    }

    // member subscribe

    protected function _buildSchemaMemberSubscribe(Schema $schema)
    {
        return $schema
            ->addField('salutation', ['type' => 'select'])
            ->addField('firstname', ['type' => 'string'])
            ->addField('lastname', ['type' => 'string'])
            ->addField('email', ['type' => 'string'])
            ->addField('username', ['type' => 'string'])
            ->addField('password', ['type' => 'string'])
            ->addField('captcha', ['type' => 'string']);
    }

    protected function _buildValidatorMemberSubscribe(Validator $validator)
    {
        return $validator
            ->notEmpty('salutation', __d('fe', 'Please select a salutation'))
            ->notEmpty('firstname', __d('fe', 'A firstname is required'))
            ->notEmpty('lastname', __d('fe', 'A lastname is required'))
            ->notEmpty('email', __d('fe', 'An email address is required'))
            ->add('email', 'format', [
                'rule' => 'email',
                'message' => __d('fe', 'A valid email address is required'),
            ])
            ->add('email', 'nonExistent', [
                'rule' => [$this, 'memberNonExistent'],
                'message' => __d('fe', 'Email address already registered')
            ])
            ->notEmpty('username', __d('fe', 'An username is required'))
            ->add('username', 'length', [
                'rule' => ['lengthBetween', 6, 8],
                'message' => __d('fe', 'Your username must be between %s and %s characters long', 6, 8)
            ])
            ->add('username', 'nonExistent', [
                'rule' => [$this, 'memberNonExistent'],
                'message' => __d('fe', 'Username already exists')
            ])
            ->notEmpty('password', __d('fe', 'A password is required'))
            ->add('password', 'length', [
                'rule' => ['lengthBetween', 6, 8],
                'message' => __d('fe', 'Your password must be between %s and %s characters long', 6, 8)
            ])
            ->notEmpty('captcha', __d('fe', 'Incorrect answer'))
            ->add('captcha', 'check', [
                'rule' => [$this, 'captcha'],
                'message' => __d('fe', 'Incorrect answer')
            ]);

    }

    // member login

    protected function _buildSchemaMemberLogin(Schema $schema)
    {
        return $schema
            ->addField('username', ['type' => 'string'])
            ->addField('password', ['type' => 'string']);
    }

    protected function _buildValidatorMemberLogin(Validator $validator)
    {
        return $validator
            ->notEmpty('username', __d('fe', 'An username is required'))
            ->notEmpty('password', __d('fe', 'A password is required'))
            ->add('password', 'login', [
                'rule' => [$this, 'memberLogin'],
                'message' => __d('fe', 'Invalid username or password')
            ]);
    }

    // member forgot

    protected function _buildSchemaMemberForgot(Schema $schema)
    {
        return $schema
            ->addField('email', ['type' => 'string'])
            ->addField('password', ['type' => 'string'])
            ->addField('captcha', ['type' => 'string']);
    }

    protected function _buildValidatorMemberForgot(Validator $validator)
    {
        return $validator
            ->notEmpty('email', __d('fe', 'An email address is required'))
            ->add('email', 'format', [
                'rule' => 'email',
                'message' => __d('fe', 'A valid email address is required'),
            ])
            ->add('email', 'existent', [
                'rule' => [$this, 'memberExistent'],
                'message' => __d('fe', 'Email address not registered')
            ])
            ->notEmpty('password', __d('fe', 'A password is required'))
            ->add('password', 'length', [
                'rule' => ['lengthBetween', 6, 8],
                'message' => __d('fe', 'Your password must be between %s and %s characters long', 6, 8)
            ])
            ->notEmpty('captcha', __d('fe', 'Incorrect answer'))
            ->add('captcha', 'check', [
                'rule' => [$this, 'captcha'],
                'message' => __d('fe', 'Incorrect answer')
            ]);

    }

    // member protected

    protected function _buildSchemaMemberProtected(Schema $schema)
    {
        return $schema;
    }

    protected function _buildValidatorMemberProtected(Validator $validator)
    {
        return $validator;
    }

    // member logout

    protected function _buildSchemaMemberLogout(Schema $schema)
    {
        return $schema;
    }

    protected function _buildValidatorMemberLogout(Validator $validator)
    {
        return $validator;
    }

    // member methods

    function memberRegister($id, $data, $request){

        $pwd = md5($data['password']);
        $data['password'] = '****';

        $this->connection->insert(
            'members', [
                'id' => $id,
                'username' => $data['username'],
                'password' => $pwd,
                'email' => $data['email'],
                'data' => json_encode($data),
                'modified' => date("Y-m-d H:i:s"),
                'created' => date("Y-m-d H:i:s"),
            ]
        );
        return true;
    }

    function memberForgot($data){

        // check
        $member = $this->connection->execute("SELECT `id`, `username` FROM `members` WHERE `email` = :email", ['email' => $data['email']])->fetch('assoc');
        if(is_array($member) && count($member) > 0){
            $this->connection->update(
                'members', [
                    'password' => md5($data['password']),
                    'modified' => date("Y-m-d H:i:s"),
                ], [
                    'id' => $member['id']
                ]
            );
            return ['username' => $member['username'], 'password' => $data['password']];
        }

        return false;
    }

    // newsletter methods

    function newsletterInit($id, $interests, $data, $request){
        switch(Configure::read('newsletter.type')){
            case "internal":
                    $this->connection->insert(
                        'newsletter', [
                            'id' => $id,
                            'email' => $data['email'],
                            'data' => json_encode($data),
                            'status' => 'init',
                            'modified' => date("Y-m-d H:i:s"),
                            'created' => date("Y-m-d H:i:s"),
                        ]
                    );
                    return true;
                break;
            case "tm5":
            case "tm6":
            case "maileon":
                return $this->interface->init($id, $interests, $data, $request);
                break;
            default:
                echo __METHOD__ . ": unknown newsletter interface!"; exit;
                break;
        }
        return false;
    }

    function newsletterSubscribe($id, $request){
        switch(Configure::read('newsletter.type')){
            case "internal":
                $check = $this->connection->execute("SELECT `status` FROM `newsletter` WHERE `id` = :id AND `status` IN ('init', 'subscribed')", ['id' => $id])->fetch('assoc');
                if(is_array($check) && count($check) > 0){
                    if($check['status'] == 'init'){
                        $this->connection->update(
                            'newsletter', [
                                'status' => 'subscribed',
                                'modified' => date("Y-m-d H:i:s"),
                            ], [
                                'id' => $id
                            ]
                        );
                    }
                    return true;
                }
                break;
            case "tm5":
            case "tm6":
            case "maileon":
                return $this->interface->subscribe($id, $request);
                break;
            default:
                echo __METHOD__ . ": unknown newsletter interface!"; exit;
                break;
        }
        return false;
    }

    function newsletterUnsubscribe($data, $request){
        switch(Configure::read('newsletter.type')){
            case "internal":
                $check = $this->connection->execute("SELECT `id` FROM `newsletter` WHERE `email` = :email AND `status` IN ('init', 'subscribed')", ['email' => $data['email']])->fetch('assoc');
                if(is_array($check) && count($check) > 0){
                    $this->connection->update(
                        'newsletter', [
                            'status' => 'unsubscribed',
                            'modified' => date("Y-m-d H:i:s"),
                        ], [
                            'id' => $check['id']
                        ]
                    );
                    return true;
                }
                break;
            case "tm5":
            case "tm6":
            case "maileon":
                return $this->interface->unsubscribe($data, $request);
                break;
            default:
                echo __METHOD__ . ": unknown newsletter interface!"; exit;
                break;
        }
        return false;
    }

    // custom rules

    function memberLogin($check, $context){
        $check = $this->connection->execute("SELECT `id` FROM `members` WHERE `username` = :username AND `password` = :password", ['username' => $context['data']['username'], 'password' => md5($context['data']['password'])])->fetch('assoc');
        if(is_array($check) && count($check) > 0){
            return true;
        }
        return false;
    }

    function memberNonExistent($check, $context)
    {
        if(!empty($check)){
            switch($context['field']){
                case "email":
                case "username":
                    $check = $this->connection->execute("SELECT `id` FROM `members` WHERE `" . $context['field'] . "` = :value", ['value' => $check])->fetch('assoc');
                    if(!is_array($check) || count($check) == 0){
                        return true;
                    }
                    break;
                default:
                    echo __METHOD__ . ": invalid field '" . $context['field'] . "'!"; exit;
                    break;
            }
        }
        return false;
    }

    function memberExistent($check, $context)
    {
        if(!empty($check)){
            switch($context['field']){
                case "email":
                    $check = $this->connection->execute("SELECT `id` FROM `members` WHERE `" . $context['field'] . "` = :value", ['value' => $check])->fetch('assoc');
                    if(is_array($check) && count($check) > 0){
                        return true;
                    }
                    break;
                default:
                    echo __METHOD__ . ": invalid field '" . $context['field'] . "'!"; exit;
                    break;
            }
        }
        return false;
    }

    function newsletterNonExistent($check, $context)
    {
        if(!empty($check)){
            switch(Configure::read('newsletter.type')){
                case "internal":
                    $check = $this->connection->execute("SELECT `id` FROM `newsletter` WHERE `email` = :email AND `status` IN ('init', 'subscribed')", ['email' => $check])->fetch('assoc');
                    if(!is_array($check) || count($check) == 0){
                        return true;
                    }
                    break;
                case "tm5":
                case "tm6":
                case "maileon":
                    return $this->interface->nonexistent($check);
                    break;
                default:
                    echo __METHOD__ . ": unknown newsletter interface!"; exit;
                    break;
            }
        }
        return false;
    }

    function newsletterExistent($check, $context)
    {
        if(!empty($check)){
            switch(Configure::read('newsletter.type')){
                case "internal":
                    $check = $this->connection->execute("SELECT `id` FROM `newsletter` WHERE `email` = :email AND `status` IN ('init', 'subscribed')", ['email' => $check])->fetch('assoc');
                    if(is_array($check) && count($check) > 0){
                        return true;
                    }
                    break;
                case "tm5":
                case "tm6":
                case "maileon":
                    return $this->interface->existent($check);
                    break;
                default:
                    echo __METHOD__ . ": unknown newsletter interface!"; exit;
                    break;
            }
        }
        return false;
    }

    function captcha($check, $context)
    {
        if(is_array($this->captcha) && array_key_exists('type', $this->captcha)){
            if($this->captcha['type'] == 'text'){
                $answer = $this->captcha['text'];
            }else{
                switch($this->captcha['operation']){
                    case '+':
                        $answer = $this->captcha['value1'] + $this->captcha['value2'];
                        break;
                    default:
                        $answer = false;
                        break;
                }
            }
            if($answer !== false && $answer == $check){
                return true;
            }
        }
        return false;
    }

    function privacy($check, $context){
        if($check == 1){
            return true;
        }
        return false;
    }

    function package($check, $context)
    {

        // init
        $data = [];

        // get data
        // NOTE: we don't get the "complete" context here - so we get the period from $_POST/$_GET!
        if(array_key_exists('REQUEST_METHOD', $_SERVER)){
            switch($_SERVER['REQUEST_METHOD']){
                case "POST":
                    $data = $_POST;
                    break;
                default:
                    $data = $_GET;
                    break;
            }
        }

        // check
        if(array_key_exists('arrival', $data) && !empty($data['arrival']) && array_key_exists('departure', $data) && !empty($data['departure'])){
            $now = mktime(0,0,0,date("m"),date("d"),date("Y"));
            $arrival = strtotime($data['arrival']);
            $departure = strtotime($data['departure']);
            if($arrival >= $now && $departure > $arrival){
                $package = $this->connection->execute("SELECT `valid_times` FROM `elements` WHERE `active` = 1 AND `id` = :id LIMIT 1", ['id' => $check])->fetch('assoc');
                if(is_array($package) && count($package) > 0){

                    $times = array_filter(explode("|", $package['valid_times']));

                    // check
                    if(is_array($times) && count($times) > 0){
                        foreach($times as $time){
                            if(strpos($time, ":") !== false){
                                list($from,$to) = explode(":", $time, 2);
                                $from = strtotime($from);
                                $to = strtotime($to);

                                if($arrival < $from && $departure > $to){ // valid "during"
                                    return true;
                                }else if($arrival > $from && $arrival < $to){ // valid "start"
                                    return true;
                                }else if($departure > $from && $departure < $to){ // valid "end"
                                    return true;
                                }

                            }
                        }
                    }
                }
            }
            return __d('fe', 'This offer is not available in the selected period');
        }else{
            return __d('fe', 'Please select an arrival and departure date');
        }

        return false;
    }

}
