<?php
namespace App\Classes;

use DB;

Class langClass{

    public static function trans($title)
    {
        if (!empty($title)) {
            $language = session()->get('locale');
            $data = DB::table('translations')->where('language', !empty($language) ? $language : 'hu')->where('huname', $title)->first();
            if (!empty($data)) {
                return $data->name;
            } else {
                $data = DB::table('translations')->where('language', 'hu')->where('name', $title)->first();
                if (empty($data)) {
                    $languages = self::liveLanguages();
                    foreach ($languages as $language) {
                        $id = DB::table('translations')
                            ->insert([
                                'huname' => $title,
                                'name' => $title,
                                'language' => $language->shortname
                            ]);
                    }
                    return $title;
                }
                return $data->name;
            }
        }
        return $title;
    }

    public static function liveLanguages()
    {
        $translations = DB::table('translations')
            ->select('language', DB::raw('Sum(1) as db'))
            ->groupBy('language');

        $languages = DB::table('languages')
            ->joinSub($translations, 'translations', function ($join) {
                $join->on('languages.shortname', '=', 'translations.language');
            })->get();

        return $languages;
    }

    public static function notAliveLanguages()
    {
        return DB::table('languages')->whereNotExists(function($query) {
            $query->select(DB::raw(1))
                ->from('translations')
                ->whereRaw('Languages.shortname = Translations.language');
        })->get();
    }

}
