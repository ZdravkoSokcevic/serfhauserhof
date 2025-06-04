<?php

use Cake\Core\Configure;

/**
 * default "media" setup
 * change media.css if neccessary
 */
$_media = [
    'website' => [
       'header-su' => [
           'type' => 'bar',
           'label' => __d('be', 'Header (Summer)'),
           'images' => 1,
           'max' => 5,
           'anchor' => false,
           'elements' => 'header-teaser',
       ],
        'header-wi' => [
            'type' => 'bar',
            'label' => __d('be', 'Header (Winter)'),
            'images' => 1,
            'max' => 5,
            'anchor' => false,
            'elements' => 'header-teaser',
        ],
        'media' => [
            'type' => 'bar',
            'label' => __d('be', 'Media'),
            'elements' => 'tiny-teaser|small-teaser|fw-teaser|impressions-teaser|gallery|special|overview|pool',
        ],
    ]
];

/**
 * simply extend this array for other element types!
 *
 * @parameters
 *
 * active: boolean
 * structure: boolean (optional; is element attachable to structure f.e. pages, forms)
 * linkable: boolean (optional; only needed if "structure" is true; default: "true")
 * show: boolean (show automaticly in the navigation under "Media" - otherwise modifiy navigation.php to show this element)
 * sortable: boolean (optional; is element list sortable; default "false")
 * global_sorting: boolean (optional; only if "sortable" == true; for global sorting NOT just in selected category)
 * searchable: array (optional; array: list of fields to consider while searching OR pass settings with krx 'settings' to a callable function with the value of key 'func' )
 * icon: font awesome icon
 * config: array (optional)
 *      active: boolean (show "active" checkbox)
 *      range: boolean (show "display from/to" fields)
 *      times: boolean (show "valid times" filed)
 * use_categories: mixed (optional; true, false or name of element that should be used as categories; default: "true")
 * sort_categories: boolean (optional; make categories sortable - only  if "use_categories" = true; default: "false")
 * categories: array(optional; only relevant if use_categories = "true")
 *      title: boolean (show title field)
 *      content: boolean (show content field)
 *      seo: boolean (show seo field)
 *      special: boolean (show "special" checkbox)
 *      rel: boolean (show node selector)
 * editor: array (optional)
 *      template: string (optional; html markup to insert in editor
 *      options: array (optional; futher options for edior f.e. link text, css-class, ...)
 * media: array (optional)
 * translations: array (all needed translation)
 * fields: array (all needed fields for this element type - array key = field name; invalid key: "id", "code", "category_id", "internal", "fields", "media", "show_from", "show_to", "active", "modified", "created")
 *      fieldset: string|boolean (optional; name of fieldset)
 *      translate: boolean (optional; is field translateable)
 *      required: array (optional)
 *          on: array ("insert" and/or "update" - when is this field required?)
 *          rules: array ("requirepresence", "notempty", "allowempty" or every other rule you can add() to an validation object)
 *      attr: array (attributes submited to form helper)
 *      callbacks: array (optional)
 *          beforesave: function (callable method from ElementsTable class to handle value before save)
 *          afterfind: function (callable method from ElementsTable class to handle value after find)
 *          beforedelete: function (callable method from ElementsTable class to handle value before delete)
 * settings: array (optional; settings for this elements?)
 *      selection: string (element field that need special settings f.e. portals)
 *      subselection: string (element field for more detailed settings f.e. seasons)
 *      fields: (see "fields" above; no dynamic fields yet; no file upload yet)
 * prices: array (optional; prices for this elements?)
 *      per_element: boolean (optional; default = false)
 *      seasons: array (optional)
 *          active: boolean (optional; default = false)
 *          fields: array (optional)
 *              title: boolean (show title field; default = false)
 *              content: boolean (show content field; default = false)
 *          link: array (optional; link season with element f.e. package; default = false)
 *              code string (element code f.e. package)
 *              required boolean (optional; default = true)
 *          rel: string (optional; use seasons of other element f.e. room; default = false)
 *      drafts: array (optional)
 *          fields: array (optional)
 *              title: boolean (show title field; default = false)
 *              caption: boolean (show content field; default = false)
 *          options: array (optional; options for drafts)
 *      elements: string (optional; elements that refer to prices f.e. rooms for package prices)
 *      flags: array (optional; value/title pairs)
 * dynamic: array (optional; should form be "dynamic")
 *      depends: string (key of select field defiended in "fields"
 *      fields: array ("options" from the depending field as key, keys from needed fields defineded in "fields")
 */
$_elements = [
    // elements
    'elements' => [
        // menu groups
        'menugroup' => [
            'active' => true,
            'structure' => true,
            'linkable' => false,
            'show' => false,
            'use_categories' => false,
            'icon' => 'object-group',
            'config' => [
                'active' => false,
                'range' => false,
                'times' => false,
            ],
            'translations' => [
                'type' => __d('be', 'Menu group'),
                'menu' => __d('be', 'Menu groups'),
                'title' => [
                    'new' => __d('be', 'Create new menu group'),
                    'edit' => __d('be', 'Edit menu group'),
                ],
                'flash' => [
                    'delete' => [
                        'success' => __d('be', 'The menu group has been successfully removed!'),
                        'error' => __d('be', 'An error has occurred, please try again!'),
                    ],
                    'copy' => [
                        'success' => __d('be', 'The menu group has been successfully copied!'),
                        'error' => __d('be', 'An error has occurred, please try again!'),
                    ]
                ],
                'buttons' => [
                    'add' => __d('be', 'Add new menu group'),
                    'delete' => __d('be', 'Do you really want to delete this menu group?'),
                ]
            ],
            'fields' => [
                'title' => [
                    'translate' => true,
                    'required' => [
                        'on' => ['insert', 'update'],
                        'rules' => [
                            'notempty' => __d('be', 'A title is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Title'),
                        'placeholder' => __d('be', 'Title'),
                    ]
                ],
            ]
        ],
        // pages
        'page' => [
            'active' => true,
            'structure' => true,
            'show' => false,
            'searchable' => ['headline', 'content'],
            'icon' => 'file-text-o',
            'config' => [
                'active' => false,
                'range' => false,
                'times' => false,
            ],
            'media' => $_media,
            'translations' => [
                'type' => __d('be', 'Page'),
                'menu' => __d('be', 'Pages'),
                'title' => [
                    'new' => __d('be', 'Create new page'),
                    'edit' => __d('be', 'Edit page'),
                ],
                'flash' => [
                    'delete' => [
                        'success' => __d('be', 'The page has been successfully removed!'),
                        'error' => __d('be', 'An error has occurred, please try again!'),
                    ],
                    'copy' => [
                        'success' => __d('be', 'The page has been successfully copied!'),
                        'error' => __d('be', 'An error has occurred, please try again!'),
                    ]
                ],
                'buttons' => [
                    'add' => __d('be', 'Add new page'),
                    'delete' => __d('be', 'Do you really want to delete this page?'),
                ]
            ],
            'fields' => [
                'html' => [
                    'fieldset' => __d('be', 'SEO'),
                    'translate' => true,
                    'required' => [
                        'on' => ['insert', 'update'],
                        'rules' => [
                            'notempty' => __d('be', 'A html title is required'),
                            'minLength' => [
                                'rule' => ['minLength', Configure::read('seo.meta.title.min')],
                                'message' => __d('be', 'Min. %s chars!', Configure::read('seo.meta.title.min')),
                            ],
                            'maxLength' => [
                                'rule' => ['maxLength', Configure::read('seo.meta.title.max')],
                                'message' => __d('be', 'Max. %s chars!', Configure::read('seo.meta.title.max')),
                            ]
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'HTML title'),
                        'placeholder' => __d('be', 'HTML title'),
                        'class' => 'counter',
                        'data-counter-min' => Configure::read('seo.meta.title.min'),
                        'data-counter-max' => Configure::read('seo.meta.title.max'),
                    ]
                ],
                'meta' => [
                    'fieldset' => __d('be', 'SEO'),
                    'translate' => true,
                    'required' => [
                        'on' => ['insert', 'update'],
                        'rules' => [
                            'notempty' => __d('be', 'A META description is required'),
                            'minLength' => [
                                'rule' => ['minLength', Configure::read('seo.meta.desc.min')],
                                'message' => __d('be', 'Min. %s chars!', Configure::read('seo.meta.desc.min')),
                            ],
                            'maxLength' => [
                                'rule' => ['maxLength', Configure::read('seo.meta.desc.max')],
                                'message' => __d('be', 'Max. %s chars!', Configure::read('seo.meta.desc.max')),
                            ]
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'META description'),
                        'placeholder' => __d('be', 'META description'),
                        'class' => 'counter',
                        'data-counter-min' => Configure::read('seo.meta.desc.min'),
                        'data-counter-max' => Configure::read('seo.meta.desc.max'),
                    ]
                ],
                'title' => [
                    'translate' => true,
                    'required' => [
                        'on' => ['insert', 'update'],
                        'rules' => [
                            'notempty' => __d('be', 'A title is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Title'),
                        'placeholder' => __d('be', 'Title'),
                    ]
                ],
                'headline' => [
                    'translate' => true,
                    'required' => [
                        'on' => ['insert', 'update'],
                        'rules' => [
                            'notempty' => __d('be', 'A headline is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Headline'),
                        'placeholder' => __d('be', 'Headline'),
                    ]
                ],
                'content' => [
                    'translate' => true,
                    'required' => [
                        'on' => ['insert', 'update'],
                        'rules' => [
                            'notempty' => __d('be', 'A content is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'textarea',
                        'label' => __d('be', 'Content'),
                        'placeholder' => __d('be', 'Content'),
                        'class' => 'wysiwyg',
                    ]
                ],
            ]
        ],
        // forms
        'form' => [
            'active' => true,
            'structure' => true,
            'show' => false,
            'searchable' => ['headline', 'content'],
            'icon' => 'envelope-o',
            'config' => [
                'active' => false,
                'range' => false,
                'times' => false,
            ],
            'media' => $_media,
            'translations' => [
                'type' => __d('be', 'Form'),
                'menu' => __d('be', 'Forms'),
                'title' => [
                    'new' => __d('be', 'Create new form'),
                    'edit' => __d('be', 'Edit form'),
                ],
                'flash' => [
                    'delete' => [
                        'success' => __d('be', 'The form has been successfully removed!'),
                        'error' => __d('be', 'An error has occurred, please try again!'),
                    ],
                    'copy' => [
                        'success' => __d('be', 'The form has been successfully copied!'),
                        'error' => __d('be', 'An error has occurred, please try again!'),
                    ]
                ],
                'buttons' => [
                    'add' => __d('be', 'Add new form'),
                    'delete' => __d('be', 'Do you really want to delete this form?'),
                ]
            ],
            'fields' => [
                'html' => [
                    'fieldset' => __d('be', 'SEO'),
                    'translate' => true,
                    'required' => [
                        'on' => ['insert', 'update'],
                        'rules' => [
                            'notempty' => __d('be', 'A html title is required'),
                            'minLength' => [
                                'rule' => ['minLength', Configure::read('seo.meta.title.min')],
                                'message' => __d('be', 'Min. %s chars!', Configure::read('seo.meta.title.min')),
                            ],
                            'maxLength' => [
                                'rule' => ['maxLength', Configure::read('seo.meta.title.max')],
                                'message' => __d('be', 'Max. %s chars!', Configure::read('seo.meta.title.max')),
                            ]
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'HTML title'),
                        'placeholder' => __d('be', 'HTML title'),
                        'class' => 'counter',
                        'data-counter-min' => Configure::read('seo.meta.title.min'),
                        'data-counter-max' => Configure::read('seo.meta.title.max'),
                    ]
                ],
                'meta' => [
                    'fieldset' => __d('be', 'SEO'),
                    'translate' => true,
                    'required' => [
                        'on' => ['insert', 'update'],
                        'rules' => [
                            'notempty' => __d('be', 'A META description is required'),
                            'minLength' => [
                                'rule' => ['minLength', Configure::read('seo.meta.desc.min')],
                                'message' => __d('be', 'Min. %s chars!', Configure::read('seo.meta.desc.min')),
                            ],
                            'maxLength' => [
                                'rule' => ['maxLength', Configure::read('seo.meta.desc.max')],
                                'message' => __d('be', 'Max. %s chars!', Configure::read('seo.meta.desc.max')),
                            ]
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'META description'),
                        'placeholder' => __d('be', 'META description'),
                        'class' => 'counter',
                        'data-counter-min' => Configure::read('seo.meta.desc.min'),
                        'data-counter-max' => Configure::read('seo.meta.desc.max'),
                    ]
                ],
                'title' => [
                    'translate' => true,
                    'required' => [
                        'on' => ['insert', 'update'],
                        'rules' => [
                            'notempty' => __d('be', 'A title is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Title'),
                        'placeholder' => __d('be', 'Title'),
                    ]
                ],
                'headline' => [
                    'translate' => true,
                    'required' => [
                        'on' => ['insert', 'update'],
                        'rules' => [
                            'notempty' => __d('be', 'A headline is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Headline'),
                        'placeholder' => __d('be', 'Headline'),
                    ]
                ],
                'content' => [
                    'translate' => true,
                    'required' => [
                        'on' => ['insert', 'update'],
                        'rules' => [
                            'notempty' => __d('be', 'A content is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'textarea',
                        'label' => __d('be', 'Content'),
                        'placeholder' => __d('be', 'Content'),
                        'class' => 'wysiwyg',
                    ]
                ],
                'view' => [
                    'translate' => false,
                    'required' => [
                        'on' => ['insert', 'update'],
                        'rules' => [
                            'notempty' => __d('be', 'A type is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'select',
                        'label' => __d('be', 'Type'),
                        'empty' => __d('be', '-- Choose a type --'),
                        'options' => [
                            'contact' => __d('be', 'Contact'),
                            'newsletter' => __d('be', 'Newsletter'),
                            'request' => __d('be', 'Request'),
                            'brochure' => __d('be', 'Brochure'),
                            // 'coupon' => __d('be', 'Coupon'),
                            // 'callback' => __d('be', 'Callback'),
                            // 'member' => __d('be', 'Member'),
                            // 'lastminute' => __d('be', 'Last-minute'),
                            // 'table' => __d('be', 'Table reservation'),
                            'job' => __d('be', 'Job'),
                        ]
                    ]
                ],
                'recipient' => [
                    'fieldset' => __d('be', 'E-Mail'),
                    'translate' => false,
                    'required' => [
                        'on' => ['insert', 'update'],
                        'rules' => [
                            'notempty' => __d('be', 'An email address is required'),
                            'email' => [
                                'rule' => 'email',
                                'message' => __d('be', 'Invalid email address'),
                            ]
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Recipient'),
                        'placeholder' => __d('be', 'Recipient'),
                    ]
                ],
                'email_subject' => [
                    'fieldset' => __d('be', 'E-Mail'),
                    'translate' => false,
                    'required' => [
                        'on' => ['insert', 'update'],
                        'rules' => [
                            'notempty' => __d('be', 'A subject is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Subject'),
                        'placeholder' => __d('be', 'Subject'),
                    ]
                ],
                'email_headline' => [
                    'fieldset' => __d('be', 'E-Mail'),
                    'translate' => false,
                    'required' => [
                        'on' => ['insert', 'update'],
                        'rules' => [
                            'notempty' => __d('be', 'A headline is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Headline'),
                        'placeholder' => __d('be', 'Headline'),
                    ]
                ],
                'email_content' => [
                    'fieldset' => __d('be', 'E-Mail'),
                    'translate' => false,
                    'required' => [
                        'on' => ['insert', 'update'],
                        'rules' => [
                            'notempty' => __d('be', 'A content is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'textarea',
                        'label' => __d('be', 'Content'),
                        'placeholder' => __d('be', 'Content'),
                        'class' => 'wysiwyg',
                        'data-config' => 'email',
                    ]
                ],
                'doi_subject' => [
                    'fieldset' => __d('be', 'E-Mail'),
                    'translate' => true,
                    'required' => [
                        'on' => ['insert', 'update'],
                        'rules' => [
                            'notempty' => __d('be', 'A subject is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Subject'),
                        'placeholder' => __d('be', 'Subject'),
                    ]
                ],
                'doi_headline' => [
                    'fieldset' => __d('be', 'E-Mail'),
                    'translate' => true,
                    'required' => [
                        'on' => ['insert', 'update'],
                        'rules' => [
                            'notempty' => __d('be', 'A headline is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Headline'),
                        'placeholder' => __d('be', 'Headline'),
                    ]
                ],
                'doi_content' => [
                    'fieldset' => __d('be', 'E-Mail'),
                    'translate' => true,
                    'required' => [
                        'on' => ['insert', 'update'],
                        'rules' => [
                            'notempty' => __d('be', 'A content is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'textarea',
                        'label' => __d('be', 'Content'),
                        'placeholder' => __d('be', 'Content'),
                        'class' => 'wysiwyg',
                        'data-config' => 'email',
                    ]
                ],
                'autoreply' => [
                    'fieldset' => __d('be', 'Auto reply'),
                    'translate' => false,
                    'attr' => [
                        'type' => 'checkbox',
                        'label' => __d('be', 'active'),
                    ]
                ],
                'reply_subject' => [
                    'fieldset' => __d('be', 'Auto reply'),
                    'translate' => true,
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Subject'),
                        'placeholder' => __d('be', 'Subject'),
                    ]
                ],
                'reply_headline' => [
                    'fieldset' => __d('be', 'Auto reply'),
                    'translate' => true,
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Headline'),
                        'placeholder' => __d('be', 'Headline'),
                    ]
                ],
                'reply_content' => [
                    'fieldset' => __d('be', 'Auto reply'),
                    'translate' => true,
                    'attr' => [
                        'type' => 'textarea',
                        'label' => __d('be', 'Content'),
                        'placeholder' => __d('be', 'Content'),
                        'class' => 'wysiwyg',
                        'data-config' => 'email',
                    ]
                ],
                'headline_member' => [
                    'fieldset' => __d('be', 'Protected'),
                    'translate' => true,
                    'required' => [
                        'on' => ['insert', 'update'],
                        'rules' => [
                            'notempty' => __d('be', 'A headline is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Headline'),
                        'placeholder' => __d('be', 'Headline'),
                    ]
                ],
                'content_member' => [
                    'fieldset' => __d('be', 'Protected'),
                    'translate' => true,
                    'required' => [
                        'on' => ['insert', 'update'],
                        'rules' => [
                            'notempty' => __d('be', 'A content is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'textarea',
                        'label' => __d('be', 'Content'),
                        'placeholder' => __d('be', 'Content'),
                        'class' => 'wysiwyg',
                    ]
                ],
                'headline_login' => [
                    'fieldset' => __d('be', 'Login'),
                    'translate' => true,
                    'required' => [
                        'on' => ['insert', 'update'],
                        'rules' => [
                            'notempty' => __d('be', 'A headline is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Headline'),
                        'placeholder' => __d('be', 'Headline'),
                    ]
                ],
                'content_login' => [
                    'fieldset' => __d('be', 'Login'),
                    'translate' => true,
                    'required' => [
                        'on' => ['insert', 'update'],
                        'rules' => [
                            'notempty' => __d('be', 'A content is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'textarea',
                        'label' => __d('be', 'Content'),
                        'placeholder' => __d('be', 'Content'),
                        'class' => 'wysiwyg',
                    ]
                ],
                'headline_signup' => [
                    'fieldset' => __d('be', 'Signup'),
                    'translate' => true,
                    'required' => [
                        'on' => ['insert', 'update'],
                        'rules' => [
                            'notempty' => __d('be', 'A headline is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Headline'),
                        'placeholder' => __d('be', 'Headline'),
                    ]
                ],
                'content_signup' => [
                    'fieldset' => __d('be', 'Signup'),
                    'translate' => true,
                    'required' => [
                        'on' => ['insert', 'update'],
                        'rules' => [
                            'notempty' => __d('be', 'A content is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'textarea',
                        'label' => __d('be', 'Content'),
                        'placeholder' => __d('be', 'Content'),
                        'class' => 'wysiwyg',
                    ]
                ],
                'email_subject_signup' => [
                    'fieldset' => __d('be', 'Signup'),
                    'translate' => false,
                    'required' => [
                        'on' => ['insert', 'update'],
                        'rules' => [
                            'notempty' => __d('be', 'A subject is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Subject'),
                        'placeholder' => __d('be', 'Subject'),
                    ]
                ],
                'email_headline_signup' => [
                    'fieldset' => __d('be', 'Signup'),
                    'translate' => false,
                    'required' => [
                        'on' => ['insert', 'update'],
                        'rules' => [
                            'notempty' => __d('be', 'A headline is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Headline'),
                        'placeholder' => __d('be', 'Headline'),
                    ]
                ],
                'email_content_signup' => [
                    'fieldset' => __d('be', 'Signup'),
                    'translate' => false,
                    'required' => [
                        'on' => ['insert', 'update'],
                        'rules' => [
                            'notempty' => __d('be', 'A content is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'textarea',
                        'label' => __d('be', 'Content'),
                        'placeholder' => __d('be', 'Content'),
                        'class' => 'wysiwyg',
                        'data-config' => 'email',
                    ]
                ],
                'headline_forgot' => [
                    'fieldset' => __d('be', 'Forgot'),
                    'translate' => true,
                    'required' => [
                        'on' => ['insert', 'update'],
                        'rules' => [
                            'notempty' => __d('be', 'A headline is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Headline'),
                        'placeholder' => __d('be', 'Headline'),
                    ]
                ],
                'content_forgot' => [
                    'fieldset' => __d('be', 'Forgot'),
                    'translate' => true,
                    'required' => [
                        'on' => ['insert', 'update'],
                        'rules' => [
                            'notempty' => __d('be', 'A content is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'textarea',
                        'label' => __d('be', 'Content'),
                        'placeholder' => __d('be', 'Content'),
                        'class' => 'wysiwyg',
                    ]
                ],
                'email_subject_forgot' => [
                    'fieldset' => __d('be', 'Forgot'),
                    'translate' => false,
                    'required' => [
                        'on' => ['insert', 'update'],
                        'rules' => [
                            'notempty' => __d('be', 'A subject is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Subject'),
                        'placeholder' => __d('be', 'Subject'),
                    ]
                ],
                'email_headline_forgot' => [
                    'fieldset' => __d('be', 'Forgot'),
                    'translate' => false,
                    'required' => [
                        'on' => ['insert', 'update'],
                        'rules' => [
                            'notempty' => __d('be', 'A headline is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Headline'),
                        'placeholder' => __d('be', 'Headline'),
                    ]
                ],
                'email_content_forgot' => [
                    'fieldset' => __d('be', 'Forgot'),
                    'translate' => false,
                    'required' => [
                        'on' => ['insert', 'update'],
                        'rules' => [
                            'notempty' => __d('be', 'A content is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'textarea',
                        'label' => __d('be', 'Content'),
                        'placeholder' => __d('be', 'Content'),
                        'class' => 'wysiwyg',
                        'data-config' => 'email',
                    ]
                ],
                'last_minute_offers' => [
                    'translate' => false,
                    'required' => [
                        'on' => ['insert', 'update'],
                        'rules' => [
                            'notempty' => __d('be', 'A last-minute offer category is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Last-minute offer category'),
                        'class' => 'selector',
                        'data-selector-max' => 1,
                        'data-selector-category' => 'elements:lastminute',
                        'data-selector-text' => __d('be', 'Select category'),
                    ]
                ],
                'jobs'  => [
                    'translate' => false,
                    'required' => [
                        'on' => ['insert', 'update'],
                        'rules' => [
                            'notempty' => __d('be', 'A job category is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Job categories'),
                        'class' => 'selector',
                        'data-selector-category' => 'elements:job',
                        'data-selector-text' => __d('be', 'Select categories'),
                    ]
                ],
            ],
            'dynamic' => [
                'depends' => 'view',
                'fields' => [
                    'contact' => ['html', 'meta', 'title', 'headline', 'content', 'recipient', 'email_subject', 'email_headline', 'email_content', 'autoreply', 'reply_subject', 'reply_headline', 'reply_content'],
                    'newsletter' => ['html', 'meta', 'title', 'headline', 'content', 'doi_subject', 'doi_headline', 'doi_content'],
                    'request' => ['html', 'meta', 'title', 'headline', 'content', 'recipient', 'email_subject', 'email_headline', 'email_content', 'autoreply', 'reply_subject', 'reply_headline', 'reply_content'],
                    'brochure' => ['html', 'meta', 'title', 'headline', 'content', 'recipient', 'email_subject', 'email_headline', 'email_content', 'autoreply', 'reply_subject', 'reply_headline', 'reply_content'],
                    'coupon' => ['html', 'meta', 'title', 'headline', 'content', 'recipient', 'email_subject', 'email_headline', 'email_content', 'autoreply', 'reply_subject', 'reply_headline', 'reply_content'],
                    'callback' => ['html', 'meta', 'title', 'headline', 'content', 'recipient', 'email_subject', 'email_headline', 'email_content',],
                    'member' => ['html', 'meta', 'title', 'headline_member', 'content_member', 'headline_login', 'content_login', 'headline_signup', 'content_signup', 'email_subject_signup', 'email_headline_signup', 'email_content_signup', 'headline_forgot', 'content_forgot', 'email_subject_forgot', 'email_headline_forgot', 'email_content_forgot'],
                    'lastminute' => ['t4d', 'html', 'meta', 'title', 'headline', 'last_minute_offers', 'content', 'recipient', 'email_subject', 'email_headline', 'email_content', 'autoreply', 'reply_subject', 'reply_headline', 'reply_content', 'top_lnk_1_link', 'top_lnk_1_text', 'top_lnk_2_link', 'top_lnk_2_text', 'top_lnk_3_link', 'top_lnk_3_text'],
                    'table' => ['html', 'meta', 'title', 'headline', 'content', 'recipient', 'email_subject', 'email_headline', 'email_content', 'autoreply', 'reply_subject', 'reply_headline', 'reply_content'],
                    'job' => ['html', 'meta', 'title', 'headline', 'content', 'recipient', 'email_subject', 'email_headline', 'email_content', 'jobs', 'autoreply', 'reply_subject', 'reply_headline', 'reply_content'],
                ]
            ]
        ],
        
        // rooms
        'room' => [
            'active' => true,
            'structure' => true,
            'show' => false,
            'sortable' => true,
            'global_sorting' => true,
            'searchable' => ['headline', 'content'],
            'icon' => 'bed',
            'sort_categories' => true,
            'categories' => [
                'title' => true,
                'rel' => true,
            ],
            'config' => [
                'active' => false,
                'range' => false,
                'times' => false,
            ],
            'media' => $_media,
            'translations' => [
                'type' => __d('be', 'Room'),
                'menu' => __d('be', 'Rooms'),
                'title' => [
                    'new' => __d('be', 'Create new room'),
                    'edit' => __d('be', 'Edit room'),
                ],
                'flash' => [
                    'delete' => [
                        'success' => __d('be', 'The room has been successfully removed!'),
                        'error' => __d('be', 'An error has occurred, please try again!'),
                    ],
                    'copy' => [
                        'success' => __d('be', 'The room has been successfully copied!'),
                        'error' => __d('be', 'An error has occurred, please try again!'),
                    ]
                ],
                'buttons' => [
                    'add' => __d('be', 'Add new room'),
                    'delete' => __d('be', 'Do you really want to delete this room?'),
                ]
            ],
            'fields' => [
                'html' => [
                    'fieldset' => __d('be', 'SEO'),
                    'translate' => true,
                    'required' => [
                        'on' => ['insert', 'update'],
                        'rules' => [
                            'notempty' => __d('be', 'A html title is required'),
                            'minLength' => [
                                'rule' => ['minLength', Configure::read('seo.meta.title.min')],
                                'message' => __d('be', 'Min. %s chars!', Configure::read('seo.meta.title.min')),
                            ],
                            'maxLength' => [
                                'rule' => ['maxLength', Configure::read('seo.meta.title.max')],
                                'message' => __d('be', 'Max. %s chars!', Configure::read('seo.meta.title.max')),
                            ]
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'HTML title'),
                        'placeholder' => __d('be', 'HTML title'),
                        'class' => 'counter',
                        'data-counter-min' => Configure::read('seo.meta.title.min'),
                        'data-counter-max' => Configure::read('seo.meta.title.max'),
                    ]
                ],
                'meta' => [
                    'fieldset' => __d('be', 'SEO'),
                    'translate' => true,
                    'required' => [
                        'on' => ['insert', 'update'],
                        'rules' => [
                            'notempty' => __d('be', 'A META description is required'),
                            'minLength' => [
                                'rule' => ['minLength', Configure::read('seo.meta.desc.min')],
                                'message' => __d('be', 'Min. %s chars!', Configure::read('seo.meta.desc.min')),
                            ],
                            'maxLength' => [
                                'rule' => ['maxLength', Configure::read('seo.meta.desc.max')],
                                'message' => __d('be', 'Max. %s chars!', Configure::read('seo.meta.desc.max')),
                            ]
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'META description'),
                        'placeholder' => __d('be', 'META description'),
                        'class' => 'counter',
                        'data-counter-min' => Configure::read('seo.meta.desc.min'),
                        'data-counter-max' => Configure::read('seo.meta.desc.max'),
                    ]
                ],
                'title' => [
                    'translate' => true,
                    'required' => [
                        'on' => ['insert', 'update'],
                        'rules' => [
                            'notempty' => __d('be', 'A title is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Title'),
                        'placeholder' => __d('be', 'Title'),
                    ]
                ],
                'headline' => [
                    'translate' => true,
                    'required' => [
                        'on' => ['insert', 'update'],
                        'rules' => [
                            'notempty' => __d('be', 'A headline is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Headline'),
                        'placeholder' => __d('be', 'Headline'),
                    ]
                ],
                'content' => [
                    'translate' => true,
                    'required' => [
                        'on' => ['insert', 'update'],
                        'rules' => [
                            'notempty' => __d('be', 'A content is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'textarea',
                        'label' => __d('be', 'Content'),
                        'placeholder' => __d('be', 'Content'),
                        'class' => 'wysiwyg',
                    ]
                ],
                'sketch' => [
                    'translate' => false,
                    'required' => [
                        'on' => ['insert', 'update'],
                        'rules' => [
                            'notempty' => __d('be', 'A sketches is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Room sketches'),
                        'class' => 'selector',
                        'data-selector-max' => 5,
                        'data-selector-image' => '4',
                        'data-selector-text' => __d('be', 'Select sketches'),
                    ]
                ],
                'images' => [
                    'translate' => false,
                    'required' => [
                        'on' => ['insert', 'update'],
                        'rules' => [
                            'notempty' => __d('be', 'An image is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Room images'),
                        'class' => 'selector',
                        'data-selector-max' => 5,
                        'data-selector-image' => '4',
                        'data-selector-text' => __d('be', 'Select images'),
                    ]
                ],
                'info' => [
                    'translate' => false,
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Information'),
                        'class' => 'selector',
                        'data-selector-max' => 1,
                        'data-selector-element' => 'special:textblock',
                        'data-selector-text' => __d('be', 'Select text'),
                    ]
                ],
                'tours' => [
                    'translate' => false,
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', '360° Tour'),
                        'class' => 'selector',
                        'data-selector-element' => 'link',
                        'data-selector-text' => __d('be', 'Select tour'),
                    ]
                ],
                'occupancy' => [
                    'translate' => false,
                    'required' => [
                        'on' => ['insert', 'update'],
                        'rules' => [
                            'notempty' => __d('be', 'The number of persons is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'select',
                        'label' => __d('be', 'Number of persons'),
                        'empty' => __d('be', '-- Choose a number of persons --'),
                        'options' => [
                            '1' => __d('be', '%s person', 1),
                            '2' => __d('be', '%s persons', 2),
                            '3' => __d('be', '%s persons', 3),
                            '4' => __d('be', '%s persons', 4),
                            '5' => __d('be', '%s persons', 5),
                            '6' => __d('be', '%s persons', 6),
                            '7' => __d('be', '%s persons', 7),
                            '8' => __d('be', '%s persons', 8),
                            '9' => __d('be', '%s persons', 9),
                            '10' => __d('be', '%s persons', 10),
                        ],
                        'templateVars' => ['help' => '<div class="help-message">' . __d('be', 'Number of persons for packages') . '</div>'],
                    ]
                ],
                'back' => [
                    'fieldset' => __d('be', 'Back to overview'),
                    'translate' => false,
//                    'required' => [
//                        'on' => ['insert','update'],
//                        'rules' => [
//                            'notempty' => __d('be', 'A back link is required'),
//                        ]
//                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Back link'),
                        'class' => 'selector',
                        'data-selector-max' => 1,
                        'data-selector-node' => 'true',
                        'data-selector-text' => __d('be', 'Select link'),
                    ]
                ],
//                'vioma' => [
//                    'fieldset' => __d('be', 'External relations'),
//                    'translate' => false,
//                    'attr' => [
//                        'type' => 'text',
//                        'label' => 'Vioma ID',
//                        'placeholder' => 'Vioma ID',
//                    ]
//                ],
            ],
            'prices' => [
                'per_element' => false,
                'seasons' => [
                    'active' => true,
                    'fields' => [
                        'title' => true,
                        'content' => false,
                    ],
                    'link' => [
                        'code' => 'package',
                        'required' => false,
                    ]
                ],
                'drafts' => [
                    'fields' => [
                        'title' => true,
                        'caption' => true,
                    ],
                    'options' => [
                        'day' => __d('fe', 'Day price'),
                        'short' => __d('fe', 'Shortstay'),
                        'package' => __d('fe', 'Package'),
                    ]
                ],
            ]
        ],
        
        // packages
        'package' => [
            'active' => true,
            'structure' => true,
            'show' => false,
            'sortable' => true,
            'searchable' => ['headline', 'content', 'services_title', 'services_text'],
            'icon' => 'gift',
            'sort_categories' => true,
            'categories' => [
                'title' => true,
                'rel' => true,
            ],
            'config' => [
                'active' => true,
                'range' => true,
                'times' => true,
            ],
            'media' => $_media,
            'translations' => [
                'type' => __d('be', 'Package'),
                'menu' => __d('be', 'Packages'),
                'title' => [
                    'new' => __d('be', 'Create new package'),
                    'edit' => __d('be', 'Edit package'),
                ],
                'flash' => [
                    'delete' => [
                        'success' => __d('be', 'The package has been successfully removed!'),
                        'error' => __d('be', 'An error has occurred, please try again!'),
                    ],
                    'copy' => [
                        'success' => __d('be', 'The package has been successfully copied!'),
                        'error' => __d('be', 'An error has occurred, please try again!'),
                    ]
                ],
                'buttons' => [
                    'add' => __d('be', 'Add new package'),
                    'delete' => __d('be', 'Do you really want to delete this package?'),
                ]
            ],
            'fields' => [
                'html' => [
                    'fieldset' => __d('be', 'SEO'),
                    'translate' => true,
                    'required' => [
                        'on' => ['insert', 'update'],
                        'rules' => [
                            'notempty' => __d('be', 'A html title is required'),
                            'minLength' => [
                                'rule' => ['minLength', Configure::read('seo.meta.title.min')],
                                'message' => __d('be', 'Min. %s chars!', Configure::read('seo.meta.title.min')),
                            ],
                            'maxLength' => [
                                'rule' => ['maxLength', Configure::read('seo.meta.title.max')],
                                'message' => __d('be', 'Max. %s chars!', Configure::read('seo.meta.title.max')),
                            ]
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'HTML title'),
                        'placeholder' => __d('be', 'HTML title'),
                        'class' => 'counter',
                        'data-counter-min' => Configure::read('seo.meta.title.min'),
                        'data-counter-max' => Configure::read('seo.meta.title.max'),
                    ]
                ],
                'meta' => [
                    'fieldset' => __d('be', 'SEO'),
                    'translate' => true,
                    'required' => [
                        'on' => ['insert', 'update'],
                        'rules' => [
                            'notempty' => __d('be', 'A META description is required'),
                            'minLength' => [
                                'rule' => ['minLength', Configure::read('seo.meta.desc.min')],
                                'message' => __d('be', 'Min. %s chars!', Configure::read('seo.meta.desc.min')),
                            ],
                            'maxLength' => [
                                'rule' => ['maxLength', Configure::read('seo.meta.desc.max')],
                                'message' => __d('be', 'Max. %s chars!', Configure::read('seo.meta.desc.max')),
                            ]
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'META description'),
                        'placeholder' => __d('be', 'META description'),
                        'class' => 'counter',
                        'data-counter-min' => Configure::read('seo.meta.desc.min'),
                        'data-counter-max' => Configure::read('seo.meta.desc.max'),
                    ]
                ],
                'images' => [
                    'translate' => false,
                    'required' => [
                        'on' => ['insert', 'update'],
                        'rules' => [
                            'notempty' => __d('be', 'An image is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Package image'),
                        'class' => 'selector',
                        'data-selector-max' => 1,
                        'data-selector-image' => '1',
                        'data-selector-text' => __d('be', 'Select image'),
                    ]
                ],
                'title' => [
                    'translate' => true,
                    'required' => [
                        'on' => ['insert', 'update'],
                        'rules' => [
                            'notempty' => __d('be', 'A title is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Title'),
                        'placeholder' => __d('be', 'Title'),
                    ]
                ],
                'headline' => [
                    'translate' => true,
                    'required' => [
                        'on' => ['insert', 'update'],
                        'rules' => [
                            'notempty' => __d('be', 'A headline is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Headline'),
                        'placeholder' => __d('be', 'Headline'),
                    ]
                ],
                'teaser' => [
                    'translate' => true,
                    'required' => [
                        'on' => ['insert', 'update'],
                        'rules' => [
                            'notempty' => __d('be', 'A teaser text is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'textarea',
                        'label' => __d('be', 'Teaser text'),
                        'placeholder' => __d('be', 'Teaser text'),
                        'class' => 'wysiwyg',
                        'data-config' => 'small'
                    ]
                ],
                'content' => [
                    'translate' => true,
                    'required' => [
                        'on' => ['insert', 'update'],
                        'rules' => [
                            'notempty' => __d('be', 'A content is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'textarea',
                        'label' => __d('be', 'Content'),
                        'placeholder' => __d('be', 'Content'),
                        'class' => 'wysiwyg',
                    ]
                ],
                'services_headline' => [
                    'translate' => true,
                    'required' => [
                        'on' => ['insert', 'update'],
                        'rules' => [
                            'notempty' => __d('be', 'A services headline is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Included services headline'),
                        'placeholder' => __d('be', 'Included services headline'),
                    ]
                ],
                'services' => [
                    'translate' => true,
                    'required' => [
                        'on' => ['insert', 'update'],
                        'rules' => [
                            'notempty' => __d('be', 'A text is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'textarea',
                        'label' => __d('be', 'Included services'),
                        'placeholder' => __d('be', 'Included services'),
                        'class' => 'wysiwyg',
                    ]
                ],
                'info' => [
                    'translate' => false,
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Information'),
                        'class' => 'selector',
                        'data-selector-max' => 1,
                        'data-selector-element' => 'special:textblock',
                        'data-selector-text' => __d('be', 'Select text'),
                    ]
                ],
                'spipt' => [// show persons in price table
                    'translate' => false,
                    'attr' => [
                        'type' => 'checkbox',
                        'label' => __d('be', 'Show number of persons in prices table'),
                    ]
                ],
                'back' => [
                    'fieldset' => __d('be', 'Back to overview'),
                    'translate' => false,
//                    'required' => [
//                        'on' => ['insert','update'],
//                        'rules' => [
//                            'notempty' => __d('be', 'A back link is required'),
//                        ]
//                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Back link'),
                        'class' => 'selector',
                        'data-selector-max' => 1,
                        'data-selector-node' => 'true',
                        'data-selector-text' => __d('be', 'Select link'),
                    ]
                ],
//                'vioma' => [
//                    'fieldset' => __d('be', 'External relations'),
//                    'translate' => false,
//                    'attr' => [
//                        'type' => 'text',
//                        'label' => 'Vioma ID',
//                        'placeholder' => 'Vioma ID',
//                    ]
//                ],
            ],
            'prices' => [
                'per_element' => true,
                'seasons' => [
                    'active' => true,
                    'rel' => 'room',
                ],
                'drafts' => [
                    'fields' => [
                        'title' => true,
                        'caption' => false,
                    ],
                ],
                'elements' => 'room',
            ]
        ],
        
        // last-minute
        'lastminute' => [
            'active' => false,
            'structure' => false,
            'show' => false,
            'sortable' => true,
            'searchable' => ['headline', 'content'],
            'icon' => 'clock-o',
            'config' => [
                'active' => true,
                'range' => false,
                'times' => false,
            ],
            'media' => false,
            'translations' => [
                'type' => __d('be', 'Last-minute offer'),
                'menu' => __d('be', 'Last-minute offers'),
                'title' => [
                    'new' => __d('be', 'Create new last-minute offer'),
                    'edit' => __d('be', 'Edit last-minute offer'),
                ],
                'flash' => [
                    'delete' => [
                        'success' => __d('be', 'The last-minute offer has been successfully removed!'),
                        'error' => __d('be', 'An error has occurred, please try again!'),
                    ],
                    'copy' => [
                        'success' => __d('be', 'The last-minute offer has been successfully copied!'),
                        'error' => __d('be', 'An error has occurred, please try again!'),
                    ]
                ],
                'buttons' => [
                    'add' => __d('be', 'Add new last-minute offer'),
                    'delete' => __d('be', 'Do you really want to delete this last-minute offer?'),
                ]
            ],
            'fields' => [
//                'headline' => [
//                    'translate' => true,
//                    'required' => [
//                        'on' => ['insert','update'],
//                        'rules' => [
//                            'notempty' => __d('be', 'A headline is required'),
//                        ]
//                    ],
//                    'attr' => [
//                        'type' => 'text',
//                        'label' => __d('be', 'Headline'),
//                        'placeholder' => __d('be', 'Headline'),
//                    ]
//                ],
                'content' => [
                    'translate' => true,
                    'required' => [
                        'on' => ['insert', 'update'],
                        'rules' => [
                            'notempty' => __d('be', 'A content is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'textarea',
                        'label' => __d('be', 'Content'),
                        'placeholder' => __d('be', 'Content'),
                        'class' => 'wysiwyg',
                    ]
                ],
                'room' => [
                    'translate' => false,
                    'required' => [
                        'on' => ['insert', 'update'],
                        'rules' => [
                            'notempty' => __d('be', 'A room is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Room'),
                        'class' => 'selector',
                        'data-selector-max' => 1,
                        'data-selector-element' => 'room',
                        'data-selector-text' => __d('be', 'Select room'),
                    ]
                ],
                'quota' => [
                    'fieldset' => __d('be', 'Number of rooms'),
                    'translate' => false,
                    'required' => [
                        'on' => ['insert', 'update'],
                        'rules' => [
                            'notempty' => __d('be', 'A room number is required'),
                            'naturalNumber' => [
                                'rule' => ['naturalNumber', true],
                                'message' => __d('be', 'Invalid number'),
                                'last' => true,
                            ],
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Number of rooms'),
                    ]
                ],
                'price_desc' => [
                    'fieldset' => __d('be', 'Price'),
                    'translate' => false,
                    'required' => [
                        'on' => ['insert', 'update'],
                        'rules' => [
                            'notempty' => __d('be', 'A description is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Description'),
                    ]
                ],
                'price_value' => [
                    'fieldset' => __d('be', 'Price'),
                    'translate' => false,
                    'required' => [
                        'on' => ['insert', 'update'],
                        'rules' => [
                            'notempty' => __d('be', 'A value is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Price'),
                    ]
                ],
                'ranges' => [
                    'fieldset' => __d('be', 'Valid times'),
                    'translate' => false,
                    'required' => [
                        'on' => ['insert', 'update'],
                        'rules' => [
                            'notempty' => __d('be', 'A range is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'class' => 'times',
                        'label' => __d('be', 'Valid times'),
                    ]
                ],
            ],
        ],

        // jobs
        'job' => [
            'active' => true,
            'structure' => false,
            'show' => false,
            'sortable' => true,
            'searchable' => ['headline', 'content'],
            'categories' => [
                'title' => true,
            ],
            'icon' => 'clock-o',
            'config' => [
                'active' => true,
                'range' => false,
                'times' => false,
            ],
            'media' => false,
            'translations' => [
                'type' => __d('be', 'Job'),
                'menu' => __d('be', 'Jobs'),
                'title' => [
                    'new' => __d('be', 'Create new job'),
                    'edit' => __d('be', 'Edit job'),
                ],
                'flash' => [
                    'delete' => [
                        'success' => __d('be', 'The job has been successfully removed!'),
                        'error' => __d('be', 'An error has occurred, please try again!'),
                    ],
                    'copy' => [
                        'success' => __d('be', 'The job has been successfully copied!'),
                        'error' => __d('be', 'An error has occurred, please try again!'),
                    ]
                ],
                'buttons' => [
                    'add' => __d('be', 'Add new job'),
                    'delete' => __d('be', 'Do you really want to delete this job?'),
                ]
            ],
            'fields' => [
                'headline' => [
                    'translate' => true,
                    'required' => [
                        'on' => ['insert','update'],
                        'rules' => [
                            'notempty' => __d('be', 'A headline is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Headline'),
                        'placeholder' => __d('be', 'Headline'),
                    ]
                ],
                'content' => [
                    'translate' => true,
                    'required' => [
                        'on' => ['insert', 'update'],
                        'rules' => [
                            'notempty' => __d('be', 'A content is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'textarea',
                        'label' => __d('be', 'Content'),
                        'placeholder' => __d('be', 'Content'),
                        'class' => 'wysiwyg',
                    ]
                ],
            ],
        ],
        
        // treatments
        'treatment' => [
            'active' => true,
            'structure' => false,
            'show' => false,
            'sortable' => true,
            'searchable' => ['title', 'content'],
            'icon' => 'envira',
            'config' => [
                'active' => true,
                'range' => false,
                'times' => false,
            ],
            'translations' => [
                'type' => __d('be', 'Treatment'),
                'menu' => __d('be', 'Treatments'),
                'title' => [
                    'new' => __d('be', 'Create new treatment'),
                    'edit' => __d('be', 'Edit treatment'),
                ],
                'flash' => [
                    'delete' => [
                        'success' => __d('be', 'The treatment has been successfully removed!'),
                        'error' => __d('be', 'An error has occurred, please try again!'),
                    ],
                    'copy' => [
                        'success' => __d('be', 'The treatment has been successfully copied!'),
                        'error' => __d('be', 'An error has occurred, please try again!'),
                    ]
                ],
                'buttons' => [
                    'add' => __d('be', 'Add new treatment'),
                    'delete' => __d('be', 'Do you really want to delete this treatment?'),
                ]
            ],
            'fields' => [
                'title' => [
                    'translate' => true,
                    'required' => [
                        'on' => ['insert', 'update'],
                        'rules' => [
                            'notempty' => __d('be', 'A title is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Title'),
                        'placeholder' => __d('be', 'Title'),
                    ]
                ],
                'content' => [
                    'translate' => true,
                    'required' => [
                        'on' => ['insert', 'update'],
                        'rules' => [
                            'notempty' => __d('be', 'A content is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'textarea',
                        'label' => __d('be', 'Content'),
                        'placeholder' => __d('be', 'Content'),
                        'class' => 'wysiwyg',
                    ]
                ],
            ],
            'prices' => [
                'per_element' => false,
                'drafts' => [
                    'fields' => [
                        'title' => true,
                        'caption' => false,
                    ],
                ],
            ]
        ],
        
        // image galleries
        'gallery' => [
            'active' => true,
            'show' => true,
            'icon' => 'picture-o',
            'config' => [
                'active' => false,
                'range' => false,
                'times' => false,
            ],
            'translations' => [
                'type' => __d('be', 'Image gallery'),
                'menu' => __d('be', 'Image galleries'),
                'title' => [
                    'new' => __d('be', 'Create new image gallery'),
                    'edit' => __d('be', 'Edit image gallery'),
                ],
                'flash' => [
                    'delete' => [
                        'success' => __d('be', 'The image gallery has been successfully removed!'),
                        'error' => __d('be', 'An error has occurred, please try again!'),
                    ],
                    'copy' => [
                        'success' => __d('be', 'The image gallery has been successfully copied!'),
                        'error' => __d('be', 'An error has occurred, please try again!'),
                    ]
                ],
                'buttons' => [
                    'add' => __d('be', 'Add new image gallery'),
                    'delete' => __d('be', 'Do you really want to delete this image gallery?'),
                ]
            ],
            'fields' => [
                'images' => [
                    'fieldset' => __d('be', 'Images'),
                    'translate' => false,
                    'required' => [
                        'on' => ['insert', 'update'],
                        'rules' => [
                            'notempty' => __d('be', 'An image is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Images'),
                        'class' => 'selector',
                        'data-selector-image' => '1',
                        'data-selector-text' => __d('be', 'Select images'),
                    ]
                ],
            ],
        ],
        // slideshow
        'slideshow' => [
            'active' => true,
            'show' => true,
            'icon' => 'desktop',
            'sortable' => true,
            'config' => [
                'active' => false,
                'range' => false,
                'times' => false,
            ],
            'translations' => [
                'type' => __d('be', 'Slideshow'),
                'menu' => __d('be', 'Slideshows'),
                'title' => [
                    'new' => __d('be', 'Create new slideshow'),
                    'edit' => __d('be', 'Edit slideshow'),
                ],
                'flash' => [
                    'delete' => [
                        'success' => __d('be', 'The slideshow has been successfully removed!'),
                        'error' => __d('be', 'An error has occurred, please try again!'),
                    ],
                    'copy' => [
                        'success' => __d('be', 'The slideshow has been successfully copied!'),
                        'error' => __d('be', 'An error has occurred, please try again!'),
                    ]
                ],
                'buttons' => [
                    'add' => __d('be', 'Add new slideshow'),
                    'delete' => __d('be', 'Do you really want to delete this slideshow?'),
                ]
            ],
            'fields' => [
                'title' => [
                    'translate' => true,
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Title'),
                        'placeholder' => __d('be', 'Title'),
                    ]
                ],
                'images' => [
                    'fieldset' => __d('be', 'Images'),
                    'translate' => false,
                    'required' => [
                        'on' => ['insert', 'update'],
                        'rules' => [
                            'notempty' => __d('be', 'An image is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Images'),
                        'class' => 'selector',
                        'data-selector-image' => '1',
                        'data-selector-text' => __d('be', 'Select images'),
                    ]
                ],
            ],
        ],
        // header teaser
        'header-teaser' => [
            'active' => true,
            'show' => false,
            'icon' => 'newspaper-o',
            'config' => [
                'active' => false,
                'range' => false,
                'times' => false,
            ],
            'translations' => [
                'type' => __d('be', 'Header teaser'),
                'menu' => __d('be', 'Header teasers'),
                'title' => [
                    'new' => __d('be', 'Create new header teaser'),
                    'edit' => __d('be', 'Edit header teaser'),
                ],
                'flash' => [
                    'delete' => [
                        'success' => __d('be', 'The teaser has been successfully removed!'),
                        'error' => __d('be', 'An error has occurred, please try again!'),
                    ],
                    'copy' => [
                        'success' => __d('be', 'The teaser has been successfully copied!'),
                        'error' => __d('be', 'An error has occurred, please try again!'),
                    ]
                ],
                'buttons' => [
                    'add' => __d('be', 'Add new teaser'),
                    'delete' => __d('be', 'Do you really want to delete this teaser?'),
                ]
            ],
            'fields' => [
                'line1' => [
                    'translate' => true,
                    'required' => [
                        'on' => ['insert', 'update'],
                        'rules' => [
                            'notempty' => __d('be', 'A text is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Line 1'),
                        'placeholder' => __d('be', 'Line 1'),
                    ]
                ],
                'line2' => [
                    'translate' => true,
                    'required' => [
                        'on' => ['insert', 'update'],
                        'rules' => [
                            'notempty' => __d('be', 'A text is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Line 2'),
                        'placeholder' => __d('be', 'Line 2'),
                    ]
                ],
                'image' => [
                    'fieldset' => __d('be', 'Media'),
                    'translate' => false,
                    'required' => [
                        'on' => ['insert', 'update'],
                        'rules' => [
                            'notempty' => __d('be', 'An image is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Image'),
                        'class' => 'selector',
                        'data-selector-max' => 1,
                        'data-selector-image' => '1',
                        'data-selector-text' => __d('be', 'Select image'),
                    ]
                ],
//                'link' => [
//                    'fieldset' => __d('be', 'Media'),
//                    'translate' => false,
//                    'required' => [
//                        'on' => ['insert','update'],
//                        'rules' => [
//                            'notempty' => __d('be', 'A link is required'),
//                        ]
//                    ],
//                    'attr' => [
//                        'type' => 'text',
//                        'label' => __d('be', 'Link'),
//                        'class' => 'selector',
//                        'data-selector-max' => 1,
//                        'data-selector-node' => 'true',
//                        'data-selector-element' => 'link',
//                        'data-selector-text' => __d('be', 'Select link'),
//                    ]
//                ],
            ],
        ],
        // tiny teaser
        'tiny-teaser' => [
            'active' => true,
            'show' => false,
            'searchable' => ['headline', 'content'],
            'icon' => 'newspaper-o',
            'config' => [
                'active' => false,
                'range' => false,
                'times' => false,
            ],
            'translations' => [
                'type' => __d('be', 'Tiny Teaser'),
                'menu' => __d('be', 'Tiny teasers'),
                'title' => [
                    'new' => __d('be', 'Create new tiny teaser'),
                    'edit' => __d('be', 'Edit tiny teaser'),
                ],
                'flash' => [
                    'delete' => [
                        'success' => __d('be', 'The teaser has been successfully removed!'),
                        'error' => __d('be', 'An error has occurred, please try again!'),
                    ],
                    'copy' => [
                        'success' => __d('be', 'The teaser has been successfully copied!'),
                        'error' => __d('be', 'An error has occurred, please try again!'),
                    ]
                ],
                'buttons' => [
                    'add' => __d('be', 'Add new teaser'),
                    'delete' => __d('be', 'Do you really want to delete this teaser?'),
                ]
            ],
            'fields' => [
                'headline' => [
                    'translate' => true,
                    'required' => [
                        'on' => ['insert', 'update'],
                        'rules' => [
                            'notempty' => __d('be', 'A headline is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Headline'),
                        'placeholder' => __d('be', 'Headline'),
                    ]
                ],
                'image' => [
                    'fieldset' => __d('be', 'Media'),
                    'translate' => false,
                    'required' => [
                        'on' => ['insert', 'update'],
                        'rules' => [
                            'notempty' => __d('be', 'An image is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Image'),
                        'class' => 'selector',
                        'data-selector-max' => 1,
                        'data-selector-image' => '3',
                        'data-selector-text' => __d('be', 'Select image'),
                    ]
                ],
                'link' => [
                    'fieldset' => __d('be', 'Media'),
                    'translate' => false,
                    'required' => [
                        'on' => ['insert', 'update'],
                        'rules' => [
                            'notempty' => __d('be', 'A link is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Link'),
                        'class' => 'selector',
                        'data-selector-max' => 1,
                        'data-selector-node' => 'true',
                        'data-selector-element' => 'link',
                        'data-selector-text' => __d('be', 'Select link'),
                    ]
                ],
            ],
        ],
        // small teaser
        'small-teaser' => [
            'active' => true,
            'show' => false,
            'searchable' => ['headline', 'content'],
            'icon' => 'newspaper-o',
            'config' => [
                'active' => false,
                'range' => false,
                'times' => false,
            ],
            'translations' => [
                'type' => __d('be', 'Small Teaser'),
                'menu' => __d('be', 'Small teasers'),
                'title' => [
                    'new' => __d('be', 'Create new small teaser'),
                    'edit' => __d('be', 'Edit small teaser'),
                ],
                'flash' => [
                    'delete' => [
                        'success' => __d('be', 'The teaser has been successfully removed!'),
                        'error' => __d('be', 'An error has occurred, please try again!'),
                    ],
                    'copy' => [
                        'success' => __d('be', 'The teaser has been successfully copied!'),
                        'error' => __d('be', 'An error has occurred, please try again!'),
                    ]
                ],
                'buttons' => [
                    'add' => __d('be', 'Add new teaser'),
                    'delete' => __d('be', 'Do you really want to delete this teaser?'),
                ]
            ],
            'fields' => [
                'headline' => [
                    'translate' => true,
                    'required' => [
                        'on' => ['insert', 'update'],
                        'rules' => [
                            'notempty' => __d('be', 'A headline is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Headline'),
                        'placeholder' => __d('be', 'Headline'),
                    ]
                ],
                'content' => [
                    'translate' => true,
                    'required' => [
                        'on' => ['insert', 'update'],
                        'rules' => [
                            'notempty' => __d('be', 'A content is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'textarea',
                        'label' => __d('be', 'Content'),
                        'placeholder' => __d('be', 'Content'),
                        'class' => 'wysiwyg',
                        'data-config' => 'teaser',
                    ]
                ],
                'image' => [
                    'fieldset' => __d('be', 'Media'),
                    'translate' => false,
                    'required' => [
                        'on' => ['insert', 'update'],
                        'rules' => [
                            'notempty' => __d('be', 'An image is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Image'),
                        'class' => 'selector',
                        'data-selector-max' => 1,
                        'data-selector-image' => '3',
                        'data-selector-text' => __d('be', 'Select image'),
                    ]
                ],
                'link' => [
                    'fieldset' => __d('be', 'Link 1'),
                    'translate' => false,
                    'required' => [
                        'on' => ['insert', 'update'],
                        'rules' => [
                            'notempty' => __d('be', 'A link is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Link'),
                        'class' => 'selector',
                        'data-selector-max' => 1,
                        'data-selector-node' => 'true',
                        'data-selector-element' => 'link|download',
                        'data-selector-text' => __d('be', 'Select link'),
                    ]
                ],
                'linktext' => [
                    'fieldset' => __d('be', 'Link 1'),
                    'translate' => true,
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Link text'),
                        'placeholder' => __d('be', 'Link text'),
                    ]
                ],
                'link2' => [
                    'fieldset' => __d('be', 'Link 2'),
                    'translate' => false,
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Link'),
                        'class' => 'selector',
                        'data-selector-max' => 1,
                        'data-selector-node' => 'true',
                        'data-selector-element' => 'link|download',
                        'data-selector-text' => __d('be', 'Select link'),
                    ]
                ],
                'linktext2' => [
                    'fieldset' => __d('be', 'Link 2'),
                    'translate' => true,
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Link text'),
                        'placeholder' => __d('be', 'Link text'),
                    ]
                ],
            ],
        ],
        // full width teaser
        'fw-teaser' => [
            'active' => true,
            'show' => false,
            'searchable' => ['preheadline', 'headline', 'content'],
            'icon' => 'newspaper-o',
            'config' => [
                'active' => false,
                'range' => false,
                'times' => false,
            ],
            'translations' => [
                'type' => __d('be', 'Full width teaser'),
                'menu' => __d('be', 'Full width teasers'),
                'title' => [
                    'new' => __d('be', 'Create new full width teaser'),
                    'edit' => __d('be', 'Edit full width teaser'),
                ],
                'flash' => [
                    'delete' => [
                        'success' => __d('be', 'The teaser has been successfully removed!'),
                        'error' => __d('be', 'An error has occurred, please try again!'),
                    ],
                    'copy' => [
                        'success' => __d('be', 'The teaser has been successfully copied!'),
                        'error' => __d('be', 'An error has occurred, please try again!'),
                    ]
                ],
                'buttons' => [
                    'add' => __d('be', 'Add new teaser'),
                    'delete' => __d('be', 'Do you really want to delete this teaser?'),
                ]
            ],
            'fields' => [
                'preheadline' => [
                    'translate' => true,
                    'required' => [
                        'on' => ['insert', 'update'],
                        'rules' => [
                            'notempty' => __d('be', 'A text is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Pre-headline'),
                        'placeholder' => __d('be', 'Pre-headline'),
                    ]
                ],
                'headline' => [
                    'translate' => true,
                    'required' => [
                        'on' => ['insert', 'update'],
                        'rules' => [
                            'notempty' => __d('be', 'A headline is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Headline'),
                        'placeholder' => __d('be', 'Headline'),
                    ]
                ],
                'content' => [
                    'translate' => true,
                    'required' => [
                        'on' => ['insert', 'update'],
                        'rules' => [
                            'notempty' => __d('be', 'A content is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'textarea',
                        'label' => __d('be', 'Content'),
                        'placeholder' => __d('be', 'Content'),
                        'class' => 'wysiwyg',
                        'data-config' => 'teaser',
                    ]
                ],
                'image' => [
                    'fieldset' => __d('be', 'Media'),
                    'translate' => false,
                    'required' => [
                        'on' => ['insert', 'update'],
                        'rules' => [
                            'notempty' => __d('be', 'An image is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Image'),
                        'class' => 'selector',
                        'data-selector-max' => 1,
                        'data-selector-image' => '1',
                        'data-selector-text' => __d('be', 'Select image'),
                    ]
                ],
            ],
        ],
        // impressions teaser
        'impressions-teaser' => [
            'active' => true,
            'show' => false,
            'searchable' => ['box1_headline', 'box1_content', 'box2_headline', 'box2_content'],
            'icon' => 'newspaper-o',
            'config' => [
                'active' => false,
                'range' => false,
                'times' => false,
            ],
            'translations' => [
                'type' => __d('be', 'Impressions teaser'),
                'menu' => __d('be', 'Impressions teasers'),
                'title' => [
                    'new' => __d('be', 'Create new impressions teaser'),
                    'edit' => __d('be', 'Edit impressions teaser'),
                ],
                'flash' => [
                    'delete' => [
                        'success' => __d('be', 'The teaser has been successfully removed!'),
                        'error' => __d('be', 'An error has occurred, please try again!'),
                    ],
                    'copy' => [
                        'success' => __d('be', 'The teaser has been successfully copied!'),
                        'error' => __d('be', 'An error has occurred, please try again!'),
                    ]
                ],
                'buttons' => [
                    'add' => __d('be', 'Add new teaser'),
                    'delete' => __d('be', 'Do you really want to delete this teaser?'),
                ]
            ],
            'fields' => [
                'box1_headline' => [
                    'fieldset' => __d('be', 'Bottom left'),
                    'translate' => true,
                    'required' => [
                        'on' => ['insert', 'update'],
                        'rules' => [
                            'notempty' => __d('be', 'A headline is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Headline'),
                        'placeholder' => __d('be', 'Headline'),
                    ]
                ],
                'box1_content' => [
                    'fieldset' => __d('be', 'Bottom left'),
                    'translate' => true,
                    'required' => [
                        'on' => ['insert', 'update'],
                        'rules' => [
                            'notempty' => __d('be', 'A text is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'textarea',
                        'label' => __d('be', 'Content'),
                        'placeholder' => __d('be', 'Content'),
                        'class' => 'wysiwyg',
                        'data-config' => 'teaser',
                    ]
                ],
                'box2_headline' => [
                    'fieldset' => __d('be', 'Top right'),
                    'translate' => true,
                    'required' => [
                        'on' => ['insert', 'update'],
                        'rules' => [
                            'notempty' => __d('be', 'A headline is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Headline'),
                        'placeholder' => __d('be', 'Headline'),
                    ]
                ],
                'box2_content' => [
                    'fieldset' => __d('be', 'Top right'),
                    'translate' => true,
                    'required' => [
                        'on' => ['insert', 'update'],
                        'rules' => [
                            'notempty' => __d('be', 'A text is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'textarea',
                        'label' => __d('be', 'Content'),
                        'placeholder' => __d('be', 'Content'),
                        'class' => 'wysiwyg',
                        'data-config' => 'teaser',
                    ]
                ],
                'image-tl' => [
                    'fieldset' => __d('be', 'Image') . ' (' . __d('be', 'top left') . ')',
                    'translate' => false,
                    'required' => [
                        'on' => ['insert', 'update'],
                        'rules' => [
                            'notempty' => __d('be', 'An image is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Image'),
                        'class' => 'selector',
                        'data-selector-max' => 1,
                        'data-selector-image' => '3',
                        'data-selector-text' => __d('be', 'Select image'),
                    ]
                ],
                'text_tl' => [
                    'fieldset' => __d('be', 'Image') . ' (' . __d('be', 'top left') . ')',
                    'translate' => true,
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Text'),
                        'placeholder' => __d('be', 'Text'),
                    ]
                ],
                'link-tl' => [
                    'fieldset' => __d('be', 'Image') . ' (' . __d('be', 'top left') . ')',
                    'translate' => false,
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Link'),
                        'class' => 'selector',
                        'data-selector-max' => 1,
                        'data-selector-node' => 'true',
                        'data-selector-element' => 'link',
                        'data-selector-text' => __d('be', 'Select link'),
                    ]
                ],
                'image-bl' => [
                    'fieldset' => __d('be', 'Image') . ' (' . __d('be', 'bottom left') . ')',
                    'translate' => false,
                    'required' => [
                        'on' => ['insert', 'update'],
                        'rules' => [
                            'notempty' => __d('be', 'An image is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Image'),
                        'class' => 'selector',
                        'data-selector-max' => 1,
                        'data-selector-image' => '4',
                        'data-selector-text' => __d('be', 'Select image'),
                    ]
                ],
                'text_bl' => [
                    'fieldset' => __d('be', 'Image') . ' (' . __d('be', 'bottom left') . ')',
                    'translate' => true,
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Text'),
                        'placeholder' => __d('be', 'Text'),
                    ]
                ],
                'link-bl' => [
                    'fieldset' => __d('be', 'Image') . ' (' . __d('be', 'bottom left') . ')',
                    'translate' => false,
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Link'),
                        'class' => 'selector',
                        'data-selector-max' => 1,
                        'data-selector-node' => 'true',
                        'data-selector-element' => 'link',
                        'data-selector-text' => __d('be', 'Select link'),
                    ]
                ],
                'image-c' => [
                    'fieldset' => __d('be', 'Image') . ' (' . __d('be', 'center') . ')',
                    'translate' => false,
                    'required' => [
                        'on' => ['insert', 'update'],
                        'rules' => [
                            'notempty' => __d('be', 'An image is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Image'),
                        'class' => 'selector',
                        'data-selector-max' => 1,
                        'data-selector-image' => '4',
                        'data-selector-text' => __d('be', 'Select image'),
                    ]
                ],
                'text_c' => [
                    'fieldset' => __d('be', 'Image') . ' (' . __d('be', 'center') . ')',
                    'translate' => true,
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Text'),
                        'placeholder' => __d('be', 'Text'),
                    ]
                ],
                'link-c' => [
                    'fieldset' => __d('be', 'Image') . ' (' . __d('be', 'center') . ')',
                    'translate' => false,
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Link'),
                        'class' => 'selector',
                        'data-selector-max' => 1,
                        'data-selector-node' => 'true',
                        'data-selector-element' => 'link',
                        'data-selector-text' => __d('be', 'Select link'),
                    ]
                ],
                'image-br' => [
                    'fieldset' => __d('be', 'Image') . ' (' . __d('be', 'bottom right') . ')',
                    'translate' => false,
                    'required' => [
                        'on' => ['insert', 'update'],
                        'rules' => [
                            'notempty' => __d('be', 'An image is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Image (square)'),
                        'class' => 'selector',
                        'data-selector-max' => 1,
                        'data-selector-image' => '4',
                        'data-selector-text' => __d('be', 'Select image'),
                    ]
                ],
                'image-br2' => [
                    'fieldset' => __d('be', 'Image') . ' (' . __d('be', 'bottom right') . ')',
                    'translate' => false,
                    'required' => [
                        'on' => ['insert', 'update'],
                        'rules' => [
                            'notempty' => __d('be', 'An image is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Image (rectangular)'),
                        'class' => 'selector',
                        'data-selector-max' => 1,
                        'data-selector-image' => '3',
                        'data-selector-text' => __d('be', 'Select image'),
                    ]
                ],
                'text_br' => [
                    'fieldset' => __d('be', 'Image') . ' (' . __d('be', 'bottom right') . ')',
                    'translate' => true,
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Text'),
                        'placeholder' => __d('be', 'Text'),
                    ]
                ],
                'link-br' => [
                    'fieldset' => __d('be', 'Image') . ' (' . __d('be', 'bottom right') . ')',
                    'translate' => false,
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Link'),
                        'class' => 'selector',
                        'data-selector-max' => 1,
                        'data-selector-node' => 'true',
                        'data-selector-element' => 'link',
                        'data-selector-text' => __d('be', 'Select link'),
                    ]
                ],
            ],
        ],
        // downloads
        'download' => [
            'active' => true,
            'show' => true,
            'icon' => 'download',
            'config' => [
                'active' => false,
                'range' => false,
                'times' => false,
            ],
            'editor' => [
                'template' => '<a href="#" data-model="%model" data-code="%code" data-id="%id" class="%class">%title</a>',
                'options' => [
                    'title' => [
                        'type' => 'text',
                        'text' => __d('be', 'Download text'),
                        'required' => true,
                        'prefill' => 'selected',
                    ],
                    'class' => [
                        'type' => 'select',
                        'text' => __d('be', 'CSS-Class'),
                        'options' => Configure::read('editor.links'),
                    ]
                ]
            ],
            'translations' => [
                'type' => __d('be', 'Download'),
                'menu' => __d('be', 'Downloads'),
                'title' => [
                    'new' => __d('be', 'Create new download'),
                    'edit' => __d('be', 'Edit download'),
                ],
                'flash' => [
                    'delete' => [
                        'success' => __d('be', 'The download has been successfully removed!'),
                        'error' => __d('be', 'An error has occurred, please try again!'),
                    ],
                    'copy' => [
                        'success' => __d('be', 'The download has been successfully copied!'),
                        'error' => __d('be', 'An error has occurred, please try again!'),
                    ]
                ],
                'buttons' => [
                    'add' => __d('be', 'Add new download'),
                    'delete' => __d('be', 'Do you really want to delete this download?'),
                ]
            ],
            'fields' => [
                'title' => [
                    'translate' => true,
                    'required' => [
                        'on' => ['insert', 'update'],
                        'rules' => [
                            'notempty' => __d('be', 'A title is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Title'),
                        'placeholder' => __d('be', 'Title'),
                    ]
                ],
                'file' => [
                    'fieldset' => __d('be', 'File'),
                    'translate' => true,
                    'required' => [
                        'on' => ['insert'],
                        'rules' => [
                            'notempty' => __d('be', 'A file is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'file',
                        'label' => __d('be', 'File'),
                        'accept' => '.pdf,.doc,.docx,.zip',
                        'templateVars' => ['help' => '<div class="help-message">' . __d('be', 'Accepted file formats: .pdf, .doc, .docx, .zip') . '</div>'],
                    ],
                    'callbacks' => [
                        'beforesave' => 'savefile',
                        'afterfind' => 'findfile',
                        'beforedelete' => 'deletefile',
                    ]
                ],
            ]
        ],
        // links
        'link' => [
            'active' => true,
            'structure' => true,
            'linkable' => false,
            'show' => true,
            'icon' => 'link',
            'config' => [
                'active' => false,
                'range' => false,
                'times' => false,
            ],
            'editor' => [
                'template' => '<a href="#" data-model="%model" data-code="%code" data-id="%id" class="%class">%title</a>',
                'options' => [
                    'title' => [
                        'type' => 'text',
                        'text' => __d('be', 'Link text'),
                        'required' => true,
                        'prefill' => 'selected',
                    ],
                    'class' => [
                        'type' => 'select',
                        'text' => __d('be', 'CSS-Class'),
                        'options' => Configure::read('editor.links'),
                    ]
                ]
            ],
            'translations' => [
                'type' => __d('be', 'Link'),
                'menu' => __d('be', 'Links'),
                'title' => [
                    'new' => __d('be', 'Create new link'),
                    'edit' => __d('be', 'Edit link'),
                ],
                'flash' => [
                    'delete' => [
                        'success' => __d('be', 'The link has been successfully removed!'),
                        'error' => __d('be', 'An error has occurred, please try again!'),
                    ],
                    'copy' => [
                        'success' => __d('be', 'The link has been successfully copied!'),
                        'error' => __d('be', 'An error has occurred, please try again!'),
                    ]
                ],
                'buttons' => [
                    'add' => __d('be', 'Add new link'),
                    'delete' => __d('be', 'Do you really want to delete this link?'),
                ]
            ],
            'fields' => [
                'title' => [
                    'translate' => true,
                    'required' => [
                        'on' => ['insert', 'update'],
                        'rules' => [
                            'notempty' => __d('be', 'A title is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Title'),
                        'placeholder' => __d('be', 'Title'),
                    ]
                ],
                'link' => [
                    'translate' => true,
                    'required' => [
                        'on' => ['insert', 'update'],
                        'rules' => [
                            'notempty' => __d('be', 'A link is required'),
//                            'url' => [
//                                'rule' => 'url',
//                                'message' => __d('be', 'Invalid URL'),
//                                'last' => true,
//                            ],
                            'protocol' => [
                                'rule' => ['custom', '/^(http\:\/\/|https\:\/\/|\/|javascript\:)/i'],
                                'message' => __d('be', 'Link without protocol'),
                            ]
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Link'),
                        'placeholder' => __d('be', 'Link'),
//                        'templateVars' => ['help' => '<div class="help-message">' . __d('be', 'Link with leading protocol (f.e. http://)') . '</div>'],
                    ]
                ],
                'target' => [
                    'translate' => false,
                    'required' => [
                        'on' => ['insert', 'update'],
                        'rules' => [
                            'notempty' => __d('be', 'A target is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'select',
                        'label' => __d('be', 'Target'),
                        'empty' => __d('be', '-- Choose a target --'),
                        'options' => [
                            '_blank' => __d('be', 'New window'),
                            '_self' => __d('be', 'Same window'),
                        ]
                    ]
                ],
            ]
        ],
        // videos
        'video' => [
            'active' => false,
            'show' => true,
            'icon' => 'video-camera',
            'config' => [
                'active' => false,
                'range' => false,
                'times' => false,
            ],
            'translations' => [
                'type' => __d('be', 'Movie'),
                'menu' => __d('be', 'Movies'),
                'title' => [
                    'new' => __d('be', 'Create new movie'),
                    'edit' => __d('be', 'Edit movie'),
                ],
                'flash' => [
                    'delete' => [
                        'success' => __d('be', 'The movie has been successfully removed!'),
                        'error' => __d('be', 'An error has occurred, please try again!'),
                    ],
                    'copy' => [
                        'success' => __d('be', 'The movie has been successfully copied!'),
                        'error' => __d('be', 'An error has occurred, please try again!'),
                    ]
                ],
                'buttons' => [
                    'add' => __d('be', 'Add new movie'),
                    'delete' => __d('be', 'Do you really want to delete this movie?'),
                ]
            ],
            'fields' => [
                'title' => [
                    'translate' => true,
                    'required' => [
                        'on' => ['insert', 'update'],
                        'rules' => [
                            'notempty' => __d('be', 'A title is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Title'),
                        'placeholder' => __d('be', 'Title'),
                    ]
                ],
                'mp4' => [
                    'fieldset' => __d('be', 'Files'),
                    'translate' => false,
                    'required' => [
                        'on' => ['insert'],
                        'rules' => [
                            'notempty' => __d('be', 'A .mp4 file is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'file',
                        'label' => __d('be', 'File (.mp4)'),
                        'accept' => '.mp4',
                        'templateVars' => ['help' => '<div class="help-message">' . __d('be', '.mp4 file') . '</div>'],
                    ],
                    'callbacks' => [
                        'beforesave' => 'savefile',
                        'beforedelete' => 'deletefile',
                    ]
                ],
                'webm' => [
                    'fieldset' => __d('be', 'Files'),
                    'translate' => false,
                    'required' => false,
                    'attr' => [
                        'type' => 'file',
                        'label' => __d('be', 'File (.webm)'),
                        'accept' => '.webm',
                        'templateVars' => ['help' => '<div class="help-message">' . __d('be', '.webm file') . '</div>'],
                    ],
                    'callbacks' => [
                        'beforesave' => 'savefile',
                        'beforedelete' => 'deletefile',
                    ]
                ],
                'ogv' => [
                    'fieldset' => __d('be', 'Files'),
                    'translate' => false,
                    'required' => false,
                    'attr' => [
                        'type' => 'file',
                        'label' => __d('be', 'File (.ogv or .ogg)'),
                        'accept' => '.ogv,.ogg',
                        'templateVars' => ['help' => '<div class="help-message">' . __d('be', '.ogv/.ogg file') . '</div>'],
                    ],
                    'callbacks' => [
                        'beforesave' => 'savefile',
                        'beforedelete' => 'deletefile',
                    ]
                ],
            ]
        ],
        // pool
        'pool' => [
            'active' => true,
            'show' => true,
            'icon' => 'cubes',
            'config' => [
                'active' => false,
                'range' => false,
                'times' => false,
            ],
            'translations' => [
                'type' => __d('be', 'Top offer slider'),
                'menu' => __d('be', 'Top offer sliders'),
                'title' => [
                    'new' => __d('be', 'Create new top offer slider'),
                    'edit' => __d('be', 'Edit top offer slider'),
                ],
                'flash' => [
                    'delete' => [
                        'success' => __d('be', 'The top offer slider has been successfully removed!'),
                        'error' => __d('be', 'An error has occurred, please try again!'),
                    ],
                    'copy' => [
                        'success' => __d('be', 'The top offer slider has been successfully copied!'),
                        'error' => __d('be', 'An error has occurred, please try again!'),
                    ]
                ],
                'buttons' => [
                    'add' => __d('be', 'Add new top offer slider'),
                    'delete' => __d('be', 'Do you really want to delete this top offer slider?'),
                ]
            ],
            'fields' => [
                'type' => [
                    'translate' => false,
                    'required' => [
                        'on' => ['insert', 'update'],
                        'rules' => [
                            'notempty' => __d('be', 'A type is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'select',
                        'label' => __d('be', 'Type'),
                        'empty' => __d('be', '-- Choose a type --'),
                        'options' => [
                            'category' => __d('be', 'Package category'),
                            'custom' => __d('be', 'Custom offers'),
                        ]
                    ]
                ],
                'category' => [
                    'translate' => false,
                    'required' => [
                        'on' => ['insert', 'update'],
                        'rules' => [
                            'notempty' => __d('be', 'An category is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Package category'),
                        'class' => 'selector',
                        'data-selector-max' => 1,
                        'data-selector-category' => 'elements:package',
                        'data-selector-text' => __d('be', 'Select category'),
                    ]
                ],
                'packages' => [
                    'translate' => false,
                    'required' => [
                        'on' => ['insert', 'update'],
                        'rules' => [
                            'notempty' => __d('be', 'A package is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Packages'),
                        'class' => 'selector',
                        'data-selector-element' => 'package',
                        'data-selector-text' => __d('be', 'Select packages'),
                    ]
                ],
                'line1' => [
                    'translate' => true,
                    'required' => [
                        'on' => ['insert', 'update'],
                        'rules' => [
                            'notempty' => __d('be', 'A text is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Line %s', 1),
                        'placeholder' => __d('be', 'Line %s', 1),
                    ]
                ],
                'line2' => [
                    'translate' => true,
                    'required' => [
                        'on' => ['insert', 'update'],
                        'rules' => [
                            'notempty' => __d('be', 'A text is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Line %s', 2),
                        'placeholder' => __d('be', 'Line %s', 2),
                    ]
                ],
            ],
            'dynamic' => [
                'depends' => 'type',
                'fields' => [
                    'category' => ['category', 'line1', 'line2'],
                    'custom' => ['packages', 'line1', 'line2'],
                ]
            ]
        ],
        // overviews
        'overview' => [
            'active' => true,
            'show' => true,
            'searchable' => [
                'func' => 'category',
                'settings' => [
                    'package' => [
                        'field' => 'packages',
                        'search' => ['title', 'teaser'],
                        'link' => true,
                    ],
                    'room' => [
                        'field' => 'rooms',
                        'search' => ['title', 'content'],
                        'link' => true,
                    ],
                    'room-total' => [
                        'field' => 'rooms',
                        'search' => [],
                        'link' => true,
                    ],
                    'treatment' => [
                        'field' => 'treatments',
                        'search' => ['title', 'content'],
                    ]
                ]
            ],
            'icon' => 'th',
            'config' => [
                'active' => false,
                'range' => false,
                'times' => false,
            ],
            'translations' => [
                'type' => __d('be', 'Overview'),
                'menu' => __d('be', 'Overviews'),
                'title' => [
                    'new' => __d('be', 'Create new overview'),
                    'edit' => __d('be', 'Edit overview'),
                ],
                'flash' => [
                    'delete' => [
                        'success' => __d('be', 'The overview has been successfully removed!'),
                        'error' => __d('be', 'An error has occurred, please try again!'),
                    ],
                    'copy' => [
                        'success' => __d('be', 'The overview has been successfully copied!'),
                        'error' => __d('be', 'An error has occurred, please try again!'),
                    ]
                ],
                'buttons' => [
                    'add' => __d('be', 'Add new overview'),
                    'delete' => __d('be', 'Do you really want to delete this overview?'),
                ]
            ],
            'fields' => [
                'type' => [
                    'translate' => false,
                    'required' => [
                        'on' => ['insert', 'update'],
                        'rules' => [
                            'notempty' => __d('be', 'A type is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'select',
                        'label' => __d('be', 'Type'),
                        'empty' => __d('be', '-- Choose a type --'),
                        'options' => [
                            'room' => __d('be', 'Rooms'),
                            'room-total' => __d('be', 'Room total'),
                            'package' => __d('be', 'Packages'),
                            'treatment' => __d('be', 'Treatments'),
                        ]
                    ]
                ],
                'headline' => [
                    'translate' => true,
                    'required' => [
                        'on' => ['insert', 'update'],
                        'rules' => [
                            'notempty' => __d('be', 'A headline is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Headline'),
                        'placeholder' => __d('be', 'Headline'),
                    ]
                ],
                'content' => [
                    'translate' => true,
                    'attr' => [
                        'type' => 'textarea',
                        'label' => __d('be', 'Content'),
                        'placeholder' => __d('be', 'Content'),
                        'class' => 'wysiwyg',
                        'data-config' => 'teaser',
                    ]
                ],
                'rooms' => [
                    'translate' => false,
                    'required' => [
                        'on' => ['insert', 'update'],
                        'rules' => [
                            'notempty' => __d('be', 'An room category is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Room category'),
                        'class' => 'selector',
                        'data-selector-max' => 1,
                        'data-selector-category' => 'elements:room',
                        'data-selector-text' => __d('be', 'Select category'),
                    ]
                ],
                'packages' => [
                    'translate' => false,
                    'required' => [
                        'on' => ['insert', 'update'],
                        'rules' => [
                            'notempty' => __d('be', 'A package category is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Package category'),
                        'class' => 'selector',
                        'data-selector-max' => 1,
                        'data-selector-category' => 'elements:package',
                        'data-selector-text' => __d('be', 'Select category'),
                    ]
                ],
                'treatments' => [
                    'translate' => false,
                    'required' => [
                        'on' => ['insert', 'update'],
                        'rules' => [
                            'notempty' => __d('be', 'A treatment category is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Treatment category'),
                        'class' => 'selector',
                        'data-selector-max' => 1,
                        'data-selector-category' => 'elements:treatment',
                        'data-selector-text' => __d('be', 'Select category'),
                    ]
                ],
            ],
            'dynamic' => [
                'depends' => 'type',
                'fields' => [
                    'room' => ['rooms'],
                    'room-total' => [],
                    'package' => ['packages'],
                    'treatment' => ['headline', 'content', 'treatments'],
                ]
            ]
        ],
        // specials
        'special' => [
            'active' => true,
            'show' => true,
            'searchable' => [
                'func' => 'special',
                'settings' => [
                    'headline' => [
                        'search' => ['headline']
                    ],
                    'textblock' => [
                        'search' => ['textblock']
                    ],
                ]
            ],
            'icon' => 'magic',
            'config' => [
                'active' => false,
                'range' => false,
                'times' => false,
            ],
            'translations' => [
                'type' => __d('be', 'Special'),
                'menu' => __d('be', 'Specials'),
                'title' => [
                    'new' => __d('be', 'Create new special element'),
                    'edit' => __d('be', 'Edit special element'),
                ],
                'flash' => [
                    'delete' => [
                        'success' => __d('be', 'The special element has been successfully removed!'),
                        'error' => __d('be', 'An error has occurred, please try again!'),
                    ],
                    'copy' => [
                        'success' => __d('be', 'The special element has been successfully copied!'),
                        'error' => __d('be', 'An error has occurred, please try again!'),
                    ]
                ],
                'buttons' => [
                    'add' => __d('be', 'Add new special element'),
                    'delete' => __d('be', 'Do you really want to delete this special element?'),
                ]
            ],
            'fields' => [
                'type' => [
                    'translate' => false,
                    'required' => [
                        'on' => ['insert', 'update'],
                        'rules' => [
                            'notempty' => __d('be', 'A type is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'select',
                        'label' => __d('be', 'Type'),
                        'empty' => __d('be', '-- Choose a type --'),
                        'options' => [
                            'headline' => __d('be', 'Headline'),
                            'textblock' => __d('be', 'Textblock'),
                            //'lwd-bozen' => __d('be', 'Weather') . ' (Landeswetterdienst Bozen)',
                            'zamg' => __d('be', 'Weather') . ' (ZAMG)',
                            //'wunderground' => __d('be', 'Weather') . ' (Wunderground)',
                            'routeplanner' => __d('be', 'Routeplanner'),
                            'youtube' => __d('be', 'YouTube'),
                            'vimeo' => __d('be', 'Vimeo'),
                            'webcam' => __d('be', 'Webcam'),
                            'sitemap' => __d('be', 'Sitemap'),
                            'search' => __d('be', 'Search'),
                            //'seekda' => 'Seekda',
                            'panorama' => __d('be', 'Panorama'),
                            //'tour' => __d('be', '360° Tour'),
                            //'vioma' => 'Vioma',
                            //'incert-deposit' => 'Incert (' . __d('be', 'deposit') . ')',
                            //'incert-voucher' => 'Incert (' . __d('be', 'voucher') . ')',
                            'children' => __d('be', 'Children prices'),
                            //'wellness' => __d('be', 'Wellness plan'),
                            'serfaus-fiss-ladis' => __d('be', 'Online booking'),
                        ]
                    ]
                ],
                'anchor' => [
                    'translate' => false,
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Anchor'),
                        'placeholder' => __d('be', 'Anchor'),
                    ],
                ],
                'headline' => [
                    'translate' => true,
                    'required' => [
                        'on' => ['insert', 'update'],
                        'rules' => [
                            'notempty' => __d('be', 'A headline is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Headline'),
                        'placeholder' => __d('be', 'Headline'),
                    ],
                ],
                'textblock' => [
                    'translate' => true,
                    'required' => [
                        'on' => ['insert', 'update'],
                        'rules' => [
                            'notempty' => __d('be', 'A content is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'textarea',
                        'label' => __d('be', 'Content'),
                        'placeholder' => __d('be', 'Content'),
                        'class' => 'wysiwyg',
                    ]
                ],
                'region' => [
                    'translate' => false,
                    'required' => [
                        'on' => ['insert', 'update'],
                        'rules' => [
                            'notempty' => __d('be', 'A region is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'select',
                        'label' => __d('be', 'Region'),
                        'placeholder' => __d('be', 'Region'),
                        'default' => 'meteocons-light',
                        'options' => [
                            '1' => 'Schlanders',
                            '2' => 'Meran',
                            '3' => 'Bozen',
                            '4' => 'Sterzing',
                            '5' => 'Brixen',
                            '6' => 'Bruneck',
                        ]
                    ],
                ],
                'file' => [
                    'translate' => false,
                    'required' => [
                        'on' => ['insert', 'update'],
                        'rules' => [
                            'notempty' => __d('be', 'A file is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'File'),
                        'placeholder' => __d('be', 'File'),
                    ],
                ],
                'key' => [
                    'translate' => false,
                    'required' => [
                        'on' => ['insert', 'update'],
                        'rules' => [
                            'notempty' => __d('be', 'An API key is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'API Key'),
                        'placeholder' => __d('be', 'API Key'),
                        'templateVars' => ['help' => '<div class="help-message">' . __d('be', 'Get API key from %s (create new project for every website!)', '<a href="http://www.wunderground.com" target="_blank">http://www.wunderground.com</a>') . '</div>'],
                    ],
                ],
                'zmw' => [
                    'translate' => false,
                    'required' => [
                        'on' => ['insert', 'update'],
                        'rules' => [
                            'notempty' => __d('be', 'A ZMW is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'ZMW'),
                        'placeholder' => __d('be', 'ZMW'),
                        'templateVars' => ['help' => '<div class="help-message">' . __d('be', 'Open f.e. "%s" to get ZMW', '<a href="http://autocomplete.wunderground.com/aq?query=Innsbruck" target="_blank">http://autocomplete.wunderground.com/aq?query=Innsbruck</a>') . '</div>'],
                    ],
                ],
                'hotelId' => [
                    'translate' => false,
                    'required' => [
                        'on' => ['insert', 'update'],
                        'rules' => [
                            'notempty' => __d('be', 'A hotel id is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Hotel id'),
                        'placeholder' => 'AT_HOTEL_IBK',
                        'templateVars' => ['help' => '<div class="help-message">' . __d('be', 'You find it in the Seekda channel manager under Dynamic Shop / Integration / erweitert') . '</div>'],
                    ],
                ],
                'seekdaApiKey' => [
                    'translate' => false,
                    'required' => [
                        'on' => ['insert', 'update'],
                        'rules' => [
                            'notempty' => __d('be', 'A api key id is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Api key'),
                        'placeholder' => '00000000-0000-0000-0000-000000000000',
                        'templateVars' => ['help' => '<div class="help-message">' . __d('be', 'You find it in the Seekda channel manager under Dynamic Shop / Integration / erweitert') . '</div>'],
                    ],
                ],
                'preloadImage' => [
                    'translate' => false,
                    'required' => false,
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Preload image'),
                        'placeholder' => '/frontend/img/load.gif',
                        'templateVars' => ['help' => '<div class="help-message">' . __d('be', 'If blank, the default loader is used.') . '</div>'],
                    ],
                ],
                'font' => [
                    'translate' => false,
                    'required' => [
                        'on' => ['insert', 'update'],
                        'rules' => [
                            'notempty' => __d('be', 'A font is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'select',
                        'label' => __d('be', 'Font'),
                        'placeholder' => __d('be', 'Font'),
                        'default' => 'meteocons-light',
                        'templateVars' => ['help' => '<div class="help-message">' . __d('be', 'Choose a font to display the weather icons.') . '</div>'],
                        'options' => [
                            'meteocons-light' => 'Meteocons Light',
                            'meteocons-full' => 'Meteocons Full',
                        ]
                    ],
                ],
                'address' => [
                    'translate' => false,
                    'required' => [
                        'on' => ['insert', 'update'],
                        'rules' => [
                            'notempty' => __d('be', 'A address is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Address'),
                        'placeholder' => __d('be', 'Address'),
                    ],
                ],
                'latitude' => [
                    'translate' => false,
                    'required' => [
                        'on' => ['insert', 'update'],
                        'rules' => [
                            'notempty' => __d('be', 'The latitude is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Latitude'),
                        'placeholder' => __d('be', 'Latitude'),
                    ],
                ],
                'longitude' => [
                    'translate' => false,
                    'required' => [
                        'on' => ['insert', 'update'],
                        'rules' => [
                            'notempty' => __d('be', 'The longitude is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Longitude'),
                        'placeholder' => __d('be', 'Longitude'),
                    ],
                ],
                'zoom' => [
                    'translate' => false,
                    'required' => [
                        'on' => ['insert', 'update'],
                        'rules' => [
                            'notempty' => __d('be', 'The zoom is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Zoom'),
                        'placeholder' => __d('be', 'Zoom'),
                    ],
                ],
                'video' => [
                    'translate' => false,
                    'required' => [
                        'on' => ['insert', 'update'],
                        'rules' => [
                            'notempty' => __d('be', 'The video id is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Video ID'),
                        'placeholder' => __d('be', 'Video ID'),
                    ],
                ],
                'webcam' => [
                    'translate' => false,
                    'required' => [
                        'on' => ['insert', 'update'],
                        'rules' => [
                            'notempty' => __d('be', 'A webcam is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'select',
                        'label' => __d('be', 'Webcam'),
                        'empty' => __d('be', '-- Choose a webcam --'),
                        'options' => [
                            '5688' => 'Kinderschneealm - Serfaus',
                            '5681' => 'Masner - Serfaus',
                            '5682' => 'Plansegg - Serfaus',
                            '5539' => 'Fiss Nordseite',
                            '5683' => 'Murmliwasser',
                            '5689' => 'Erlebnispark Hög',
                            '5544' => 'Wolfsee Fiss',
                            '5542' => 'Möseralm-Fiss',
                            '5541' => 'Schöngampalm Fiss',
                            'https://file.wetter.at/mowis/webcams/670030/mowis-serfaus-t_01.jpg' => 'Hotel Webcam',
                        ]
                    ]
                ],
                'map' => [
                    'translate' => false,
                    'required' => [
                        'on' => ['insert', 'update'],
                        'rules' => [
                            'notempty' => __d('be', 'A map type is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'select',
                        'label' => __d('be', 'Map type'),
                        'empty' => __d('be', '-- Choose a map type --'),
                        'options' => [
                            'ROADMAP' => 'ROADMAP',
                            'SATELLITE' => 'SATELLITE',
                            'HYBRID' => 'HYBRID',
                            'TERRAIN' => 'TERRAIN',
                        ]
                    ]
                ],
                'wellness' => [
                    'translate' => false,
                    'required' => [
                        'on' => ['insert', 'update'],
                        'rules' => [
                            'notempty' => __d('be', 'A map is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'select',
                        'label' => __d('be', 'Wellness map'),
                        'empty' => __d('be', '-- Choose a map --'),
                        'options' => [
                            'paradise' => 'Wellness-Paradies',
                            'sauna' => 'Saunawelt',
                            'outdoor' => 'Fitnessraum und Außenbereich',
                            'lady-spa' => 'Lady-SPA',
                            'private-spa' => 'Private-SPA',
                        ]
                    ]
                ],
                'col1' => [
                    'fieldset' => __d('be', 'Columns'),
                    'translate' => true,
                    'required' => [
                        'on' => ['insert', 'update'],
                        'rules' => [
                            'notempty' => __d('be', 'A headline is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Headline %s', 1),
                        'placeholder' => __d('be', 'Headline %s', 1),
                        'templateVars' => ['help' => '<div class="help-message">' . __d('be', 'Headline for column %s', 1) . '</div>'],
                    ],
                ],
                'col2' => [
                    'fieldset' => __d('be', 'Columns'),
                    'translate' => true,
                    'required' => [
                        'on' => ['insert', 'update'],
                        'rules' => [
                            'notempty' => __d('be', 'A headline is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Headline %s', 2),
                        'placeholder' => __d('be', 'Headline %s', 2),
                        'templateVars' => ['help' => '<div class="help-message">' . __d('be', 'Headline for column %s', 2) . '</div>'],
                    ],
                ],
                'col3' => [
                    'fieldset' => __d('be', 'Columns'),
                    'translate' => true,
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Headline %s', 3),
                        'placeholder' => __d('be', 'Headline %s', 3),
                        'templateVars' => ['help' => '<div class="help-message">' . __d('be', 'Headline for column %s', 3) . '</div>'],
                    ],
                ],
                'col4' => [
                    'fieldset' => __d('be', 'Columns'),
                    'translate' => true,
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Headline %s', 4),
                        'placeholder' => __d('be', 'Headline %s', 4),
                        'templateVars' => ['help' => '<div class="help-message">' . __d('be', 'Headline for column %s', 4) . '</div>'],
                    ],
                ],
                'line1' => [
                    'fieldset' => __d('be', 'Line %s', 1),
                    'translate' => true,
                    'required' => [
                        'on' => ['insert', 'update'],
                        'rules' => [
                            'notempty' => __d('be', 'A description is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Description'),
                        'placeholder' => __d('be', 'Description'),
                        'templateVars' => ['help' => '<div class="help-message">' . __d('be', 'Description for line %s', 1) . '</div>'],
                    ],
                ],
                'value-1-1' => [
                    'fieldset' => __d('be', 'Line %s', 1),
                    'translate' => false,
                    'required' => [
                        'on' => ['insert', 'update'],
                        'rules' => [
                            'notempty' => __d('be', 'A value is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Value %s', 1),
                        'placeholder' => __d('be', 'Value %s', 1),
                        'templateVars' => ['help' => '<div class="help-message">' . __d('be', 'Value %s for line %s', 1, 1) . '</div>'],
                    ],
                ],
                'value-1-2' => [
                    'fieldset' => __d('be', 'Line %s', 1),
                    'translate' => false,
                    'required' => [
                        'on' => ['insert', 'update'],
                        'rules' => [
                            'notempty' => __d('be', 'A value is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Value %s', 2),
                        'placeholder' => __d('be', 'Value %s', 2),
                        'templateVars' => ['help' => '<div class="help-message">' . __d('be', 'Value %s for line %s', 2, 1) . '</div>'],
                    ],
                ],
                'value-1-3' => [
                    'fieldset' => __d('be', 'Line %s', 1),
                    'translate' => false,
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Value %s', 3),
                        'placeholder' => __d('be', 'Value %s', 3),
                        'templateVars' => ['help' => '<div class="help-message">' . __d('be', 'Value %s for line %s', 3, 1) . '</div>'],
                    ],
                ],
                'value-1-4' => [
                    'fieldset' => __d('be', 'Line %s', 1),
                    'translate' => false,
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Value %s', 4),
                        'placeholder' => __d('be', 'Value %s', 4),
                        'templateVars' => ['help' => '<div class="help-message">' . __d('be', 'Value %s for line %s', 4, 1) . '</div>'],
                    ],
                ],
                'line2' => [
                    'fieldset' => __d('be', 'Line %s', 2),
                    'translate' => true,
                    'required' => [
                        'on' => ['insert', 'update'],
                        'rules' => [
                            'notempty' => __d('be', 'A description is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Description'),
                        'placeholder' => __d('be', 'Description'),
                        'templateVars' => ['help' => '<div class="help-message">' . __d('be', 'Description for line %s', 2) . '</div>'],
                    ],
                ],
                'value-2-1' => [
                    'fieldset' => __d('be', 'Line %s', 2),
                    'translate' => false,
                    'required' => [
                        'on' => ['insert', 'update'],
                        'rules' => [
                            'notempty' => __d('be', 'A value is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Value %s', 1),
                        'placeholder' => __d('be', 'Value %s', 1),
                        'templateVars' => ['help' => '<div class="help-message">' . __d('be', 'Value %s for line %s', 1, 2) . '</div>'],
                    ],
                ],
                'value-2-2' => [
                    'fieldset' => __d('be', 'Line %s', 2),
                    'translate' => false,
                    'required' => [
                        'on' => ['insert', 'update'],
                        'rules' => [
                            'notempty' => __d('be', 'A value is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Value %s', 2),
                        'placeholder' => __d('be', 'Value %s', 2),
                        'templateVars' => ['help' => '<div class="help-message">' . __d('be', 'Value %s for line %s', 2, 2) . '</div>'],
                    ],
                ],
                'value-2-3' => [
                    'fieldset' => __d('be', 'Line %s', 2),
                    'translate' => false,
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Value %s', 3),
                        'placeholder' => __d('be', 'Value %s', 3),
                        'templateVars' => ['help' => '<div class="help-message">' . __d('be', 'Value %s for line %s', 3, 2) . '</div>'],
                    ],
                ],
                'value-2-4' => [
                    'fieldset' => __d('be', 'Line %s', 2),
                    'translate' => false,
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Value %s', 4),
                        'placeholder' => __d('be', 'Value %s', 4),
                        'templateVars' => ['help' => '<div class="help-message">' . __d('be', 'Value %s for line %s', 4, 2) . '</div>'],
                    ],
                ],
                'line3' => [
                    'fieldset' => __d('be', 'Line %s', 3),
                    'translate' => true,
                    'required' => [
                        'on' => ['insert', 'update'],
                        'rules' => [
                            'notempty' => __d('be', 'A description is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Description'),
                        'placeholder' => __d('be', 'Description'),
                        'templateVars' => ['help' => '<div class="help-message">' . __d('be', 'Description for line %s', 3) . '</div>'],
                    ],
                ],
                'value-3-1' => [
                    'fieldset' => __d('be', 'Line %s', 3),
                    'translate' => false,
                    'required' => [
                        'on' => ['insert', 'update'],
                        'rules' => [
                            'notempty' => __d('be', 'A value is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Value %s', 1),
                        'placeholder' => __d('be', 'Value %s', 1),
                        'templateVars' => ['help' => '<div class="help-message">' . __d('be', 'Value %s for line %s', 1, 3) . '</div>'],
                    ],
                ],
                'value-3-2' => [
                    'fieldset' => __d('be', 'Line %s', 3),
                    'translate' => false,
                    'required' => [
                        'on' => ['insert', 'update'],
                        'rules' => [
                            'notempty' => __d('be', 'A value is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Value %s', 2),
                        'placeholder' => __d('be', 'Value %s', 2),
                        'templateVars' => ['help' => '<div class="help-message">' . __d('be', 'Value %s for line %s', 2, 3) . '</div>'],
                    ],
                ],
                'value-3-3' => [
                    'fieldset' => __d('be', 'Line %s', 3),
                    'translate' => false,
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Value %s', 3),
                        'placeholder' => __d('be', 'Value %s', 3),
                        'templateVars' => ['help' => '<div class="help-message">' . __d('be', 'Value %s for line %s', 3, 3) . '</div>'],
                    ],
                ],
                'value-3-4' => [
                    'fieldset' => __d('be', 'Line %s', 3),
                    'translate' => false,
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Value %s', 4),
                        'placeholder' => __d('be', 'Value %s', 4),
                        'templateVars' => ['help' => '<div class="help-message">' . __d('be', 'Value %s for line %s', 4, 3) . '</div>'],
                    ],
                ],
                'line4' => [
                    'fieldset' => __d('be', 'Line %s', 4),
                    'translate' => true,
                    'required' => [
                        'on' => ['insert', 'update'],
                        'rules' => [
                            'notempty' => __d('be', 'A description is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Description'),
                        'placeholder' => __d('be', 'Description'),
                        'templateVars' => ['help' => '<div class="help-message">' . __d('be', 'Description for line %s', 4) . '</div>'],
                    ],
                ],
                'value-4-1' => [
                    'fieldset' => __d('be', 'Line %s', 4),
                    'translate' => false,
                    'required' => [
                        'on' => ['insert', 'update'],
                        'rules' => [
                            'notempty' => __d('be', 'A value is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Value %s', 1),
                        'placeholder' => __d('be', 'Value %s', 1),
                        'templateVars' => ['help' => '<div class="help-message">' . __d('be', 'Value %s for line %s', 1, 4) . '</div>'],
                    ],
                ],
                'value-4-2' => [
                    'fieldset' => __d('be', 'Line %s', 4),
                    'translate' => false,
                    'required' => [
                        'on' => ['insert', 'update'],
                        'rules' => [
                            'notempty' => __d('be', 'A value is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Value %s', 2),
                        'placeholder' => __d('be', 'Value %s', 2),
                        'templateVars' => ['help' => '<div class="help-message">' . __d('be', 'Value %s for line %s', 2, 4) . '</div>'],
                    ],
                ],
                'value-4-3' => [
                    'fieldset' => __d('be', 'Line %s', 4),
                    'translate' => false,
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Value %s', 3),
                        'placeholder' => __d('be', 'Value %s', 3),
                        'templateVars' => ['help' => '<div class="help-message">' . __d('be', 'Value %s for line %s', 3, 4) . '</div>'],
                    ],
                ],
                'value-4-4' => [
                    'fieldset' => __d('be', 'Line %s', 4),
                    'translate' => false,
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Value %s', 4),
                        'placeholder' => __d('be', 'Value %s', 4),
                        'templateVars' => ['help' => '<div class="help-message">' . __d('be', 'Value %s for line %s', 4, 4) . '</div>'],
                    ],
                ],
                'line5' => [
                    'fieldset' => __d('be', 'Line %s', 5),
                    'translate' => true,
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Description'),
                        'placeholder' => __d('be', 'Description'),
                        'templateVars' => ['help' => '<div class="help-message">' . __d('be', 'Description for line %s', 5) . '</div>'],
                    ],
                ],
                'value-5-1' => [
                    'fieldset' => __d('be', 'Line %s', 5),
                    'translate' => false,
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Value %s', 1),
                        'placeholder' => __d('be', 'Value %s', 1),
                        'templateVars' => ['help' => '<div class="help-message">' . __d('be', 'Value %s for line %s', 1, 5) . '</div>'],
                    ],
                ],
                'value-5-2' => [
                    'fieldset' => __d('be', 'Line %s', 5),
                    'translate' => false,
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Value %s', 2),
                        'placeholder' => __d('be', 'Value %s', 2),
                        'templateVars' => ['help' => '<div class="help-message">' . __d('be', 'Value %s for line %s', 2, 5) . '</div>'],
                    ],
                ],
                'value-5-3' => [
                    'fieldset' => __d('be', 'Line %s', 5),
                    'translate' => false,
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Value %s', 3),
                        'placeholder' => __d('be', 'Value %s', 3),
                        'templateVars' => ['help' => '<div class="help-message">' . __d('be', 'Value %s for line %s', 3, 5) . '</div>'],
                    ],
                ],
                'value-5-4' => [
                    'fieldset' => __d('be', 'Line %s', 5),
                    'translate' => false,
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Value %s', 4),
                        'placeholder' => __d('be', 'Value %s', 4),
                        'templateVars' => ['help' => '<div class="help-message">' . __d('be', 'Value %s for line %s', 4, 5) . '</div>'],
                    ],
                ],
            ],
            'dynamic' => [
                'depends' => 'type',
                'fields' => [
                    'headline' => ['headline',],
                    'textblock' => ['textblock',],
                    //'lwd-bozen' => ['region'],
                    'zamg' => ['file'],
                    //'wunderground' => ['key','zmw','font'],
                    'routeplanner' => ['address', 'latitude', 'longitude', 'zoom', 'map'],
                    'youtube' => ['video'],
                    'vimeo' => ['video'],
                    'webcam' => ['headline', 'webcam'],
                    'sitemap' => [],
                    'search' => [],
                    'panorama' => [],
                    'seekda' => ['hotelId', 'seekdaApiKey', 'preloadImage'],
                    'vioma' => [],
                    'children' => ['anchor', 'headline', 'textblock', 'col1', 'col2', 'col3', 'col4', 'line1', 'line2', 'line3', 'line4', 'line5', 'value-1-1', 'value-1-2', 'value-1-3', 'value-1-4', 'value-2-1', 'value-2-2', 'value-2-3', 'value-2-4', 'value-3-1', 'value-3-2', 'value-3-3', 'value-3-4', 'value-4-1', 'value-4-2', 'value-4-3', 'value-4-4', 'value-5-1', 'value-5-2', 'value-5-3', 'value-5-4'],
                    'wellness' => ['headline', 'textblock', 'wellness'],
                    'serfaus-fiss-ladis' => [],
                ]
            ]
        ],
    ],
];

return $_elements;
