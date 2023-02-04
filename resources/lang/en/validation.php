<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted' => 'Must be accepted.',
    'accepted_if' => 'Must be accepted when :other is :value.',
    'active_url' => 'Is not a valid URL.',
    'after' => 'Must be a date after :date.',
    'after_or_equal' => 'Must be a date after or equal to :date.',
    'alpha' => 'Must only contain letters.',
    'alpha_dash' => 'Must only contain letters, numbers, dashes and underscores.',
    'alpha_num' => 'Must only contain letters and numbers.',
    'array' => 'Must be an array.',
    'before' => 'Must be a date before :date.',
    'before_or_equal' => 'Must be a date before or equal to :date.',
    'between' => [
        'numeric' => 'Must be between :min and :max.',
        'file' => 'Must be between :min and :max kilobytes.',
        'string' => 'Must be between :min and :max characters.',
        'array' => 'Must have between :min and :max items.',
    ],
    'boolean' => 'Field must be true or false.',
    'confirmed' => 'Confirmation does not match.',
    'current_password' => 'The password is incorrect.',
    'date' => 'Is not a valid date.',
    'date_equals' => 'Must be a date equal to :date.',
    'date_format' => 'Does not match the format :format.',
    'different' => 'Must be different from :other field.',
    'digits' => 'Must be :digits digits.',
    'digits_between' => 'Must be between :min and :max digits.',
    'dimensions' => 'Has invalid image dimensions.',
    'distinct' => 'Field has a duplicate value.',
    'email' => 'Must be a valid email address.',
    'ends_with' => 'Must end with one of the following: :values.',
    'exists' => 'Resource not found.',
    'file' => 'Must be a file.',
    'filled' => 'Field must have a value.',
    'gt' => [
        'numeric' => 'Must be greater than :value.',
        'file' => 'Must be greater than :value kilobytes.',
        'string' => 'Must be greater than :value characters.',
        'array' => 'Must have more than :value items.',
    ],
    'gte' => [
        'numeric' => 'Must be greater than or equal :value.',
        'file' => 'Must be greater than or equal :value kilobytes.',
        'string' => 'Must be greater than or equal :value characters.',
        'array' => 'Must have :value items or more.',
    ],
    'image' => 'Must be an image.',
    'in' => 'Invalid value.',
    'in_array' => 'The value does not exist in :other.',
    'integer' => 'Must be an integer.',
    'ip' => 'Must be a valid IP address.',
    'ipv4' => 'Must be a valid IPv4 address.',
    'ipv6' => 'Must be a valid IPv6 address.',
    'json' => 'Must be a valid JSON string.',
    'lt' => [
        'numeric' => 'Must be less than :value.',
        'file' => 'Must be less than :value kilobytes.',
        'string' => 'Must be less than :value characters.',
        'array' => 'Must have less than :value items.',
    ],
    'lte' => [
        'numeric' => 'Must be less than or equal :value.',
        'file' => 'Must be less than or equal :value kilobytes.',
        'string' => 'Must be less than or equal :value characters.',
        'array' => 'Must not have more than :value items.',
    ],
    'max' => [
        'numeric' => 'Must not be greater than :max.',
        'file' => 'Must not be greater than :max kilobytes.',
        'string' => 'Must not be greater than :max characters.',
        'array' => 'Must not have more than :max items.',
    ],
    'mimes' => 'Must be a file of type: :values.',
    'mimetypes' => 'Must be a file of type: :values.',
    'min' => [
        'numeric' => 'Must be at least :min.',
        'file' => 'Must be at least :min kilobytes.',
        'string' => 'Must be at least :min characters.',
        'array' => 'Must have at least :min items.',
    ],
    'multiple_of' => 'Must be a multiple of :value.',
    'not_in' => 'Invalid value.',
    'not_regex' => 'Format is invalid.',
    'numeric' => 'Must be a number.',
    'password' => 'The password is incorrect.',
    'present' => 'Field must be present.',
    'regex' => 'Format is invalid.',
    'required' => 'Required field.',
    'required_if' => 'Required when :other is :value.',
    'required_unless' => 'Required unless :other is in :values.',
    'required_with' => 'Required when :values is present.',
    'required_with_all' => 'Required when :values are present.',
    'required_without' => 'Required when :values is not present.',
    'required_without_all' => 'Required when none of :values are present.',
    'prohibited' => 'Prohibited.',
    'prohibited_if' => 'Prohibited when :other is :value.',
    'prohibited_unless' => 'Prohibited unless :other is in :values.',
    'prohibits' => 'Field prohibits :other from being present.',
    'same' => 'Must match the :other field.',
    'size' => [
        'numeric' => 'Must be :size.',
        'file' => 'Must be :size kilobytes.',
        'string' => 'Must be :size characters.',
        'array' => 'Must contain :size items.',
    ],
    'starts_with' => 'Must start with one of the following: :values.',
    'string' => 'Must be a string.',
    'timezone' => 'Must be a valid timezone.',
    'unique' => 'Already been taken.',
    'uploaded' => 'Failed to upload.',
    'url' => 'Must be a valid URL.',
    'uuid' => 'Must be a valid UUID.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [],

];
