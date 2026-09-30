<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Renomme le site en « DIABA HOTEL » et applique la nouvelle charte (vert
 * #009C4A, noir #101818, vert clair #1DBF63) aux bases déjà en production :
 * le nom et les couleurs vivent dans la table `settings`, et les pages,
 * articles, FAQ et modèles de notification contiennent l'ancien nom en dur.
 */
return new class extends Migration
{
    /** Colonnes texte contenant l'ancien nom, par table. */
    private const TEXT_COLUMNS = [
        'pages' => ['title', 'content', 'meta_title', 'meta_description'],
        'posts' => ['title', 'excerpt', 'content'],
        'faq_entries' => ['question', 'answer'],
        'notification_templates' => ['subject', 'body'],
    ];

    private const SETTINGS = [
        'site_name' => 'DIABA HOTEL',
        'color_primary' => '#009C4A',
        'color_secondary' => '#C62828',
        'color_accent' => '#1DBF63',
    ];

    public function up(): void
    {
        foreach (self::SETTINGS as $key => $value) {
            $exists = DB::table('settings')->where('key', $key)->exists();

            if ($exists) {
                DB::table('settings')->where('key', $key)->update(['value' => $value, 'updated_at' => now()]);
            } else {
                DB::table('settings')->insert([
                    'key' => $key,
                    'value' => $value,
                    'group' => 'general',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            Cache::forget("setting:{$key}");
        }

        foreach (self::TEXT_COLUMNS as $table => $columns) {
            if (! Schema::hasTable($table)) {
                continue;
            }

            foreach ($columns as $column) {
                if (! Schema::hasColumn($table, $column)) {
                    continue;
                }

                DB::table($table)->whereNotNull($column)->orderBy('id')->each(function ($row) use ($table, $column) {
                    $renamed = $this->rename($row->{$column});

                    if ($renamed !== $row->{$column}) {
                        DB::table($table)->where('id', $row->id)->update([$column => $renamed]);
                    }
                });
            }
        }
    }

    public function down(): void
    {
        // Migration de contenu : l'ancien nom et l'ancienne palette ne sont
        // pas restaurés (modifiables depuis Admin > Paramètres).
    }

    private function rename(string $text): string
    {
        // « Centrale d'achat » / « Central d'Achat » employés comme nom propre
        // (initiale majuscule) ; « centrale d'achat » en minuscules, qui décrit
        // l'activité, est conservé.
        $text = preg_replace("/\b[Aa]u Centre d['’][Aa]chat de Mbour/u", 'Chez DIABA HOTEL', $text);
        $text = preg_replace("/\b[Dd]u Centre d['’][Aa]chat de Mbour/u", 'de DIABA HOTEL', $text);
        $text = preg_replace("/\b(?:[Ll]e )?Centre d['’][Aa]chat de Mbour/u", 'DIABA HOTEL', $text);

        return preg_replace("/Centrale?\s+d['’][Aa]chat/u", 'DIABA HOTEL', $text);
    }
};
