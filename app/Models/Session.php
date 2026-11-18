<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Session extends Model
{
    /**
     * اسم الجدول الفعلي في قاعدة البيانات.
     */
    protected $table = 'case_sessions';

    /**
     * الحقول المسموح بتعيينها عند إنشاء الجلسة أو تعديلها.
     */
    protected $fillable = [
        'case_id',
        'scheduled_at',
        'details',
        'status',
    ];

    /**
     * تحويل أنواع البيانات عند قراءة السجلات.
     */
    protected function casts(): array
    {
        return [
            'scheduled_at' => 'date',
        ];
    }

    /**
     * القضية التي تنتمي إليها هذه الجلسة.
     */
    public function legalCase(): BelongsTo
    {
        return $this->belongsTo(LegalCase::class, 'case_id');
    }

    /**
     * سجلات الإشعارات المرتبطة بهذه الجلسة.
     */
    public function notificationLogs(): HasMany
    {
        return $this->hasMany(NotificationLog::class, 'session_id');
    }

    
}
