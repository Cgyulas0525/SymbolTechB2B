<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateApimodelerrorRequest;
use App\Http\Requests\UpdateApimodelerrorRequest;
use App\Repositories\ApimodelerrorRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Response;
use Auth;
use DB;
use DataTables;
use myUser;

use App\Models\Apimodelerror;

class ApimodelerrorController extends AppBaseController
{
    /** @var ApimodelerrorRepository $apimodelerrorRepository*/
    private $apimodelerrorRepository;

    public function __construct(ApimodelerrorRepository $apimodelerrorRepo)
    {
        $this->apimodelerrorRepository = $apimodelerrorRepo;
    }

    public function dwData($data)
    {
        return Datatables::of($data)
            ->addIndexColumn()
            ->make(true);
    }

    /**
     * Display a listing of the Apimodelerror.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function indexApimodelerror(Request $request)
    {
        if( myUser::check() ){

            if ($request->ajax()) {

                $data = Apimodelerror::where('apimodel_id', $request->id)->get();
                return $this->dwData($data);

            }

            return view('apimodelerrors.index');
        }
    }

    /**
     * Display a listing of the Apimodelerror.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $apimodelerrors = $this->apimodelerrorRepository->all();

        return view('apimodelerrors.index')
            ->with('apimodelerrors', $apimodelerrors);
    }

    /**
     * Show the form for creating a new Apimodelerror.
     *
     * @return Response
     */
    public function create()
    {
        return view('apimodelerrors.create');
    }

    /**
     * Store a newly created Apimodelerror in storage.
     *
     * @param CreateApimodelerrorRequest $request
     *
     * @return Response
     */
    public function store(CreateApimodelerrorRequest $request)
    {
        $input = $request->all();

        $apimodelerror = $this->apimodelerrorRepository->create($input);

        Flash::success('Apimodelerror saved successfully.');

        return redirect(route('apimodelerrors.index'));
    }

    /**
     * Display the specified Apimodelerror.
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $apimodelerror = $this->apimodelerrorRepository->find($id);

        if (empty($apimodelerror)) {
            Flash::error('Apimodelerror not found');

            return redirect(route('apimodelerrors.index'));
        }

        return view('apimodelerrors.show')->with('apimodelerror', $apimodelerror);
    }

    /**
     * Show the form for editing the specified Apimodelerror.
     *
     * @param int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $apimodelerror = $this->apimodelerrorRepository->find($id);

        if (empty($apimodelerror)) {
            Flash::error('Apimodelerror not found');

            return redirect(route('apimodelerrors.index'));
        }

        return view('apimodelerrors.edit')->with('apimodelerror', $apimodelerror);
    }

    /**
     * Update the specified Apimodelerror in storage.
     *
     * @param int $id
     * @param UpdateApimodelerrorRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateApimodelerrorRequest $request)
    {
        $apimodelerror = $this->apimodelerrorRepository->find($id);

        if (empty($apimodelerror)) {
            Flash::error('Apimodelerror not found');

            return redirect(route('apimodelerrors.index'));
        }

        $apimodelerror = $this->apimodelerrorRepository->update($request->all(), $id);

        Flash::success('Apimodelerror updated successfully.');

        return redirect(route('apimodelerrors.index'));
    }

    /**
     * Remove the specified Apimodelerror from storage.
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        $apimodelerror = $this->apimodelerrorRepository->find($id);

        if (empty($apimodelerror)) {
            Flash::error('Apimodelerror not found');

            return redirect(route('apimodelerrors.index'));
        }

        $this->apimodelerrorRepository->delete($id);

        Flash::success('Apimodelerror deleted successfully.');

        return redirect(route('apimodelerrors.index'));
    }
}
