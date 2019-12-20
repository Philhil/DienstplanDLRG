class Training extends Model
{
    protected $fillable = [
        'date', 'hastoauthorize', 'content',
    ];

    protected $dates = [
        'date',
    ];

    protected $attributes = array(
        'hastoauthorize' => 1,
    );


    public function positions()
    {
        return $this->hasMany(Position::class);
    }

    public function openpositions()
    {
        return $this->hasMany(Position::class)->whereNull('user_id');
    }

    public function openpositions_required()
    {
        return $this->hasMany(Position::class)->whereNull('user_id')
            ->where('requiredposition', '=', 1);
    }

    public function positionwithQualification($qualifiaction_id)
    {
        return $this->positions()->where('qualification_id', $qualifiaction_id);
    }

    public function hasUserPositions($userid)
    {
        return $this->hasMany(Position::class)->where('user_id', '=', $userid)->count() > 0 ? true : false;
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
