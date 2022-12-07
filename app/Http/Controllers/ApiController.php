<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateApiRequest;
use App\Http\Requests\UpdateApiRequest;
use App\Repositories\ApiRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Response;
use Auth;
use DB;
use DataTables;
use myUser;

use App\Models\Api;

class ApiController extends AppBaseController
{
    /** @var ApiRepository $apiRepository*/
    private $apiRepository;

    public function __construct(ApiRepository $apiRepo)
    {
        $this->apiRepository = $apiRepo;
    }

    public function dwData($data)
    {
        return Datatables::of($data)
            ->addIndexColumn()
//            ->addColumn('action', function($row){
//                $btn = '<a href="#"
//                             class="edit btn btn-success btn-sm editProduct" title="Táblák"><i class="far fa-list-alt"></i></a>';
//                return $btn;
//            })

            ->make(true);
    }


    /**
     * Display a listing of the Api.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function index(Request $request)
    {

        if( myUser::check() ){

            if ($request->ajax()) {

                $data = Api::all();
                return $this->dwData($data);

            }

            return view('apis.index');
        }
    }

    /**
     * Show the form for creating a new Api.
     *
     * @return Response
     */
    public function create()
    {
        return view('apis.create');
    }

    /**
     * Store a newly created Api in storage.
     *
     * @param CreateApiRequest $request
     *
     * @return Response
     */
    public function store(CreateApiRequest $request)
    {
        $input = $request->all();

        $api = $this->apiRepository->create($input);

        Flash::success('Api saved successfully.');

        return redirect(route('apis.index'));
    }

    /**
     * Display the specified Api.
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $api = $this->apiRepository->find($id);

        if (empty($api)) {
            Flash::error('Api not found');

            return redirect(route('apis.index'));
        }

        return view('apis.show')->with('api', $api);
    }

    /**
     * Show the form for editing the specified Api.
     *
     * @param int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $api = $this->apiRepository->find($id);

        if (empty($api)) {
            Flash::error('Api not found');

            return redirect(route('apis.index'));
        }

        return view('apis.edit')->with('api', $api);
    }

    /**
     * Update the specified Api in storage.
     *
     * @param int $id
     * @param UpdateApiRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateApiRequest $request)
    {
        $api = $this->apiRepository->find($id);

        if (empty($api)) {
            Flash::error('Api not found');

            return redirect(route('apis.index'));
        }

        $api = $this->apiRepository->update($request->all(), $id);

        Flash::success('Api updated successfully.');

        return redirect(route('apis.index'));
    }

    /**
     * Remove the specified Api from storage.
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        $api = $this->apiRepository->find($id);

        if (empty($api)) {
            Flash::error('Api not found');

            return redirect(route('apis.index'));
        }

        $this->apiRepository->delete($id);

        Flash::success('Api deleted successfully.');

        return redirect(route('apis.index'));
    }
}
