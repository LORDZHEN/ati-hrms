<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Saln extends Model
{
    protected $fillable = [
        'user_id',
        'as_of_date',
        'joint_filing',
        'separate_filing',
        'not_applicable',
        'declarant_family_name',
        'declarant_first_name',
        'declarant_middle_initial',
        'declarant_position',
        'declarant_agency_office',
        'declarant_office_address',
        'spouse_family_name',
        'spouse_first_name',
        'spouse_middle_initial',
        'spouse_position',
        'spouse_agency_office',
        'spouse_office_address',
        'total_assets',
        'total_liabilities',
        'net_worth',
        'has_business_interests',
        'no_business_interests',
        'has_relatives_in_government',
        'no_relatives_in_government',
        'date_signed',
        'declarant_signature',
        'spouse_signature',
        'declarant_id_presented',
        'spouse_id_presented',
        'subscribed_sworn_date',
        'person_administering_oath',
        'remarks',
    ];

    protected $casts = [
        'as_of_date' => 'date',
        'date_signed' => 'date',
        'subscribed_sworn_date' => 'date',
        'joint_filing' => 'boolean',
        'separate_filing' => 'boolean',
        'not_applicable' => 'boolean',
        'has_business_interests' => 'boolean',
        'no_business_interests' => 'boolean',
        'has_relatives_in_government' => 'boolean',
        'no_relatives_in_government' => 'boolean',
        'total_assets' => 'decimal:2',
        'total_liabilities' => 'decimal:2',
        'net_worth' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function children(): HasMany
    {
        return $this->hasMany(SalnChild::class);
    }

    public function realProperties(): HasMany
    {
        return $this->hasMany(SalnRealProperty::class);
    }

    public function personalProperties(): HasMany
    {
        return $this->hasMany(SalnPersonalProperty::class);
    }

    public function liabilities(): HasMany
    {
        return $this->hasMany(SalnLiability::class);
    }

    public function businessInterests(): HasMany
    {
        return $this->hasMany(SalnBusinessInterest::class);
    }

    public function relativesInGovernment(): HasMany
    {
        return $this->hasMany(SalnRelativeGovernment::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($saln) {
            $saln->calculateTotals();
        });
    }

    public function calculateTotals()
    {
        $this->total_assets = $this->realProperties()->sum('current_fair_market_value') +
                              $this->personalProperties()->sum('acquisition_cost');
        $this->total_liabilities = $this->liabilities()->sum('outstanding_balance');
        $this->net_worth = $this->total_assets - $this->total_liabilities;
    }
}

class SalnChild extends Model
{
    protected $fillable = [
        'saln_id',
        'name',
        'date_of_birth',
        'age',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
    ];

    public function saln(): BelongsTo
    {
        return $this->belongsTo(Saln::class);
    }
}

class SalnRealProperty extends Model
{
    protected $fillable = [
        'saln_id',
        'description',
        'kind',
        'exact_location',
        'assessed_value',
        'current_fair_market_value',
        'acquisition_year',
        'mode_of_acquisition',
        'acquisition_cost',
    ];

    protected $casts = [
        'assessed_value' => 'decimal:2',
        'current_fair_market_value' => 'decimal:2',
        'acquisition_cost' => 'decimal:2',
    ];

    public function saln(): BelongsTo
    {
        return $this->belongsTo(Saln::class);
    }
}

class SalnPersonalProperty extends Model
{
    protected $fillable = [
        'saln_id',
        'description',
        'year_acquired',
        'acquisition_cost',
    ];

    protected $casts = [
        'acquisition_cost' => 'decimal:2',
    ];

    public function saln(): BelongsTo
    {
        return $this->belongsTo(Saln::class);
    }
}

class SalnLiability extends Model
{
    protected $fillable = [
        'saln_id',
        'nature',
        'name_of_creditors',
        'outstanding_balance',
    ];

    protected $casts = [
        'outstanding_balance' => 'decimal:2',
    ];

    public function saln(): BelongsTo
    {
        return $this->belongsTo(Saln::class);
    }
}

class SalnBusinessInterest extends Model
{
    protected $fillable = [
        'saln_id',
        'name_of_entity',
        'business_address',
        'nature_of_business_interest',
        'date_of_acquisition',
    ];

    protected $casts = [
        'date_of_acquisition' => 'date',
    ];

    public function saln(): BelongsTo
    {
        return $this->belongsTo(Saln::class);
    }
}

class SalnRelativeGovernment extends Model
{
    protected $table = 'saln_relatives_government';
    protected $fillable = [
        'saln_id',
        'name_of_relative',
        'relationship',
        'position',
        'name_of_agency_office_address',
    ];

    public function saln(): BelongsTo
    {
        return $this->belongsTo(Saln::class);
    }
}
