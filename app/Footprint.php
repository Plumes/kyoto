<?php
/**
 * Created by PhpStorm.
 * User: plume
 * Date: 2017/2/14
 * Time: 9:41
 */
namespace App;

use Illuminate\Database\Eloquent\Model;

class Footprint extends Model {
    protected $table = "footprints";
    protected $primaryKey = "id";

    public function goal() {
        return $this->belongsTo('App\Goal', 'goal_id', 'id');
    }
}