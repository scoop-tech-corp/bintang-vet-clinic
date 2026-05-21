<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SanitizeOrderBy
{
    // Hanya karakter alfanumerik, underscore, dan titik (format table.column) yang diizinkan.
    // Karakter lain seperti backtick, tanda kurung, spasi, atau koma tidak dapat lolos validasi ini,
    // sehingga SQL injection via ORDER BY column tidak dapat dilakukan.
    private const COLUMN_PATTERN = '/^[a-zA-Z0-9_.]+$/';
    private const VALID_DIRECTIONS = ['asc', 'desc'];

    public function handle(Request $request, Closure $next)
    {
        if ($request->has('column')) {
            $column = $request->input('column');
            if (!preg_match(self::COLUMN_PATTERN, (string) $column)) {
                $request->merge(['column' => null]);
            }
        }

        if ($request->has('orderby')) {
            $orderby = strtolower((string) $request->input('orderby'));
            if (!in_array($orderby, self::VALID_DIRECTIONS, true)) {
                $request->merge(['orderby' => null]);
            }
        }

        return $next($request);
    }
}
