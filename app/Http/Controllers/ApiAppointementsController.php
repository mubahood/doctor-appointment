<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\DataTables\UsersDataTable;
use App\Models\Appointment;
use App\Models\User;
use App\Models\Utils;

class ApiAppointementsController extends Controller
{
    public function index(Request $r)
    {
        $user_id = ((int)($r->client_id));

        $u = User::find($user_id);
        if ($u == null) {
            Utils::show_response(0, 0, 'Failed. user not found on db..');
        }

        $items = [];

        if($u->user_ty == 'admin'){
            $items  = Appointment::all();
        }else if($u->user_ty == 'doctor'){
            $items  = Appointment::where('doctor_id',$user_id)->get();
        } else{            
            $items  = Appointment::where('client_id',$user_id)->get();
        }


        return $items;
    }

    public function store(Request $r)
    {

        if (
            (!isset($r->hospitial_id)) ||
            (!isset($r->doctor_id)) ||
            (!isset($r->client_id)) ||
            (!isset($r->price)) ||
            (!isset($r->latitude)) ||
            (!isset($r->longitude)) ||
            (!isset($r->details)) ||
            (!isset($r->category_id))
        ) {
            Utils::show_response(0, 0, 'Failed. impormation submited not enough.');
        }

        $user_id = ((int)($r->client_id));
        $u = User::find($user_id);
        if ($u == null) {
            Utils::show_response(0, 0, 'Failed. user not found on db..');
        }
        $ap = new Appointment();
        $ap->hospitial_id = $r->hospitial_id;
        $ap->doctor_id = $r->doctor_id;
        $ap->client_id = $r->client_id;
        $ap->price = $r->price;
        $ap->latitude = $r->latitude;
        $ap->longitude = $r->longitude;
        $ap->category_id = $r->category_id;
        $ap->status = 'Pending';
        $ap->appointment_time = '';
        $ap->details = $r->details;
        $ap->order_location = $u->location_id;
        if ($ap->save()) {
            Utils::show_response(1, 1, 'Appintment submited successfully.');
        } else {
            Utils::show_response(0, 0, 'Failed. to submit appintment.');
        }
    }

    public function edit()
    {
        return  view('metro.dashboard.users-create');
    }
}
