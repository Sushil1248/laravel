<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Company extends Model
{
    use  HasFactory, SoftDeletes, Sortable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'company_name',
        'company_email',
        'contact_person',
        'contact_number',
        'password',
        'status',
        'updated_at'
    ];
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function company_detail(){
	    return $this->hasOne(CompanyDetail::class);
    }


    /*public function notifications()
    {
        return $this->morphMany(Notification::class, 'notifiable')
        ->whereNull('action_type')
        ->orWhere(function ($query) {
            $query->where(function( $query ){
                $query->where('data','like','%answered_forum%')
                ->orWhere('data','like','%replied_answer%');
            })
            ->whereHasMorph('action',[ForumAnswer::class],function( $query ){
                $query->active()
                ->whereHas("forumQuestion",function($query){
                    $query->active();
                })
                ->whereHas("user",function($query){
                    $query->active();
                })
                ->where(function($query){
                    $query->whereNull('reply_id')
                    ->orWhereHas("forumAnswer",function($query){
                        $query->active();
                    });
                });
            });
        })
        ->orWhere(function ($query) {
            $query->where('data','like','%liked_answer%')
            ->whereHasMorph('action',[ForumAnswerLike::class],function( $query ){
                $query->whereHas("forumAnswer",function( $query ){
                    $query->active()
                    ->whereHas("forumQuestion",function($query){
                        $query->active();
                    })
                    ->whereHas("user",function($query){
                        $query->active();
                    });
                })->active();
            });
        })
        ->orderBy('created_at', 'desc');
    }*/

    /* Scopes */
    public function scopeActive($query)
    {
        $query->where('status', 1);
    }

    /* Accessors and Mutators */
   /*  protected function fullName(): Attribute{
        return Attribute::make(
            get: function( $value , $attributes ){
                //return $this->first_name . $this->last_name;
                return $attributes['first_name'] .  ( !empty($attributes['last_name']) ? (' ' . $attributes['last_name']) : '') ;
            }
        );
    } */
   /*  protected function profileCompleted(): Attribute{
        return Attribute::make(
            get: function( $value , $attributes ){
                if( $this->user_detail && $this->user_detail->step_number == 2  )
                    return true;
                return false;
            }
        );
    } */
    protected function statusLabel(): Attribute{
        return Attribute::make(
            get: function( $value , $attributes ){
                if( $attributes['status'] )
                    return '<span class="open-text"><span class="dot"></span>Active</span>';
                return '<span class="open-text closed"><span class="dot"></span>Inactive</span>';
            }
        );
    }

    /** Stripe data **/
    public function stripeName()
    {
        return $this->full_name;
    }

    public function company_users(){
        return $this->hasMany(User::class);
    }

    public function stripeAddress()
    {
        if( $this->company_detail ){
            return [
                'city' => $this->company_detail->city ? $this->company_detail->city->city_name : '',
                'country' => $this->company_detail->country ? $this->company_detail->country->name : '',
                'line1' => $this->company_detail->address,
                'line2' => $this->company_detail->city ? $this->company_detail->city->city_name : '',
                'postal_code' => '',
                'state' => $this->company_detail->state ? $this->company_detail->state->state_code : '',
            ];
        }
    }



}

