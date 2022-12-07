<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateApimodelRequest;
use App\Http\Requests\UpdateApimodelRequest;
use App\Repositories\ApimodelRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Response;
use Auth;
use DB;
use DataTables;
use myUser;

use App\Models\Apimodel;

class ApimodelController extends AppBaseController
{
    /** @var ApimodelRepository $apimodelRepository*/
    private $apimodelRepository;

    public function __construct(ApimodelRepository $apimodelRepo)
    {
        $this->apimodelRepository = $apimodelRepo;
    }

    public function dwData($data)
    {
        return Datatables::of($data)
            ->addIndexColumn()
//            ->addColumn('action', function($row){
//                $btn = '';
//                if ($row->apimodelerror->count() > 0) {
//                    $btn = '<a href="' . route('shoppingCarts.edit', [$row->Id]) . '"
//                                 class="edit btn btn-success btn-sm editProduct" title="Módosítás"><i class="fa fa-paint-brush"></i></a>';
//                }
//                return $btn;
//            })
            ->make(true);
    }


    /**
     * Display a listing of the Apimodel.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function index(Request $request)
    {

        if( myUser::check() ){

            if ($request->ajax()) {

                $data = Apimodel::where('api_id', $request->id)->get();
                return $this->dwData($data);

            }

            return view('apis.index');
        }
    }

    /**
     * Show the form for creating a new Apimodel.
     *
     * @return Response
     */
    public function create()
    {
        return view('apimodels.create');
    }

    /**
     * Store a newly created Apimodel in storage.
     *
     * @param CreateApimodelRequest $request
     *
     * @return Response
     */
    public function store(CreateApimodelRequest $request)
    {
        $input = $request->all();

        $apimodel = $this->apimodelRepository->create($input);

        Flash::success('Apimodel saved successfully.');

        return redirect(route('apimodels.index'));
    }

    /**
     * Display the specified Apimodel.
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $apimodel = $this->apimodelRepository->find($id);

        if (empty($apimodel)) {
            Flash::error('Apimodel not found');

            return redirect(route('apimodels.index'));
        }

        return view('apimodels.show')->with('apimodel', $apimodel);
    }

    /**
     * Show the form for editing the specified Apimodel.
     *
     * @param int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $apimodel = $this->apimodelRepository->find($id);

        if (empty($apimodel)) {
            Flash::error('Apimodel not found');

            return redirect(route('apimodels.index'));
        }

        return view('apimodels.edit')->with('apimodel', $apimodel);
    }

    /**
     * Update the specified Apimodel in storage.
     *
     * @param int $id
     * @param UpdateApimodelRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateApimodelRequest $request)
    {
        $apimodel = $this->apimodelRepository->find($id);

        if (empty($apimodel)) {
            Flash::error('Apimodel not found');

            return redirect(route('apimodels.index'));
        }

        $apimodel = $this->apimodelRepository->update($request->all(), $id);

        Flash::success('Apimodel updated successfully.');

        return redirect(route('apimodels.index'));
    }

    /**
     * Remove the specified Apimodel from storage.
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        $apimodel = $this->apimodelRepository->find($id);

        if (empty($apimodel)) {
            Flash::error('Apimodel not found');

            return redirect(route('apimodels.index'));
        }

        $this->apimodelRepository->delete($id);

        Flash::success('Apimodel deleted successfully.');

        return redirect(route('apimodels.index'));
    }
}
