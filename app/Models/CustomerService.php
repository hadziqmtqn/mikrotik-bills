<?php

namespace App\Models;

use App\Observers\CustomerServiceObserver;
use App\Services\InvoiceSettingService;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

#[ObservedBy([CustomerServiceObserver::class])]
class CustomerService extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'slug',
        'reference_number',
        'user_id',
        'service_package_id',
        'daily_price',
        'price',
        'package_type',
        'username',
        'password',
        'installation_date',
        'start_date',
        'end_date_time',
        'status',
        'notes'
    ];

    protected function casts(): array
    {
        return [
            'slug' => 'string',
            'installation_date' => 'date',
            'start_date' => 'datetime',
            'end_date_time' => 'datetime',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function servicePackage(): BelongsTo
    {
        return $this->belongsTo(ServicePackage::class, 'service_package_id');
    }

    public function invCustomerServices(): HasMany
    {
        return $this->hasMany(InvCustomerService::class, 'customer_service_id');
    }

    public function customerServiceUsageLatest(): HasOne
    {
        return $this->hasOne(CustomerServiceUsage::class, 'customer_service_id')
            ->latest('period_end');
    }

    public function customerServiceUsages(): HasMany
    {
        return $this->hasMany(CustomerServiceUsage::class, 'customer_service_id');
    }

    // TODO Scopes
    #[Scope]
    protected function filterByReferenceNumber(Builder $query, $referenceNumber): Builder
    {
        return $query->where('reference_number', $referenceNumber);
    }

    // TODO Attributes
    protected function nextBillingDate(): Attribute
    {
        return Attribute::make(
            get: fn() => InvoiceSettingService::nextRepetitionDate()
        );
    }
}
