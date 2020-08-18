<?php

use Illuminate\Database\Seeder;

class EligibleRegionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $regions = array(
			array('region' => 'United States'),
			array('region' => 'Canada'),
			array('region' => 'Afghanistan'),
			array('region' => 'Albania'),
			array('region' => 'Algeria'),
			array('region' => 'American Samoa'),
			array('region' => 'Andorra'),
			array('region' => 'Angola'),
			array('region' => 'Anguilla'),
			array('region' => 'Antarctica'),
			array('region' => 'Antigua and/or Barbuda'),
			array('region' => 'Argentina'),
			array('region' => 'Armenia'),
			array('region' => 'Aruba'),
			array('region' => 'Australia'),
			array('region' => 'Austria'),
			array('region' => 'Azerbaijan'),
			array('region' => 'Bahamas'),
			array('region' => 'Bahrain'),
			array('region' => 'Bangladesh'),
			array('region' => 'Barbados'),
			array('region' => 'Belarus'),
			array('region' => 'Belgium'),
			array('region' => 'Belize'),
			array('region' => 'Benin'),
			array('region' => 'Bermuda'),
			array('region' => 'Bhutan'),
			array('region' => 'Bolivia'),
			array('region' => 'Bosnia and Herzegovina'),
			array('region' => 'Botswana'),
			array('region' => 'Bouvet Island'),
			array('region' => 'Brazil'),
			array('region' => 'British lndian Ocean Territory'),
			array('region' => 'Brunei Darussalam'),
			array('region' => 'Bulgaria'),
			array('region' => 'Burkina Faso'),
			array('region' => 'Burundi'),
			array('region' => 'Cambodia'),
			array('region' => 'Cameroon'),
			array('region' => 'Cape Verde'),
			array('region' => 'Cayman Islands'),
			array('region' => 'Central African Republic'),
			array('region' => 'Chad'),
			array('region' => 'Chile'),
			array('region' => 'China'),
			array('region' => 'Christmas Island'),
			array('region' => 'Cocos (Keeling) Islands'),
			array('region' => 'Colombia'),
			array('region' => 'Comoros'),
			array('region' => 'Congo'),
			array('region' => 'Cook Islands'),
			array('region' => 'Costa Rica'),
			array('region' => 'Croatia (Hrvatska)'),
			array('region' => 'Cuba'),
			array('region' => 'Cyprus'),
			array('region' => 'Czech Republic'),
			array('region' => 'Democratic Republic of Congo'),
			array('region' => 'Denmark'),
			array('region' => 'Djibouti'),
			array('region' => 'Dominica'),
			array('region' => 'Dominican Republic'),
			array('region' => 'East Timor'),
			array('region' => 'Ecudaor'),
			array('region' => 'Egypt'),
			array('region' => 'El Salvador'),
			array('region' => 'Equatorial Guinea'),
			array('region' => 'Eritrea'),
			array('region' => 'Estonia'),
			array('region' => 'Ethiopia'),
			array('region' => 'Falkland Islands (Malvinas)'),
			array('region' => 'Faroe Islands'),
			array('region' => 'Fiji'),
			array('region' => 'Finland'),
			array('region' => 'France'),
			array('region' => 'France, Metropolitan'),
			array('region' => 'French Guiana'),
			array('region' => 'French Polynesia'),
			array('region' => 'French Southern Territories'),
			array('region' => 'Gabon'),
			array('region' => 'Gambia'),
			array('region' => 'Georgia'),
			array('region' => 'Germany'),
			array('region' => 'Ghana'),
			array('region' => 'Gibraltar'),
			array('region' => 'Greece'),
			array('region' => 'Greenland'),
			array('region' => 'Grenada'),
			array('region' => 'Guadeloupe'),
			array('region' => 'Guam'),
			array('region' => 'Guatemala'),
			array('region' => 'Guinea'),
			array('region' => 'Guinea-Bissau'),
			array('region' => 'Guyana'),
			array('region' => 'Haiti'),
			array('region' => 'Heard and Mc Donald Islands'),
			array('region' => 'Honduras'),
			array('region' => 'Hong Kong'),
			array('region' => 'Hungary'),
			array('region' => 'Iceland'),
			array('region' => 'India'),
			array('region' => 'Indonesia'),
			array('region' => 'Iran (Islamic Republic of)'),
			array('region' => 'Iraq'),
			array('region' => 'Ireland'),
			array('region' => 'Israel'),
			array('region' => 'Italy'),
			array('region' => 'Ivory Coast'),
			array('region' => 'Jamaica'),
			array('region' => 'Japan'),
			array('region' => 'Jordan'),
			array('region' => 'Kazakhstan'),
			array('region' => 'Kenya'),
			array('region' => 'Kiribati'),
			array('region' => 'Korea, Democratic People\'s Republic of'),
			array('region' => 'Korea, Republic of'),
			array('region' => 'Kuwait'),
			array('region' => 'Kyrgyzstan'),
			array('region' => 'Lao People\'s Democratic Republic'),
			array('region' => 'Latvia'),
			array('region' => 'Lebanon'),
			array('region' => 'Lesotho'),
			array('region' => 'Liberia'),
			array('region' => 'Libyan Arab Jamahiriya'),
			array('region' => 'Liechtenstein'),
			array('region' => 'Lithuania'),
			array('region' => 'Luxembourg'),
			array('region' => 'Macau'),
			array('region' => 'Macedonia'),
			array('region' => 'Madagascar'),
			array('region' => 'Malawi'),
			array('region' => 'Malaysia'),
			array('region' => 'Maldives'),
			array('region' => 'Mali'),
			array('region' => 'Malta'),
			array('region' => 'Marshall Islands'),
			array('region' => 'Martinique'),
			array('region' => 'Mauritania'),
			array('region' => 'Mauritius'),
			array('region' => 'Mayotte'),
			array('region' => 'Mexico'),
			array('region' => 'Micronesia, Federated States of'),
			array('region' => 'Moldova, Republic of'),
			array('region' => 'Monaco'),
			array('region' => 'Mongolia'),
			array('region' => 'Montserrat'),
			array('region' => 'Morocco'),
			array('region' => 'Mozambique'),
			array('region' => 'Myanmar'),
			array('region' => 'Namibia'),
			array('region' => 'Nauru'),
			array('region' => 'Nepal'),
			array('region' => 'Netherlands'),
			array('region' => 'Netherlands Antilles'),
			array('region' => 'New Caledonia'),
			array('region' => 'New Zealand'),
			array('region' => 'Nicaragua'),
			array('region' => 'Niger'),
			array('region' => 'Nigeria'),
			array('region' => 'Niue'),
			array('region' => 'Norfork Island'),
			array('region' => 'Northern Mariana Islands'),
			array('region' => 'Norway'),
			array('region' => 'Oman'),
			array('region' => 'Pakistan'),
			array('region' => 'Palau'),
			array('region' => 'Panama'),
			array('region' => 'Papua New Guinea'),
			array('region' => 'Paraguay'),
			array('region' => 'Peru'),
			array('region' => 'Philippines'),
			array('region' => 'Pitcairn'),
			array('region' => 'Poland'),
			array('region' => 'Portugal'),
			array('region' => 'Puerto Rico'),
			array('region' => 'Qatar'),
			array('region' => 'Republic of South Sudan'),
			array('region' => 'Reunion'),
			array('region' => 'Romania'),
			array('region' => 'Russian Federation'),
			array('region' => 'Rwanda'),
			array('region' => 'Saint Kitts and Nevis'),
			array('region' => 'Saint Lucia'),
			array('region' => 'Saint Vincent and the Grenadines'),
			array('region' => 'Samoa'),
			array('region' => 'San Marino'),
			array('region' => 'Sao Tome and Principe'),
			array('region' => 'Saudi Arabia'),
			array('region' => 'Senegal'),
			array('region' => 'Serbia'),
			array('region' => 'Seychelles'),
			array('region' => 'Sierra Leone'),
			array('region' => 'Singapore'),
			array('region' => 'Slovakia'),
			array('region' => 'Slovenia'),
			array('region' => 'Solomon Islands'),
			array('region' => 'Somalia'),
			array('region' => 'South Africa'),
			array('region' => 'South Georgia South Sandwich Islands'),
			array('region' => 'Spain'),
			array('region' => 'Sri Lanka'),
			array('region' => 'St. Helena'),
			array('region' => 'St. Pierre and Miquelon'),
			array('region' => 'Sudan'),
			array('region' => 'Suriname'),
			array('region' => 'Svalbarn and Jan Mayen Islands'),
			array('region' => 'Swaziland'),
			array('region' => 'Sweden'),
			array('region' => 'Switzerland'),
			array('region' => 'Syrian Arab Republic'),
			array('region' => 'Taiwan'),
			array('region' => 'Tajikistan'),
			array('region' => 'Tanzania, United Republic of'),
			array('region' => 'Thailand'),
			array('region' => 'Togo'),
			array('region' => 'Tokelau'),
			array('region' => 'Tonga'),
			array('region' => 'Trinidad and Tobago'),
			array('region' => 'Tunisia'),
			array('region' => 'Turkey'),
			array('region' => 'Turkmenistan'),
			array('region' => 'Turks and Caicos Islands'),
			array('region' => 'Tuvalu'),
			array('region' => 'Uganda'),
			array('region' => 'Ukraine'),
			array('region' => 'United Arab Emirates'),
			array('region' => 'United Kingdom'),
			array('region' => 'United States minor outlying islands'),
			array('region' => 'Uruguay'),
			array('region' => 'Uzbekistan'),
			array('region' => 'Vanuatu'),
			array('region' => 'Vatican City State'),
			array('region' => 'Venezuela'),
			array('region' => 'Vietnam'),
			array('region' => 'Virgin Islands (British)'),
			array('region' => 'Virgin Islands (U.S.)'),
			array('region' => 'Wallis and Futuna Islands'),
			array('region' => 'Western Sahara'),
			array('region' => 'Yemen'),
			array('region' => 'Yugoslavia'),
			array('region' => 'Zaire'),
			array('region' => 'Zambia'),
			array('region' => 'Zimbabwe'),
			array('region' => 'All'),
			array('region' => 'Others'),
		);
		DB::table('eligible_regions')->insert($regions);
    }
}
