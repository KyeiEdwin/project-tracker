<?php

namespace App\Models;

use App\Models\Concerns\SerializesForInertia;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QaTestStep extends Model
{
    /** @use HasFactory<\Database\Factories\QaTestStepFactory> */
    use HasFactory;
    use SerializesForInertia;

    protected $fillable = [
        'qa_test_id',
        'instruction',
        'expected_result',
        'actual_result',
        'status',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function toInertia(): array
    {
        return [
            'id' => $this->id,
            'qaTestId' => $this->qa_test_id,
            'instruction' => $this->instruction,
            'expectedResult' => $this->expected_result,
            'actualResult' => $this->actual_result,
            'status' => $this->status,
            'sortOrder' => (int) $this->sort_order,
        ];
    }

    public function qaTest(): BelongsTo
    {
        return $this->belongsTo(QaTest::class);
    }
}
