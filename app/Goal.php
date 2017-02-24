<?php
/**
 * Created by PhpStorm.
 * User: plume
 * Date: 2017/2/14
 * Time: 9:41
 */
namespace App;

use Illuminate\Database\Eloquent\Model;

class Goal extends Model {
    protected $table = "goals";
    protected $primaryKey = "id";

    public function user() {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }

    public function footprints() {
        return $this->hasMany('App\Footprint', 'goal_id', 'id');
    }
}