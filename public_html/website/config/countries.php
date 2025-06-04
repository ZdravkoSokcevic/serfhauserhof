<?php

use Cake\Core\Configure;

$_countries = [

    'countries' => [

        'AD' => [
            'name' => __d('country', 'Andorra'),
        ],
        'AE' => [
            'name' => __d('country', 'United Arab Emirates')
        ],
        'AF' => [
            'name' => __d('country', 'Afghanistan')
        ],
        'AG' => [
            'name' => __d('country', 'Antigua and Barbuda')
        ],
        'AI' => [
            'name' => __d('country', 'Anguilla')
        ],
        'AL' => [
            'name' => __d('country', 'Albania')
        ],
        'AM' => [
            'name' => __d('country', 'Armenia')
        ],
        'AN' => [
            'name' => __d('country', 'Netherlands Antilles')
        ],
        'AO' => [
            'name' => __d('country', 'Angola')
        ],
        'AQ' => [
            'name' => __d('country', 'Antarctica')
        ],
        'AR' => [
            'name' => __d('country', 'Argentina')
        ],
        'AS' => [
            'name' => __d('country', 'American Samoa')
        ],
        'AT' => [
            'name' => __d('country', 'Austria'),
            'important' => true,
        ],
        'AU' => [
            'name' => __d('country', 'Australia')
        ],
        'AW' => [
            'name' => __d('country', 'Aruba')
        ],
        'AX' => [
            'name' => __d('country', 'Åland Islands')
        ],
        'AZ' => [
            'name' => __d('country', 'Azerbaijan')
        ],
        'BA' => [
            'name' => __d('country', 'Bosnia and Herzegovina')
        ],
        'BB' => [
            'name' => __d('country', 'Barbados')
        ],
        'BD' => [
            'name' => __d('country', 'Bangladesh')
        ],
        'BE' => [
            'name' => __d('country', 'Belgium')
        ],
        'BF' => [
            'name' => __d('country', 'Burkina Faso')
        ],
        'BG' => [
            'name' => __d('country', 'Bulgaria')
        ],
        'BH' => [
            'name' => __d('country', 'Bahrain')
        ],
        'BI' => [
            'name' => __d('country', 'Burundi')
        ],
        'BJ' => [
            'name' => __d('country', 'Benin')
        ],
        'BL' => [
            'name' => __d('country', 'Saint Barthélemy')
        ],
        'BM' => [
            'name' => __d('country', 'Bermuda')
        ],
        'BN' => [
            'name' => __d('country', 'Brunei Darussalam')
        ],
        'BO' => [
            'name' => __d('country', 'Bolivia')
        ],
        'BR' => [
            'name' => __d('country', 'Brazil')
        ],
        'BS' => [
            'name' => __d('country', 'Bahamas')
        ],
        'BT' => [
            'name' => __d('country', 'Bhutan')
        ],
        'BV' => [
            'name' => __d('country', 'Bouvet Island')
        ],
        'BW' => [
            'name' => __d('country', 'Botswana')
        ],
        'BY' => [
            'name' => __d('country', 'Belarus')
        ],
        'BZ' => [
            'name' => __d('country', 'Belize')
        ],
        'CA' => [
            'name' => __d('country', 'Canada')
        ],
        'CC' => [
            'name' => __d('country', 'Cocos Islands')
        ],
        'CF' => [
            'name' => __d('country', 'Central African Republic')
        ],
        'CG' => [
            'name' => __d('country', 'Congo')
        ],
        'CH' => [
            'name' => __d('country', 'Switzerland'),
            'important' => true,
        ],
        'CI' => [
            'name' => __d('country', "Côte D'Ivoire")
        ],
        'CK' => [
            'name' => __d('country', 'Cook Islands')
        ],
        'CL' => [
            'name' => __d('country', 'Chile')
        ],
        'CM' => [
            'name' => __d('country', 'Cameroon')
        ],
        'CN' => [
            'name' => __d('country', 'China')
        ],
        'CO' => [
            'name' => __d('country', 'Colombia')
        ],
        'CR' => [
            'name' => __d('country', 'Costa Rica')
        ],
        'CU' => [
            'name' => __d('country', 'Cuba')
        ],
        'CV' => [
            'name' => __d('country', 'Cape Verde')
        ],
        'CX' => [
            'name' => __d('country', 'Christmas Island')
        ],
        'CY' => [
            'name' => __d('country', 'Cyprus')
        ],
        'CZ' => [
            'name' => __d('country', 'Czech Republic')
        ],
        'DE' => [
            'name' => __d('country', 'Germany'),
            'important' => true,
        ],
        'DJ' => [
            'name' => __d('country', 'Djibouti')
        ],
        'DK' => [
            'name' => __d('country', 'Denmark')
        ],
        'DM' => [
            'name' => __d('country', 'Dominica')
        ],
        'DO' => [
            'name' => __d('country', 'Dominican Republic')
        ],
        'DZ' => [
            'name' => __d('country', 'Algeria')
        ],
        'EC' => [
            'name' => __d('country', 'Ecuador')
        ],
        'EE' => [
            'name' => __d('country', 'Estonia')
        ],
        'EG' => [
            'name' => __d('country', 'Egypt')
        ],
        'EH' => [
            'name' => __d('country', 'Western Sahara')
        ],
        'ER' => [
            'name' => __d('country', 'Eritrea')
        ],
        'ES' => [
            'name' => __d('country', 'Spain')
        ],
        'ET' => [
            'name' => __d('country', 'Ethiopia')
        ],
        'FI' => [
            'name' => __d('country', 'Finland')
        ],
        'FJ' => [
            'name' => __d('country', 'Fiji')
        ],
        'FK' => [
            'name' => __d('country', 'Falkland Islands')
        ],
        'FM' => [
            'name' => __d('country', 'Micronesia')
        ],
        'FO' => [
            'name' => __d('country', 'Faroe Islands')
        ],
        'FR' => [
            'name' => __d('country', 'France')
        ],
        'GA' => [
            'name' => __d('country', 'Gabon')
        ],
        'GB' => [
            'name' => __d('country', 'United Kingdom')
        ],
        'GD' => [
            'name' => __d('country', 'Grenada')
        ],
        'GE' => [
            'name' => __d('country', 'Georgia')
        ],
        'GF' => [
            'name' => __d('country', 'French Guiana')
        ],
        'GG' => [
            'name' => __d('country', 'Guernsey')
        ],
        'GH' => [
            'name' => __d('country', 'Ghana')
        ],
        'GI' => [
            'name' => __d('country', 'Gibraltar')
        ],
        'GL' => [
            'name' => __d('country', 'Greenland')
        ],
        'GM' => [
            'name' => __d('country', 'Gambia')
        ],
        'GN' => [
            'name' => __d('country', 'Guinea')
        ],
        'GP' => [
            'name' => __d('country', 'Guadeloupe')
        ],
        'GQ' => [
            'name' => __d('country', 'Equatorial Guinea')
        ],
        'GR' => [
            'name' => __d('country', 'Greece')
        ],
        'GS' => [
            'name' => __d('country', 'South Georgia and the South Sandwich Islands')
        ],
        'GT' => [
            'name' => __d('country', 'Guatemala')
        ],
        'GU' => [
            'name' => __d('country', 'Guam')
        ],
        'GW' => [
            'name' => __d('country', 'Guinea-Bissau')
        ],
        'GY' => [
            'name' => __d('country', 'Guyana')
        ],
        'HK' => [
            'name' => __d('country', 'Hong Kong')
        ],
        'HM' => [
            'name' => __d('country', 'Heard Island and McDonald Islands')
        ],
        'HN' => [
            'name' => __d('country', 'Honduras')
        ],
        'HR' => [
            'name' => __d('country', 'Croatia')
        ],
        'HT' => [
            'name' => __d('country', 'Haiti')
        ],
        'HU' => [
            'name' => __d('country', 'Hungary')
        ],
        'ID' => [
            'name' => __d('country', 'Indonesia')
        ],
        'IE' => [
            'name' => __d('country', 'Ireland')
        ],
        'IL' => [
            'name' => __d('country', 'Israel')
        ],
        'IM' => [
            'name' => __d('country', 'Isle of Man')
        ],
        'IN' => [
            'name' => __d('country', 'India')
        ],
        'IO' => [
            'name' => __d('country', 'British Indian Ocean Territory')
        ],
        'IQ' => [
            'name' => __d('country', 'Iraq')
        ],
        'IR' => [
            'name' => __d('country', 'Iran')
        ],
        'IS' => [
            'name' => __d('country', 'Iceland')
        ],
        'IT' => [
            'name' => __d('country', 'Italy'),
            'important' => true,
        ],
        'JE' => [
            'name' => __d('country', 'Jersey')
        ],
        'JM' => [
            'name' => __d('country', 'Jamaica')
        ],
        'JO' => [
            'name' => __d('country', 'Jordan')
        ],
        'JP' => [
            'name' => __d('country', 'Japan')
        ],
        'KE' => [
            'name' => __d('country', 'Kenya')
        ],
        'KG' => [
            'name' => __d('country', 'Kyrgyzstan')
        ],
        'KH' => [
            'name' => __d('country', 'Cambodia')
        ],
        'KI' => [
            'name' => __d('country', 'Kiribati')
        ],
        'KM' => [
            'name' => __d('country', 'Comoros')
        ],
        'KN' => [
            'name' => __d('country', 'Saint Kitts and Nevis')
        ],
        'KP' => [
            'name' => __d('country', "North Korea")
        ],
        'KR' => [
            'name' => __d('country', 'South Korea')
        ],
        'KW' => [
            'name' => __d('country', 'Kuwait')
        ],
        'KY' => [
            'name' => __d('country', 'Cayman Islands')
        ],
        'KZ' => [
            'name' => __d('country', 'Kazakhstan')
        ],
        'LA' => [
            'name' => __d('country', "Laos")
        ],
        'LB' => [
            'name' => __d('country', 'Lebanon')
        ],
        'LC' => [
            'name' => __d('country', 'Saint Lucia')
        ],
        'LI' => [
            'name' => __d('country', 'Liechtenstein')
        ],
        'LK' => [
            'name' => __d('country', 'Sri Lanka')
        ],
        'LR' => [
            'name' => __d('country', 'Liberia')
        ],
        'LS' => [
            'name' => __d('country', 'Lesotho')
        ],
        'LT' => [
            'name' => __d('country', 'Lithuania')
        ],
        'LU' => [
            'name' => __d('country', 'Luxembourg')
        ],
        'LV' => [
            'name' => __d('country', 'Latvia')
        ],
        'LY' => [
            'name' => __d('country', 'Libyan Arab Jamahiriya')
        ],
        'MA' => [
            'name' => __d('country', 'Morocco')
        ],
        'MC' => [
            'name' => __d('country', 'Monaco')
        ],
        'MD' => [
            'name' => __d('country', 'Moldova')
        ],
        'ME' => [
            'name' => __d('country', 'Montenegro')
        ],
        'MF' => [
            'name' => __d('country', 'Saint Martin')
        ],
        'MG' => [
            'name' => __d('country', 'Madagascar')
        ],
        'MH' => [
            'name' => __d('country', 'Marshall Islands')
        ],
        'MK' => [
            'name' => __d('country', 'Macedonia')
        ],
        'ML' => [
            'name' => __d('country', 'Mali')
        ],
        'MM' => [
            'name' => __d('country', 'Myanmar')
        ],
        'MN' => [
            'name' => __d('country', 'Mongolia')
        ],
        'MO' => [
            'name' => __d('country', 'Macao')
        ],
        'MP' => [
            'name' => __d('country', 'Northern Mariana Islands')
        ],
        'MQ' => [
            'name' => __d('country', 'Martinique')
        ],
        'MR' => [
            'name' => __d('country', 'Mauritania')
        ],
        'MS' => [
            'name' => __d('country', 'Montserrat')
        ],
        'MT' => [
            'name' => __d('country', 'Malta')
        ],
        'MU' => [
            'name' => __d('country', 'Mauritius')
        ],
        'MV' => [
            'name' => __d('country', 'Maldives')
        ],
        'MW' => [
            'name' => __d('country', 'Malawi')
        ],
        'MX' => [
            'name' => __d('country', 'Mexico')
        ],
        'MY' => [
            'name' => __d('country', 'Malaysia')
        ],
        'MZ' => [
            'name' => __d('country', 'Mozambique')
        ],
        'NA' => [
            'name' => __d('country', 'Namibia')
        ],
        'NC' => [
            'name' => __d('country', 'New Caledonia')
        ],
        'NE' => [
            'name' => __d('country', 'Niger')
        ],
        'NF' => [
            'name' => __d('country', 'Norfolk Island')
        ],
        'NG' => [
            'name' => __d('country', 'Nigeria')
        ],
        'NI' => [
            'name' => __d('country', 'Nicaragua')
        ],
        'NL' => [
            'name' => __d('country', 'Netherlands')
        ],
        'NO' => [
            'name' => __d('country', 'Norway')
        ],
        'NP' => [
            'name' => __d('country', 'Nepal')
        ],
        'NR' => [
            'name' => __d('country', 'Nauru')
        ],
        'NU' => [
            'name' => __d('country', 'Niue')
        ],
        'NZ' => [
            'name' => __d('country', 'New Zealand')
        ],
        'OM' => [
            'name' => __d('country', 'Oman')
        ],
        'PA' => [
            'name' => __d('country', 'Panama')
        ],
        'PE' => [
            'name' => __d('country', 'Peru')
        ],
        'PF' => [
            'name' => __d('country', 'French Polynesia')
        ],
        'PG' => [
            'name' => __d('country', 'Papua New Guinea')
        ],
        'PH' => [
            'name' => __d('country', 'Philippines')
        ],
        'PK' => [
            'name' => __d('country', 'Pakistan')
        ],
        'PL' => [
            'name' => __d('country', 'Poland')
        ],
        'PM' => [
            'name' => __d('country', 'Saint Pierre and Miquelon')
        ],
        'PN' => [
            'name' => __d('country', 'Pitcairn')
        ],
        'PR' => [
            'name' => __d('country', 'Puerto Rico')
        ],
        'PT' => [
            'name' => __d('country', 'Portugal')
        ],
        'PW' => [
            'name' => __d('country', 'Palau')
        ],
        'PY' => [
            'name' => __d('country', 'Paraguay')
        ],
        'QA' => [
            'name' => __d('country', 'Qatar')
        ],
        'RE' => [
            'name' => __d('country', 'Reunion')
        ],
        'RO' => [
            'name' => __d('country', 'Romania')
        ],
        'RS' => [
            'name' => __d('country', 'Serbia')
        ],
        'RU' => [
            'name' => __d('country', 'Russia')
        ],
        'RW' => [
            'name' => __d('country', 'Rwanda')
        ],
        'SA' => [
            'name' => __d('country', 'Saudi Arabia')
        ],
        'SB' => [
            'name' => __d('country', 'Solomon Islands')
        ],
        'SC' => [
            'name' => __d('country', 'Seychelles')
        ],
        'SD' => [
            'name' => __d('country', 'Sudan')
        ],
        'SE' => [
            'name' => __d('country', 'Sweden')
        ],
        'SG' => [
            'name' => __d('country', 'Singapore')
        ],
        'SH' => [
            'name' => __d('country', 'Saint Helena')
        ],
        'SI' => [
            'name' => __d('country', 'Slovenia')
        ],
        'SJ' => [
            'name' => __d('country', 'Svalbard and Jan Mayen')
        ],
        'SK' => [
            'name' => __d('country', 'Slovakia')
        ],
        'SL' => [
            'name' => __d('country', 'Sierra Leone')
        ],
        'SM' => [
            'name' => __d('country', 'San Marino')
        ],
        'SN' => [
            'name' => __d('country', 'Senegal')
        ],
        'SO' => [
            'name' => __d('country', 'Somalia')
        ],
        'SR' => [
            'name' => __d('country', 'Suriname')
        ],
        'ST' => [
            'name' => __d('country', 'Sao Tome and Principe')
        ],
        'SV' => [
            'name' => __d('country', 'El Salvador')
        ],
        'SY' => [
            'name' => __d('country', 'Syrian Arab Republic')
        ],
        'SZ' => [
            'name' => __d('country', 'Swaziland')
        ],
        'TC' => [
            'name' => __d('country', 'Turks and Caicos Islands')
        ],
        'TD' => [
            'name' => __d('country', 'Chad')
        ],
        'TF' => [
            'name' => __d('country', 'French Southern Territories')
        ],
        'TG' => [
            'name' => __d('country', 'Togo')
        ],
        'TH' => [
            'name' => __d('country', 'Thailand')
        ],
        'TJ' => [
            'name' => __d('country', 'Tajikistan')
        ],
        'TK' => [
            'name' => __d('country', 'Tokelau')
        ],
        'TL' => [
            'name' => __d('country', 'Timor-Leste')
        ],
        'TM' => [
            'name' => __d('country', 'Turkmenistan')
        ],
        'TN' => [
            'name' => __d('country', 'Tunisia')
        ],
        'TO' => [
            'name' => __d('country', 'Tonga')
        ],
        'TR' => [
            'name' => __d('country', 'Turkey')
        ],
        'TT' => [
            'name' => __d('country', 'Trinidad and Tobago')
        ],
        'TV' => [
            'name' => __d('country', 'Tuvalu')
        ],
        'TW' => [
            'name' => __d('country', 'Taiwan')
        ],
        'TZ' => [
            'name' => __d('country', 'Tanzania')
        ],
        'UA' => [
            'name' => __d('country', 'Ukraine')
        ],
        'UG' => [
            'name' => __d('country', 'Uganda')
        ],
        'US' => [
            'name' => __d('country', 'United States')
        ],
        'UY' => [
            'name' => __d('country', 'Uruguay')
        ],
        'UZ' => [
            'name' => __d('country', 'Uzbekistan')
        ],
        'VA' => [
            'name' => __d('country', 'Vatican City')
        ],
        'VC' => [
            'name' => __d('country', 'Saint Vincent and the Grenadines')
        ],
        'VE' => [
            'name' => __d('country', 'Venezuela')
        ],
        'VG' => [
            'name' => __d('country', 'Virgin Islands, British')
        ],
        'VI' => [
            'name' => __d('country', 'Virgin Islands, U.S.')
        ],
        'VN' => [
            'name' => __d('country', 'Viet Nam')
        ],
        'VU' => [
            'name' => __d('country', 'Vanuatu')
        ],
        'WF' => [
            'name' => __d('country', 'Wallis And Futuna')
        ],
        'WS' => [
            'name' => __d('country', 'Samoa')
        ],
        'YE' => [
            'name' => __d('country', 'Yemen')
        ],
        'YT' => [
            'name' => __d('country', 'Mayotte')
        ],
        'ZA' => [
            'name' => __d('country', 'South Africa')
        ],
        'ZM' => [
            'name' => __d('country', 'Zambia')
        ],
        'ZW' => [
            'name' => __d('country', 'Zimbabwe')
        ], 
    
    ]
];

return $_countries;