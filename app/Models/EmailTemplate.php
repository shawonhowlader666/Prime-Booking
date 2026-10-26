<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    protected $fillable = [
        'code',
        'name',
        'category',
        'subject',
        'body_html',
        'placeholders',
        'is_active',
    ];

    protected $casts = [
        'placeholders' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Render the template by replacing {placeholders} with given values.
     */
    public function render(array $data = []): array
    {
        $subject = $this->subject;
        $body = $this->body_html;

        foreach ($data as $key => $value) {
            $tag = '{' . $key . '}';
            $subject = str_replace($tag, (string) $value, $subject);
            $body = str_replace($tag, (string) $value, $body);
        }

        return [
            'subject' => $subject,
            'body' => $body,
        ];
    }

    /**
     * Get or create a default template by code.
     */
    public static function getTemplate(string $code): ?self
    {
        return static::where('code', $code)->first();
    }
}
