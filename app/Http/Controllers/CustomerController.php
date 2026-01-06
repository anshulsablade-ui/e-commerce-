<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {

            $customers = Customer::select('id', 'name', 'email', 'mobile', 'address', 'image', 'country_id', 'city_id')->get();

            return DataTables::of($customers)
                ->addIndexColumn()
                ->addColumn('customer', function ($row) {
                    return [
                        'id' => $row->id,
                        'name' => $row->name,
                        'image' => asset('customer/' . $row->image),
                    ];
                })
                ->addColumn('country', function ($row) {
                    return $row->country->name ?? 'N/A';
                })
                ->addColumn('city', function ($row) {
                    return $row->city->name ?? 'N/A';
                })
                ->addColumn('action', function ($row) {
                    return '<div class="d-flex justify-content-center">
                    <a href="' . route('customer.show', $row->id) . '" class="menu-link"><i class="menu-icon tf-icons bx bx-show"></i></a>
                    <a href="' . route('customer.edit', $row->id) . '" class="menu-link"><i class="menu-icon tf-icons bx bx-edit"></i></a>
                    <a href="javascript:void(0)" data-id="' . $row->id . '" class="menu-link delete"><i class="menu-icon text-danger tf-icons bx bx-trash"></i></a>
                </div>';
                })
                ->rawColumns(['image', 'action'])
                ->make(true);
        }
        return view('customer.index');
    }

    public function create()
    {
        $countries = Country::all();
        return view('customer.create', compact('countries'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:customers,email',
            'mobile' => 'required|numeric|unique:customers,mobile',
            'address' => 'required|string',
            'country' => 'required|string',
            'city' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()], 422);
        }

        $customer = Customer::create([
            'name' => $request->name,
            'email' => $request->email,
            'mobile' => $request->mobile,
            'address' => $request->address,
            'country_id' => $request->country,
            'city_id' => $request->city,
        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('customer'), $filename);
            $customer->update(['image' => $filename]);
        }

        session()->flash('success', 'Customer created successfully');
        return response()->json(['status' => 'success', 'message' => 'Customer created successfully']);
    }

    public function show($id)
    {
        $customer = Customer::find($id);
        return view('customer.show', compact('customer'));
    }

    public function edit($id)
    {
        $customer = Customer::find($id);
        $countries = Country::all();
        $cities = Country::find($customer->country_id)->cities;
        return view('customer.update', compact('customer', 'countries', 'cities'));
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email',
            'mobile' => 'required|numeric',
            'address' => 'required|string',
            'country' => 'required|string',
            'city' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()], 422);
        }

        Customer::where('id', $request->id)->update([
            'name' => $request->name,
            'email' => $request->email,
            'mobile' => $request->mobile,
            'address' => $request->address,
            'country_id' => $request->country,
            'city_id' => $request->city,
        ]);

        $customer = Customer::find($request->id);

        if ($request->hasFile('image')) {

            if (file_exists(public_path('customer/' . $customer->image))) {
                unlink(public_path('customer/' . $customer->image));
            }

            $file = $request->file('image');
            $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('customer'), $filename);
            $customer->update(['image' => $filename]);
        }

        session()->flash('success', 'Customer updated successfully');
        return response()->json(['status' => 'success', 'message' => 'Customer created successfully']);
    }

    public function delete($id)
    {
        $customer = Customer::find($id);
        if (!$customer) {
            return response()->json(['errors' => 'Customer not found.', 'status' => 'errors']);
        }
        if (file_exists(public_path('customer/' . $customer->image))) {
            unlink(public_path('customer/' . $customer->image));
        }
        $customer->delete();

        session()->flash('success', 'Customer deleted successfully');
        return response()->json(['status' => 'success', 'message' => 'Customer deleted successfully'], 200);
    }

    public function ajaxCustomer(Request $request)
    {
        $customers = Customer::where('name', 'like', "%{$request->search}%")
            ->select('id', 'name', 'email', 'image')
            ->limit(10)
            ->get()
            ->map(function ($customer) {
                return [
                    'id' => $customer->id,
                    'name' => $customer->name,
                    'email' => $customer->email,
                    'image' => $customer->image ? asset('customer/' . $customer->image) : asset('default/default.png'),
                ];
            });

        return response()->json($customers);

    }
}
