<?php

namespace Xoco70\KendoTournaments\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Collection;

class Competitor extends Model
{
    use SoftDeletes;
    protected $DATES = ['created_at', 'updated_at', 'deleted_at'];

    protected $table = 'competitor';
    public $timestamps = true;
    protected $fillable = [
        'tournament_category_id',
        'user_id',
        'confirmed',
    ];

    /**
     * Get the Competitor's Championship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function championship()
    {
        return $this->belongsTo(Championship::class);
    }

    /**
     * Get User from Competitor.
     *
     * @return mixed
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function fightersGroups()
    {
        return $this->belongsToMany(FightersGroup::class, 'fighters_group_competitor')->withTimestamps();
    }

    /**
     * @return null|string
     */
    public function getName()
    {
        return $this->defaultName() ?? $this->user->name;
    }

    /**
     * @return null|string
     */
    public function getFullName()
    {
        return $this->defaultName() ?? $this->user->firstname . " " . $this->user->lastname;
    }

    /**
     * @return null|string
     */
    private function defaultName()
    {
        if ($this == null) return "BYE";
        if ($this->user == null) return "BYE";
        return null;
    }

}
