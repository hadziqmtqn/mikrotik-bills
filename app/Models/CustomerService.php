<?php

namespace App\Models;

use App\Observers\CustomerServiceObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
        'price',
        'package_type',
        'username',
        'password',
        'start_date',
        'end_date_time',
        'status',
        'notes'
    ];

    protected function casts(): array
    {
        return [
            'slug' => 'string',
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

    // TODO Scopes
    #[Scope]
    protected function filterByReferenceNumber(Builder $query, $referenceNumber): Builder
    {
        return $query->where('reference_number', $referenceNumber);
    }
}
