<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateTranslationsRequest;
use App\Http\Requests\UpdateTranslationsRequest;
use App\Models\Languages;
use App\Repositories\TranslationsRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Response;
use Auth;
use DB;
use DataTables;
use myUser;

use App\Models\Translations;

class TranslationsController extends AppBaseController
{
    /** @var  TranslationsRepository */
    private $translationsRepository;

    public function __construct(TranslationsRepository $translationsRepo)
    {
        $this->translationsRepository = $translationsRepo;
    }

    public function dwData($data)
    {
        return Datatables::of($data)
              ->addIndexColumn()
              ->make(true);
    }


    /**
     * Display a listing of the Translations.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function index(Request $request)
    {
        if( myUser::check() ){

            if ($request->ajax()) {

                $data = Translations::all();
                return $this->dwData($data);

            }

            return view('translations.index');
        }
    }


    /**
     * Display a listing of the one langauge Translations.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function indexLanguage(Request $request, $language)
    {
        if( myUser::check() ){

            $languages = Languages::where('shortname', $language)->first();
            if ($request->ajax()) {
                $data = DB::table('translations')
                    ->where('language', $language)
                    ->get();
                return $this->dwData($data);
            }

            return view('languages.edit')->with('languages', $languages);
        }
    }

    /**
     * Show the form for creating a new Translations.
     *
     * @return Response
     */
    public function create()
    {
        return view('translations.create');
    }

    /**
     * Store a newly created Translations in storage.
     *
     * @param CreateTranslationsRequest $request
     *
     * @return Response
     */
    public function store(CreateTranslationsRequest $request)
    {
        $input = $request->all();

        $translations = $this->translationsRepository->create($input);

        Flash::success('Translations saved successfully.');

        return redirect(route('translations.index'));
    }

    /**
     * Display the specified Translations.
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $translations = $this->translationsRepository->find($id);

        if (empty($translations)) {
            Flash::error('Translations not found');

            return redirect(route('translations.index'));
        }

        return view('translations.show')->with('translations', $translations);
    }

    /**
     * Show the form for editing the specified Translations.
     *
     * @param int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $translations = $this->translationsRepository->find($id);

        if (empty($translations)) {
            Flash::error('Translations not found');

            return redirect(route('translations.index'));
        }

        return view('translations.edit')->with('translations', $translations);
    }

    /**
     * Update the specified Translations in storage.
     *
     * @param int $id
     * @param UpdateTranslationsRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateTranslationsRequest $request)
    {
        $translations = $this->translationsRepository->find($id);

        if (empty($translations)) {
            Flash::error('Translations not found');

            return redirect(route('translations.index'));
        }

        $translations = $this->translationsRepository->update($request->all(), $id);

        Flash::success('Translations updated successfully.');

        return redirect(route('translations.index'));
    }

    /**
     * Remove the specified Translations from storage.
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        $translations = $this->translationsRepository->find($id);

        if (empty($translations)) {
            Flash::error('Translations not found');

            return redirect(route('translations.index'));
        }

        $this->translationsRepository->delete($id);

        Flash::success('Translations deleted successfully.');

        return redirect(route('translations.index'));
    }
}
