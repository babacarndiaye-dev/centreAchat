<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Le nom complet de la marque est « DIABA HOTEL Produits du Sénégal (D.H.P.S) ».
 * Met à jour le réglage `site_name` et remplace « DIABA HOTEL » (seul) dans les
 * pages, articles, FAQ et modèles de notification déjà en base. Sans effet sur les
 * textes qui portent déjà le nom complet, donc rejouable sans danger.
 */
return new class extends Migration
{
    private const FULL_NAME = 'DIABA HOTEL Produits du Sénégal (D.H.P.S)';

    private const TEXT_COLUMNS = [
        'pages' => ['title', 'content', 'meta_title', 'meta_description'],
        'posts' => ['title', 'excerpt', 'content'],
        'faq_entries' => ['question', 'answer'],
        'notification_templates' => ['subject', 'body'],
    ];

    public function up(): void
    {
        if (DB::table('settings')->where('key', 'site_name')->exists()) {
            DB::table('settings')->where('key', 'site_name')->update(['value' => self::FULL_NAME, 'updated_at' => now()]);
        } else {
            DB::table('settings')->insert(['key' => 'site_name', 'value' => self::FULL_NAME, 'group' => 'general', 'created_at' => now(), 'updated_at' => now()]);
        }
        Cache::forget('setting:site_name');

        foreach (self::TEXT_COLUMNS as $table => $columns) {
            if (! Schema::hasTable($table)) {
                continue;
            }

            foreach ($columns as $column) {
                if (! Schema::hasColumn($table, $column)) {
                    continue;
                }

                DB::table($table)->whereNotNull($column)->where($column, 'like', '%DIABA HOTEL%')->orderBy('id')->each(function ($row) use ($table, $column) {
                    $renamed = preg_replace('/DIABA HOTEL(?! Produits du S)/u', self::FULL_NAME, $row->{$column});

                    if ($renamed !== $row->{$column}) {
                        DB::table($table)->where('id', $row->id)->update([$column => $renamed]);
                    }
                });
            }
        }
    }

    public function down(): void
    {
        // Migration de contenu : le nom court n'est pas restauré.
    }
};
