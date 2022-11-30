<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\MediaLibrary\HasMedia\HasMedia;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Contact
 *
 * @package App
 * @property string $company
 * @property string $group
 * @property string $first_name
 * @property string $last_name
 * @property string $phone1
 * @property string $phone2
 * @property string $email
 * @property string $skype
 * @property string $address
 * @property string $city
 * @property string $state_region
 * @property string $zip_postal_code
 * @property string $tax_id
 * @property string $country
*/
class Contact extends Model  implements HasMedia
{
    use HasMediaTrait;
    use Notifiable;
    use SoftDeletes;

    // protected $fillable = ['first_name', 'last_name', 'phone1_code', 'phone1', 'phone2_code', 'phone2', 'email', 'skype', 'address', 'city', 'state_region', 'zip_postal_code', 'tax_id', 'company_id', 'group_id', 'country_id', 'thumbnail', 'currency_id', 'language_id', 'delivery_address', 'user_id', 'name', 'fulladdress',

    //     'password', 'remember_token', 'department_id', 'ticketit_admin', 'ticketit_agent', 'status', 'theme', 'portal_language', 'confirmation_code', 'last_login_from', 'hourly_rate', 'is_user', 'color_theme', 'color_skin'
    // ];
    protected $fillable = ['first_name', 'last_name', 'phone1_code', 'phone1', 'phone2_code', 'phone2', 'email', 'skype', 'address', 'city', 'state_region', 'zip_postal_code', 'tax_id', 'company_id', 'group_id', 'country_id', 'thumbnail', 'currency_id', 'language_id', 'delivery_address', 'user_id', 'name', 'fulladdress',

    'password', 'remember_token', 'department_id', 'ticketit_admin', 'ticketit_agent', 'status', 'theme', 'portal_language', 'confirmation_code', 'last_login_from',  'is_user', 'color_theme', 'color_skin'
    ];
    protected $hidden = [];
    public static $searchable = [ 'first_name', 'last_name', 'address', 'email' ];

    public static function boot()
    {
        parent::boot();

        Contact::observe(new \App\Observers\UserActionsObserver);

        static::addGlobalScope(new \App\Scopes\DefaultOrderScope);
    }



    /**
     * Set to null if empty
     * @param $input
     */
    public function setCompanyIdAttribute($input)
    {
        $this->attributes['company_id'] = $input ? $input : null;
    }

    /**
     * Set to null if empty
     * @param $input
     */
    public function setGroupIdAttribute($input)
    {
        $this->attributes['group_id'] = $input ? $input : null;
    }

    /**
     * Set to null if empty
     * @param $input
     */
    public function setCountryIdAttribute($input)
    {
        $this->attributes['country_id'] = $input ? $input : null;
    }

    public function company()
    {
        return $this->belongsTo(ContactCompany::class, 'company_id');
    }

    public function group()
    {
        return $this->belongsTo(ContactGroup::class, 'group_id')->withTrashed();
    }

    public function contact_type()
    {
        return $this->belongsToMany(ContactType::class, 'contact_contact_type')->withTrashed();
    }

    public function language()
    {
        return $this->belongsToMany(Language::class, 'contact_language')->withTrashed();
    }

    public function languagecode()
    {
        return $this->belongsTo(Language::class, 'language_id')->withDefault()->withTrashed();
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id')->withDefault()->withTrashed();
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency_id')->withDefault()->withTrashed();
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

	/**
     * Set to null if empty
     * @param $input
     */
    public function setFirstNameAttribute($input)
    {
        $this->attributes['first_name'] = $input ? $input : null;
    }

	public function getFirstNameAttribute($input)
    {
        return $input ? $input : null;
    }

    public function role()
    {
        return $this->belongsToMany(Role::class, 'role_user', 'user_id', 'role_id');
    }


    public function getFullBillingAddressAttribute() {
        $delivery_address = ! empty( $this->attributes['delivery_address'] ) ? json_decode( $this->attributes['delivery_address'], true ) : array();

        $first_name = ! empty( $delivery_address['first_name'] ) ? $delivery_address['first_name'] : '';
        $last_name = ! empty( $delivery_address['last_name'] ) ? $delivery_address['last_name'] : '';
        $city = ! empty( $delivery_address['city'] ) ? $delivery_address['city'] : old('city');
        $state_region = ! empty( $delivery_address['state_region'] ) ? $delivery_address['state_region'] : '';
        $address_raw = ! empty( $delivery_address['address'] ) ? $delivery_address['address'] : '';
        $zip_postal_code = ! empty( $delivery_address['zip_postal_code'] ) ? $delivery_address['zip_postal_code'] : '';
        $country_id = ! empty( $delivery_address['country_id'] ) ? $delivery_address['country_id'] : '';

        $address = $first_name;
        if ( ! empty( $last_name ) ) {
            $address .= ' ' . $last_name;
        }
        if ( ! empty( $address_raw ) ) {
            $address .= ', ' . $address_raw;
        }
        if ( ! empty( $city ) ) {
            $address .= ', ' . $city;
        }
        if ( ! empty( $state_region ) ) {
            $address .= ', ' . $state_region;
        }

        if ( ! empty( $country_id ) ) {
            $address .= ', ' . getCountryname( $country_id );
        }

        if ( ! empty( $zip_postal_code ) ) {
            $address .= ' - ' . $zip_postal_code;
        }

        return $address;
    }

}
