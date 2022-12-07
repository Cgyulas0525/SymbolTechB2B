<?php

namespace App\Http\Controllers;

use App\Classes\langClass;
use App\Http\Requests\CreateLogItemRequest;
use App\Http\Requests\UpdateLogItemRequest;
use App\Models\LogItemTable;
use App\Models\LogItemTableDetail;
use App\Repositories\LogItemRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Response;
use Auth;
use DB;
use DataTables;
use myUser;
use ddwClass;

use App\Models\LogItem;

class LogItemController extends AppBaseController
{
    /** @var  LogItemRepository */
    private $logItemRepository;

    public function __construct(LogItemRepository $logItemRepo)
    {
        $this->logItemRepository = $logItemRepo;
    }

    public function dwData($data)
    {
        return Datatables::of($data)
            ->addIndexColumn()
            ->addColumn('action', function($row){
                $btn = '';
                if (!empty($row->tablename)) {
                    $btn = '<a href="' . route('logItems.show', [$row->tableid]) . '"
                                 class="edit btn btn-success btn-sm editProduct" title="Tábla"><i class="fas fa-table"></i></a>';
                }
//                if (!empty($row->changedfield)) {
//                    $btn = $btn.'<a href="' . route('users.destroy', [$row->id]) . '"
//                                 class="btn btn-danger btn-sm deleteProduct" title="Rekord"><i class="fas fa-server"></i></a>';
//                }
                return $btn;
            })
            ->addColumn('eventName', function($row){
                return ddwClass::logEvent($row->eventtype);
            })
            ->addColumn('customerName', function($row){
                return !empty($row->customerName) ? $row->customerName : session('customer_name');
            })
            ->rawColumns(['action', 'eventName', 'customerName'])
            ->make(true);
    }

    public function indexData($startDate, $endDate, $customer = NULL, $user = NULL)
    {
        return DB::table('logitem as t1')
            ->leftJoin('customer as t2', 't2.Id', '=', 't1.customer_id')
            ->join('users as t3', 't3.id', '=', 't1.user_id')
            ->leftJoin('logitemtable as t4', 't4.logitem_id', '=', 't1.id')
            ->leftJoin('logitemtabledetail as t5', 't5.logitemtable_id', '=', 't4.id')
            ->select('t1.*', 't2.Name as customerName', 't3.name as userName', 't4.tablename', 't4.id as tableid', 't5.changedfield', 't5.id as recordid')
            ->whereBetween('t1.eventdatetime', [ $startDate, $endDate])
            ->where( function($query) use ($customer) {
                if ( $customer == 0 || is_null($customer) || $customer == ' ') {
                    $query->whereNotNull('t1.customer_id');
                } else {
                    $query->where('t1.customer_id', '=', $customer);
                }
            })
            ->where( function($query) use ($user) {
                if ( $user == 0 || is_null($user) || $user == ' ' ) {
                    $query->whereNotNull('t1.user_id');
                } else {
                    $query->where('t1.user_id', '=', $user);
                }
            })
            ->get();
    }

    /**
     * Display a listing of the LogItem.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function index(Request $request)
    {
        if( myUser::check() ){

            if ($request->ajax()) {

                $startDate = date('Y-m-d H:i:s', strtotime('now - 1 day 00:00:00'));
                $endDate   = date('Y-m-d H:i:s', strtotime('now + 1 day 24:00:00'));
                $customer  = 0;
                $user      = 0;

                return $this->dwData($this->indexData($startDate, $endDate, $customer, $user));

            }

            return view('log_items.index');
        }
    }

    /**
     * Display a listing of the LogItem.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function indexBetween(Request $request, $startDate, $endDate, $customer = NULL, $user = NULL)
    {
        if( myUser::check() ){

            if ($request->ajax()) {

                return $this->dwData($this->indexData(date('Y-m-d H:i:s', strtotime($startDate)), date('Y-m-d H:i:s', strtotime($endDate)), $customer, $user));

            }

            return view('log_items.index');
        }
    }


    public function indexLogItemTableDetail(Request $request, $id)
    {
        if( myUser::check() ){

            if ($request->ajax()) {

                $data = LogItemTableDetail::where('logitemtable_id', $id)->get();

                return Datatables::of($data)
                    ->addIndexColumn()
                    ->make(true);

            }

            return view('log_items.index');
        }
    }

    /**
     * Show the form for creating a new LogItem.
     *
     * @return Response
     */
    public function create()
    {
        return view('log_items.create');
    }

    /**
     * Store a newly created LogItem in storage.
     *
     * @param CreateLogItemRequest $request
     *
     * @return Response
     */
    public function store(CreateLogItemRequest $request)
    {
        $input = $request->all();

        $logItem = $this->logItemRepository->create($input);

        Flash::success(langClass::trans('A mentés sikeres'));

        return redirect(route('logItems.index'));
    }

    /**
     * Display the specified LogItem.
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $logItemTable = LogItemTable::find($id);

        if (empty($logItemTable)) {
            Flash::error(langClass::trans('Nincs a tételhez kapcsolódó tábla'));

            return redirect(route('logItems.index'));
        }

        return view('log_items.show')->with('logItemTable', $logItemTable);
    }

    /**
     * Show the form for editing the specified LogItem.
     *
     * @param int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $logItem = $this->logItemRepository->find($id);

        if (empty($logItem)) {
            Flash::error(langClass::trans('Log Item nem található'));

            return redirect(route('logItems.index'));
        }

        return view('log_items.edit')->with('logItem', $logItem);
    }

    /**
     * Update the specified LogItem in storage.
     *
     * @param int $id
     * @param UpdateLogItemRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateLogItemRequest $request)
    {
        $logItem = $this->logItemRepository->find($id);

        if (empty($logItem)) {
            Flash::error(langClass::trans('Log Item nem található'));

            return redirect(route('logItems.index'));
        }

        $logItem = $this->logItemRepository->update($request->all(), $id);

        Flash::success(langClass::trans('Log Item módosítása sikeres'));

        return redirect(route('logItems.index'));
    }

    /**
     * Remove the specified LogItem from storage.
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        $logItem = $this->logItemRepository->find($id);

        if (empty($logItem)) {
            Flash::error(langClass::trans('Log Item nem található'));

            return redirect(route('logItems.index'));
        }

        $this->logItemRepository->delete($id);

        Flash::success(langClass::trans('Log Item törlése sikeres'));

        return redirect(route('logItems.index'));
    }

    /**
     * Display a listing of the LogItem.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function logItemTableDetailIndex(Request $request, $id)
    {
        if( myUser::check() ){

            if ($request->ajax()) {

                $data = LogItemTableDetail::where('logitemtable_id', $id)->get();

                return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('oldValue', function($data) { return $data->oldValue(); })
                    ->addColumn('newValue', function($data) { return $data->newValue(); })
                    ->make(true);

            }

            return view('log_items.index');
        }
    }

}
