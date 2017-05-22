<?php

namespace Xoco70\KendoTournaments\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;

class ChampionshipSettings extends Model
{
    use SoftDeletes;

    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    protected $table = 'championship_settings';
    public $timestamps = true;
    protected $guarded = ['id'];

    const MIN_COMPETITORS_BY_AREA = 2,
        PLAY_OFF = 0,
        DIRECT_ELIMINATION = 1,
        PRELIMINARY_GROUP_SIZE = [3 => 3, 4 => 4, 5 => 5],
        PRELIMINARY_WINNERS = [1 => 1], // , 2 => 2, 3 => 3
        TEAM_SIZE = [2 => 2, 3 => 3, 4 => 4, 5 => 5, 6 => 6, 7 => 7, 8 => 8, 9 => 9, 10 => 10],
        TEAM_RESERVE = [1 => 1, 2 => 2, 3 => 3, 4 => 4],
        LIMIT_BY_ENTITY = [0 => '-', 1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5, 6 => 6, 7 => 7, 8 => 8, 9 => 9, 10 => 10],
        DEFAULT_SETTINGS = [
        'fightingAreas' => '1',
        'fightDuration' => '05:00',
        'hasPreliminary' => '1',
        'preliminaryGroupSize' => '3',
        'preliminaryDuration' => '05:00',
        'preliminaryWinner' => '1',
        'hasEncho' => '1',
        'enchoQty' => '1',
        'enchoDuration' => '0',
        'hasHantei' => '0',
        'hanteiLimit' => '0', // 1/2 Finals
        'enchoGoldPoint' => '0', // Step where Encho has no more time limit
        'limitByEntity' => '4',
        'cost' => '',
        'treeType' => '1',
        'seedQuantity' => '4',

    ];

    /**
     * A Setting belongs to a Championship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function championship()
    {
        return $this->belongsTo(Championship::class);
    }

    /**
     * @param Request $request
     * @param Championship $championship
     *
     * @return ChampionshipSettings
     */
    public static function createOrUpdate(Request $request, Championship $championship): ChampionshipSettings
    {
        $request->request->add(['championship_id' => $championship->id]);
        $arrSettings = $request->except('_token', 'numFighters');
        $settings = static::where(['championship_id' => $championship->id])->first();
        if ($settings == null) {
            $settings = new self();
        }
        $settings->fill($arrSettings);

        $settings->save();

        return $settings;
    }
}
