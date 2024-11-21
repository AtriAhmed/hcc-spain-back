<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaudiApplication extends Model
{
    use HasFactory;
    protected $table = "saudi_applications";
    protected $fillable = [
        "coName",
        "coAddress",
        "regNB",
        "activity",
        "empNB",
        "cPerson",
        "cEmail",
        "cPhone",
        "remark",
        "qualCertif",
        "prodReg",
        "facCertif"
    ];
}
