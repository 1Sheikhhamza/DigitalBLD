<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OCRExtraction extends Model
{
    use SoftDeletes, \Laravel\Scout\Searchable;

    public function toSearchableArray()
    {
        return [
            'id' => $this->id,
            'judgment' => $this->judgment,
            'key_words' => $this->key_words,
            'subject' => $this->subject,
            'case_no' => $this->case_no,
            'parties' => $this->parties,
            'book_volume' => $this->book_volume,
            'published_year' => $this->published_year,
            'starting_page_no' => $this->starting_page_no,
            'decided_on' => $this->decided_on,
            'judges' => $this->judges,
            'division' => $this->division,
            'petitioners' => $this->petitioners,
            'respondent' => $this->respondent,
            'related_act_order_rule' => $this->related_act_order_rule,
        ];
    }

    protected $table = 'ocr_extractions';

    protected $guarded = ['id'];

    protected $fillable = [];

    // protected $dates = ['decided_on'];   

    public function volume()
    {
        return $this->belongsTo(Volume::class);
    }

    /* public function users()
    {
        return $this->belongsToMany(Subscriber::class, 'user_folder_decisions')
            ->withPivot('folder_id')
            ->withTimestamps()
            ->withTrashed();
    }

    public function folders()
    {
        return $this->belongsToMany(Folder::class, 'user_folder_decisions')
            ->withPivot('user_id')
            ->withTimestamps()
            ->withTrashed();
    } */
}
