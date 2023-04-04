<?php

namespace App\Models;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Laravel\Cashier\Billable;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Laravel\Passport\Token;



class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, HasRoles, Sortable, Billable, LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'company_id',
        'status',
        'password',
        'contact_person',
        'contact_number',
        'unique_id',
        'web_access',
        'device_token',
        'updated_at'
    ];

    protected static $logAttributes = ['*'];

    protected $appends = ['full_name','status_label','profile_completed'];
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


    public function revokeOtherTokens()
    {
        if (!$this->currentAccessToken()) {
            return;
        }

        $tokens = $this->tokens()->where('id', '<>', $this->currentAccessToken()->id)->get();

        foreach ($tokens as $token) {
            Token::find($token->id)->revoke();
        }
    }

    public function logins()
    {
        return $this->hasMany(Login::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }

    public function user_detail(){
	    return $this->hasOne(UserDetails::class);
    }

    public function company_detail(){
	    return $this->hasOne(CompanyDetail::class);
    }

    public function userProgress(){
        return $this->hasMany(UserProgress::class);
    }
    public function programs(){
        return $this->belongsToMany(Program::class,'user_programs')->withPivot('start_date','end_date')->using(UserProgram::class)->withTimestamps();
    }

    public function activePrograms(){
        return $this->belongsToMany(Program::class,'user_programs')->withPivot('start_date')->wherePivotNull('end_date')->using(UserProgram::class)->withTimestamps();
    }

    public function companyUsers()
    {
        return $this->hasMany(CompanyUsers::class);
    }

    public function getCompanyName()
    {
        $company_id = $this->companyUsers()->first()->company_id;
        $company_detail = CompanyDetail::where('user_id', $company_id)->first();
        return $company_detail->company_name;
    }

    // public function notifications()
    // {
    //     return $this->morphMany(Notification::class, 'notifiable')
    //     ->whereNull('action_type')
    //     ->orWhere(function ($query) {
    //         $query->where(function( $query ){
    //             $query->where('data','like','%answered_forum%')
    //             ->orWhere('data','like','%replied_answer%');
    //         })
    //         ->whereHasMorph('action',[ForumAnswer::class],function( $query ){
    //             $query->active()
    //             ->whereHas("forumQuestion",function($query){
    //                 $query->active();
    //             })
    //             ->whereHas("user",function($query){
    //                 $query->active();
    //             })
    //             ->where(function($query){
    //                 $query->whereNull('reply_id')
    //                 ->orWhereHas("forumAnswer",function($query){
    //                     $query->active();
    //                 });
    //             });
    //         });
    //     })
    //     ->orWhere(function ($query) {
    //         $query->where('data','like','%liked_answer%')
    //         ->whereHasMorph('action',[ForumAnswerLike::class],function( $query ){
    //             $query->whereHas("forumAnswer",function( $query ){
    //                 $query->active()
    //                 ->whereHas("forumQuestion",function($query){
    //                     $query->active();
    //                 })
    //                 ->whereHas("user",function($query){
    //                     $query->active();
    //                 });
    //             })->active();
    //         });
    //     })
    //     ->orderBy('created_at', 'desc');
    // }

    /* Scopes */
    public function scopeActive($query)
    {
        $query->where('status', 1);
    }

    /* Accessors and Mutators */
    protected function fullName(): Attribute{
        return Attribute::make(
            get: function( $value , $attributes ){
                //return $this->first_name . $this->last_name;
                return $attributes['first_name'] .  ( !empty($attributes['last_name']) ? (' ' . $attributes['last_name']) : '') ;
            }
        );
    }
    protected function profileCompleted(): Attribute{
        return Attribute::make(
            get: function( $value , $attributes ){
                if( $this->user_detail && $this->user_detail->step_number == 2  )
                    return true;
                return false;
            }
        );
    }
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

    public function stripeAddress()
    {
        if( $this->user_detail ){
            return [
                'city' => $this->user_detail->city ? $this->user_detail->city->city_name : '',
                'country' => $this->user_detail->country ? $this->user_detail->country->name : '',
                'line1' => $this->user_detail->address,
                'line2' => $this->user_detail->city ? $this->user_detail->city->city_name : '',
                'postal_code' => '',
                'state' => $this->user_detail->state ? $this->user_detail->state->state_code : '',
            ];
        }
    }

    public function vehicles()
{
    return $this->belongsToMany(Vehicle::class, 'user_vehicle')->withPivot('ride_status');
}


    /* Helper functions */
    public function saveQuestionnaire( $type , $value ){
        $question = QuestionnaireType::where('name',$type)->first();
        if( $question )
            $this->questionnaires()->updateOrCreate(['questionnaire_type_id'=>$question->id],['answer_id'=>$value]);
        else
            throw new \Exception("Question do not exist");
    }
    public function getQuestionnaire( $type ){
        $question = QuestionnaireType::where('name',$type)->first();
        if( $question ){
            $questionnarieAnswer = $this->questionnaires()->where('questionnaire_type_id',$question->id)->first();
            if( $questionnarieAnswer )
                return $questionnarieAnswer->answer_id;
            return "";
        }else
            throw new \Exception("Question do not exist");
    }
    public function getActiveProgram(){
        return $this->activePrograms->first();
    }

    public function companies()
    {
        return $this->belongsToMany(Company::class, 'company_users', 'user_id', 'company_id');
    }

    public function company_id(){
        $company_id = CompanyUsers::where('user_id', $this->id)->pluck('company_id')->first();
        return $company_id;
    }

    public function devices()
    {
        return $this->hasMany(Device::class);
    }

    public function device_data()
    {
        return($this->hasMany(Device::class)->select(['id','user_id', 'device_token', 'device_name', 'device_activation_code','status','is_activate','tracking_radius' ]));
    }

}
