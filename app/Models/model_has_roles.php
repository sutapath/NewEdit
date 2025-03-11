<?php
class model_has_roles extends Model
{
    use HasFactory;
    protected $table = 'model_has_roles';

    protected $fillable = [
        'role_id', 
        'model_type', 
        'model_id'
    ];

    // เชื่อมโยงกับ User model
    public function user()
    {
        return $this->belongsTo(User::class, 'model_id');
    }
}
