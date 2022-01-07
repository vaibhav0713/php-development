<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Admin;
use App\Models\Todo;
use App\Models\Register;
use App\Models\Exhibitor;
use App\Models\Payment;
use App\Models\Booking;
use App\Models\Personel;
use App\Models\Inventory;
use App\Models\Feedback;
use App\Models\Signature;
use App\Models\Sponsor;
use App\Models\Hotel;
use App\Models\Furniture;
use App\Models\Contact;
use App\Models\Spacebooking;
use App\Models\Subscriber;
use App\Models\Visitor;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendMail;
// use Illuminate\Support\Facades\DB;
use Illuminate\Notifications\Notification;
use App\Notifications\BookingNotification;
use PDF;
class AuthController extends Controller
{
    function login(){
        return view('auth.login');
    }

    function userLogin(){
        return view('auth.userLogin');
    }

    function add_user(){
        $data = ['LoggedUserInfo' => Admin::where('id', "=", session('LoggedUser')) -> first()];
        // return view('admin.dashboard');
        return view('admin.add-user', $data);
    }


    function requirement(){
        $data = ['LoggedUserInfo' => Admin::where('id', "=", session('LoggedUser')) -> first()];
        // return view('admin.dashboard');
        $list = Furniture::all();
        return view('admin.requirement', ['furnitures' => $list], $data);
    }

    function delete($id){
        $data = Furniture::find($id);
        $data -> delete();

        return redirect('admin/requirement') -> with('success', "Inventory deleted successfully!");
    }

    function showData($id){
        $data = ['LoggedUserInfo' => Admin::where('id', "=", session('LoggedUser')) -> first()];
        $row = Furniture::find($id);
        return view('admin.edit', ['info' => $row], $data);
    }

    function update(Request $request){
        $data = Furniture::find($request -> id);
        $request -> validate([
            'name' => 'required',
            'rate' => 'required',
        ]);

        $data -> name = $request -> name;
        $data -> rate = $request -> rate;
        $data -> save();

        return redirect('admin/requirement') -> with('success', "Inventory updated successfully!");
    }

    function addFurniture(Request $request){
        $request -> validate([
            'name' => 'required',
            'rate' => 'required',
        ]);

        // Insert data to the admin table
        $furniture = new Furniture;
        $furniture -> name = $request -> name;
        $furniture -> rate = $request -> rate;
        $data = $furniture -> save();

        if($data){
            return back() -> with('success', "Furniture added successfully!");
        }
        else{
            return back() -> with('fail', 'Something went wrong, please try again letter!');
        }
    }

    // function furnitureList(){
    //     $data = ['LoggedUserInfo' => Admin::where('id', "=", session('LoggedUser')) -> first()];
    //     // return view('admin.feedback', $data);

    //  //   $list = Feedback::all();
    //     return view('admin.feedback',['feedbacks' => $list], $data);
    // }

    function register(){
        return view('auth.register');
    }

    function save(Request $request){
        //validation
        $request -> validate([
            'name' => 'required',
            'email' => 'required | email | unique:admins',
            'password' => 'required | min:5 | max:15'
        ]);

        // Insert data to the admin table
        $admin = new Admin;
        $admin -> name = $request -> name;
        $admin -> email = $request -> email;
        $admin -> password = Hash::make($request -> password);
        $save = $admin -> save();

        if($save){
            return back() -> with('success', "You are registered successfully!");
        }
        else{
            return back() -> with('fail', 'Something went wrong, please try again letter!');
        }
    }

    function check(Request $request){
        // validate
        $request -> validate([
            'email' => 'required | email',
            'password' => 'required | min:5 | max:15',
        ]);

        // get User Information
        $userInfo = Admin::where('email', "=", $request -> email) -> first();

        if(!$userInfo){
            return back() -> with('fail', 'We do not recognize your email address!');
        }
        else{
            // check password
            if(Hash::check($request -> password, $userInfo -> password)){
                $request -> session() -> put('LoggedUser', $userInfo -> id);
                return redirect('admin/dashboard');
            }
            else{
                return back() -> with('fail', 'Incorrect username or password!');
            }
        }
    }

    function add_new_user(Request $request){
        $exhibitor = new Exhibitor;
        $exhibitor -> formSubmissionDate = $request -> formSubmissionDate;
        $exhibitor -> companyName = $request -> companyName;
        $exhibitor -> address = $request -> address;
        $exhibitor -> country = $request -> country;
        $exhibitor -> state = $request -> state;
        $exhibitor -> city = $request -> city;
        $exhibitor -> postcode = $request -> postcode;
        $exhibitor -> phone = $request -> phone;
        $exhibitor -> mobile = $request -> mobile;
        $password = $exhibitor -> personMobile = $request -> personMobile;
        $exhibitor -> password = Hash::make($password);
        $email = $exhibitor -> email = $request -> email;
        $exhibitor -> emailOptional = $request -> emailOptional;
        $exhibitor -> website = $request -> website;
        $name = $exhibitor -> contactPerson = $request -> contactPerson;
        $exhibitor -> personMobile = $request -> personMobile;
        $exhibitor -> hallNumber = $request -> hallNumber;
        $exhibitor -> stallNumber = $request -> stallNumber;
        $exhibitor -> dimensionOne = $request -> dimensionOne;
        $exhibitor -> dimensionTwo = $request -> dimensionTwo;
        $exhibitor -> stallSize = $request -> stallSize;
        $exhibitor -> stallRate = $request -> stallRate;
        $exhibitor -> stallAmount = $request -> stallAmount;
        $exhibitor -> stallGSTAmount = $request -> stallGSTAmount;
        $exhibitor -> stallFinalAmount = $request -> stallFinalAmount;
        $exhibitor -> representivePerson = $request -> representivePerson;
        $exhibitor -> dateOne = $request -> dateOne;
        $exhibitor -> dateTwo = $request -> dateTwo;
        $exhibitor -> dateThree = $request -> dateThree;
        $exhibitor -> status = false;

        $exhibitorData = $exhibitor -> save();

        $exhbId = $exhibitor -> id;

        // Payment Table Submission
        foreach ($request->addMoreInputFields as $key => $value) {

            $payment = new Payment;
            $payment->exhbId = $exhbId;
            $payment->term = $value['term'];
            $payment->date = $value['date'];
            $payment->percentage = $value['percentage'];
            $payment->paymentAmount = $value['paymentAmount'];
            $payment->gstAmount = $value['gstAmount'];
            $payment->paidAmount = $value['paidAmount'];
            $payment->save();
        }

        $details = [
            'title' => "HBLF Show Visitor Registration Details",
            'username' => "$email",
            'password' => "$password",
            'name' => "$name"
        ];

        Mail::to($email) -> send(new SendMail($details));

        if($exhibitorData){
            return back() -> with('success', "You have successfully registered a new Exhibitor!");
        }
        else{
            return back() -> with('fail', 'Something went wrong, please try again letter!');
        }

    }

    function deleteExhibitor($id){
        $data = Exhibitor::find($id);
        $data -> delete();

        return redirect('admin/exhibitorList') -> with('success', "An Exhibitor deleted successfully!");
    }

    function showExhibitorData($id){
        $data = ['LoggedUserInfo' => Admin::where('id', "=", session('LoggedUser')) -> first()];
        $row = Exhibitor::find($id);
        $list = Payment::all();
        return view('admin.editExhibitor', ['payments' => $list,'info' => $row], $data);
    }



    function updateExhibitor(Request $request){
        $data = Exhibitor::find($request -> id);
        // $request -> validate([
        //     'name' => 'required',
        //     'rate' => 'required',
        // ]);

        $data -> formSubmissionDate = $request -> formSubmissionDate;
        $data -> companyName = $request -> companyName;
        $data -> address = $request -> address;
        $data -> country = $request -> country;
        $data -> state = $request -> state;
        $data -> city = $request -> city;
        $data -> postcode = $request -> postcode;
        $data -> phone = $request -> phone;
        $data -> email = $request -> email;
        $data -> emailOptional = $request -> emailOptional;
        $data -> website = $request -> website;
        $data -> contactPerson = $request -> contactPerson;
        $data -> personMobile = $request -> personMobile;
        $data -> hallNumber = $request -> hallNumber;
        $data -> stallNumber = $request -> stallNumber;
        $data -> dimensionOne = $request -> dimensionOne;
        $data -> dimensionTwo = $request -> dimensionTwo;
        $data -> stallSize = $request -> stallSize;
        $data -> stallRate = $request -> stallRate;
        $data -> stallAmount = $request -> stallAmount;
        $data -> stallGSTAmount = $request -> stallGSTAmount;
        $data -> stallFinalAmount = $request -> stallFinalAmount;
        $data -> representivePerson = $request -> representivePerson;
        $data -> dateOne = $request -> dateOne;
        $data -> dateTwo = $request -> dateTwo;
        $data -> dateThree = $request -> dateThree;
        $exhibitor = $data -> save();


        // $exhbId = $data -> id;
        // $payment = Payment::find($request -> id);
        // $exhbId = $exhibitor -> id;
        // Payment Table Submission
        // foreach ((array)$request->id as $key => $value) {

        //     $payment = Payment::find($request -> id[$key]);
        //     // $payment->exhbId = $exhbId;
        //     $payment->term =  $request->term[$key];
        //     $payment->date =  $request->date[$key];
        //     $payment->percentage =  $request->percentage[$key];
        //     $payment->paymentAmount =  $request->paymentAmount[$key];
        //     $payment->gstAmount =  $request->gstAmount[$key];
        //     $payment->paidAmount =  $request->paidAmount[$key];
        //     // $payment->term = $value['term'];
        //     // $payment->date = $value['date'];
        //     // $payment->percentage = $value['percentage'];
        //     // $payment->paymentAmount = $value['paymentAmount'];
        //     // $payment->gstAmount = $value['gstAmount'];
        //     // $payment->paidAmount = $value['paidAmount'];
        //     $payment->save();
        // }

        return redirect('admin/exhibitorList') -> with('success', "Inventory updated successfully!");
    }

    function todoInput(Request $request){
        // return $request -> input();
        $request -> validate([
            'todoInput' => 'required'
        ]);

        $todo = new Todo;
        $todo -> todoInput = $request -> todoInput;
        $todo -> status = false;
        $todo -> delete = false;
        $todo -> save();

        return back();
    }
    function todoDelete(Request $request, $id){
        $data = Todo::find($id);

        if($data -> delete == 0){
            $data -> delete = 1;
        }
        else{
            $data -> delete = 0;
        }

        $data -> save();

        return back();
    }

    function save_user(Request $request){
        // return $request -> input();

        //validation
        $request -> validate([
            'name' => 'required',
            'companyName' => 'required',
            'email' => 'required | email | unique:admins',
            'contactnumber' => 'required | min:8 | max:13'
        ]);

        // insert data into database
        $register = new Register;
        $register -> name = $request -> name;
        $register -> companyName = $request -> companyName;
        $email = $register -> email = $request -> email;
        $register -> contactnumber = $request -> contactnumber;

        // $password = mt_rand(10000000,99999999);

        $password = "hblfshow2021";
        $register -> Password = Hash::make($password);

        $registerData = $register -> save();

        // Send mail of the credentials
        // $details = [
        //     'title' => "HBLF Show Visitor Registration Details",
        //     'username' => "$email",
        //     'password' => "$personMobile"
        // ];

        // Mail::to($email) -> send(new SendMail($details));

        if($registerData){
            return back() -> with('success', "You are registered successfully!");
        }
        else{
            return back() -> with('fail', 'Something went wrong, please try again letter!');
        }

    }

    // new user registration check
    function userCheck(Request $request){
        // validate
        $request -> validate([
            'email' => 'required | email',
            'password' => 'required | min:5 | max:15',
        ]);

        // get User Information
        $userInfo = Exhibitor::where('email', "=", $request -> email) -> first();

        if(!$userInfo){
            return back() -> with('fail', 'We do not recognize your email address!');
        }
        else{

            // $pass = Exhibitor::where('password', "=", $userInfo -> password) -> first();
            // if(Hash::check($request -> password , $pass))

            // check password
            if(Hash::check($request -> password, $userInfo -> password)){
                $request -> session() -> put('LoggedExhibitor', $userInfo -> id);
                return redirect('user/dashboard');
            }
            else{
                return back() -> with('fail', 'Incorrect username or password!');
            }
        }
    }

    // Admin dashboard
    function dashboard(){
        $data = ['LoggedUserInfo' => Admin::where('id', "=", session('LoggedUser')) -> first()];

        $list = Feedback::all();
        $booking = Booking::all();
        $signature = Signature::all();

        $todoData = Todo::all();
        // return view('admin.dashboard',['todos' => $data]);
        return view('admin.dashboard',['feedbacks' => $list, 'bookings' => $booking, 'todos' => $todoData, 'signatures' => $signature], $data);
    }

    function feedbackStatus(Request $request, $id){
        $data = Feedback::find($id);

        if($data -> status == 0){
            $data -> status = 1;
        }
        else{
            $data -> status = 0;
        }

        $data -> save();

        return back();
    }

    function bookingStatus(Request $request, $id){
        $data = Booking::find($id);

        if($data -> status == 0){
            $data -> status = 1;
        }
        else{
            $data -> status = 0;
        }

        $data -> save();

        return back();
    }


    // Exhibitor Logout Conrtol
    function userLogout(){
        if(session() -> has('LoggedExhibitor')){
            session() -> pull('LoggedExhibitor');
            return redirect('/');
        }
    }

    // Admin Logout Control
    function logout(){
        if(session() -> has('LoggedUser')){
            session() -> pull('LoggedUser');
            return redirect('/auth/login');
        }
    }


    // user dashboard area
    function userDashboard(){
        $data = ['LoggedExhibitorInfo' => Exhibitor::where('id', "=", session('LoggedExhibitor')) -> first()];
        return view('user.dashboard', $data);
    }

    // Exhibitor Details Control
    function exhibitorDetails(){
        $data = ['LoggedExhibitorInfo' => Exhibitor::where('id', "=", session('LoggedExhibitor')) -> first()];
        // return view('user.dashboard', $data);

        $list = Payment::all();
        $sign = Signature::all();

        return view('user.exhibitorDetails', ['payments' => $list, 'signatureData' => $sign], $data);
    }

    // Exhibitor List Control
    function exhibitorList(){
        $data = ['LoggedUserInfo' => Admin::where('id', "=", session('LoggedUser')) -> first()];
        // return view('admin.exhibitorList', $data);

        $list = Exhibitor::all();
        return view('admin.exhibitorList',['exhibitors' => $list], $data);
    }

    // Booking List control
    function bookingList(){
        $data = ['LoggedUserInfo' => Admin::where('id', "=", session('LoggedUser')) -> first()];

        $list = Booking::all();
        return view('admin.bookingList',['bookings' => $list], $data);
    }

    // Booking List control
    function contactUsData(){
        $data = ['LoggedUserInfo' => Admin::where('id', "=", session('LoggedUser')) -> first()];

        $list = Contact::all();
        return view('admin.contactdata',['contactus' => $list], $data);
    }

    function sponsorRegistrationData(){
        $data = ['LoggedUserInfo' => Admin::where('id', "=", session('LoggedUser')) -> first()];

        $list = Sponsor::all();
        return view('admin.sponsorshipdata',['sponsors' => $list], $data);
    }


    function visitorRegistrationData(){
        $data = ['LoggedUserInfo' => Admin::where('id', "=", session('LoggedUser')) -> first()];

        $list = Visitor::all();


        return view('admin.visitorregistrationdata',['visitorbooking' => $list], $data);
    }

    function exhibitorRegistrationData(){
        $data = ['LoggedUserInfo' => Admin::where('id', "=", session('LoggedUser')) -> first()];
        $spaceBooking = Spacebooking::all();


        return view('admin.exhibitorregistrationdata',['spacebooking' => $spaceBooking], $data);
    }

    // Rules Regulation Control
    function rulesRegulation(Request $request){
        $data = ['LoggedExhibitorInfo' => Exhibitor::where('id', "=", session('LoggedExhibitor')) -> first()];
        $sign = Signature::all();

        return view('user.rules-regulation', ['signatureData' => $sign], $data);
    }

    // Exhibitor space booking registration control
    function ExhibitorRegistration(){
        $data = ['LoggedExhibitorInfo' => Exhibitor::where('id', "=", session('LoggedExhibitor')) -> first()];
        $sign = Signature::all();
        $list = Furniture::all();
        return view('user.registration',['furnitures' => $list, 'signatureData' => $sign], $data);

    }

    function registerData(Request $request){
        // return $request -> input();

        $request -> validate([
            'organizationName' => 'required',
            'address' => 'required',
            'country' => 'required',
            'state' => 'required',
            'city' => 'required',
            'postalCode' => 'required',
            'phone' => 'required | min:8 | max:13',
            'mobile' => 'required | min:10 | max:13',
            'email' => 'required | email',
            'website' => 'required',
            'hallNumber' => 'required',
            'stallNumber' => 'required',
            'organizationHead' => 'required',
            'exactDesignation' => 'required',
            'mobileNumber' => 'required | min:10 | max:13',
            'hblfContactPerson' => 'required',
            'hblPersonDesignation' => 'required',
            'hblfMobileNumber' => 'required | min:10 | max:13',
            'companyProfile' => 'required | min:25 | max:150',
            'addMorePerson.*.personName' => 'required',
            'addMorePerson.*.personDesignation' => 'required',
        ]);

        // insert booking data into database
        $booking = new Booking;
        $booking -> organizationName = $request -> organizationName;
        $booking -> address = $request -> address;
        $booking -> country = $request -> country;
        $booking -> state = $request -> state;
        $booking -> city = $request -> city;
        $booking -> postalCode = $request -> postalCode;
        $booking -> phone = $request -> phone;
        $booking -> mobile = $request -> mobile;
        $booking -> email = $request -> email;
        $booking -> website = $request -> website;
        $booking -> hallNumber = $request -> hallNumber;
        $booking -> stallNumber = $request -> stallNumber;
        $booking -> organizationHead = $request -> organizationHead;
        $booking -> exactDesignation = $request -> exactDesignation;
        $booking -> mobileNumber = $request -> mobileNumber;
        $booking -> hblfContactPerson = $request -> hblfContactPerson;
        $booking -> hblPersonDesignation = $request -> hblPersonDesignation;
        $booking -> hblfMobileNumber = $request -> hblfMobileNumber;
        $booking -> companyProfile = $request -> companyProfile;
        $booking -> status = false;

        $bookingData = $booking -> save();

        // $request->validate([
        //     'addMorePerson.*.personName' => 'required',
        //     'addMorePerson.*.PersonDesignation' => 'required'
        // ]);

        $bookId = $booking -> id;

        foreach($request->addMorePerson as $key => $peronData) {

            $person = new Personel;
            $person->bookId = $bookId;
            $person->personName = $peronData['personName'];
            $person->PersonDesignation = $peronData['personDesignation'];
            $person->save();
        }

        foreach($request->addMoreFurniture as $key => $furnitureData) {

            $inventory = new Inventory;
            $inventory->bookId = $furnitureData['bookId'];
            $inventory->name = $furnitureData['name'];
            $inventory->rate = $furnitureData['rate'];
            $inventory->qty = $furnitureData['qty'];
            $inventory->amount = $furnitureData['amount'];
            $inventory->save();
        }

        if($bookingData){
            return back() -> with('success', "You are registered successfully!");
        }
        else{
            return back() -> with('fail', 'Something went wrong, please try again letter!');
        }
    }


    // Support area control
    function faq(){
        $data = ['LoggedExhibitorInfo' => Exhibitor::where('id', "=", session('LoggedExhibitor')) -> first()];
        return view('user.faq', $data);
    }

    function feedback(){
        $data = ['LoggedExhibitorInfo' => Exhibitor::where('id', "=", session('LoggedExhibitor')) -> first()];
        return view('user.feedback', $data);
    }



    function feedbackForm(Request $request){
        // return $request -> input();

        $request -> validate([
            'name' => 'required',
            'mobile' => 'required | min:10 | max: 14',
            'email' => 'required | email',
            'message' => 'required'
        ]);

        $feedback = new Feedback;
        $feedback -> name = $request -> name;
        $feedback -> mobile = $request -> mobile;
        $feedback -> email = $request -> email;
        $feedback -> message = $request -> message;
        $feedback -> status = false;

        $feedbackData = $feedback -> save();

        if($feedbackData){
            return back() -> with('success', "Your Form has been successfully submitted!");
        }
        else{
            return back() -> with('fail', 'Something went wrong, please try again letter!');
        }
    }

// Download control functions
    function floordownload(){
        $data = ['LoggedExhibitorInfo' => Exhibitor::where('id', "=", session('LoggedExhibitor')) -> first()];
        return view('user.floordownload', $data);
    }

    function newspaper(){
        $data = ['LoggedExhibitorInfo' => Exhibitor::where('id', "=", session('LoggedExhibitor')) -> first()];
        return view('user.newspaper', $data);
    }


    // feedback list function
    function feedbackList(){
        $data = ['LoggedUserInfo' => Admin::where('id', "=", session('LoggedUser')) -> first()];
        // return view('admin.feedback', $data);

        $list = Feedback::all();
        return view('admin.feedback',['feedbacks' => $list], $data);
    }

    function faqData(){
        $data = ['LoggedUserInfo' => Admin::where('id', "=", session('LoggedUser')) -> first()];
        return view('admin.faq', $data);
    }


// Hotel registering, listing and approval control
    function hotelRegister(){
        $data = ['LoggedUserInfo' => Admin::where('id', "=", session('LoggedUser')) -> first()];
        return view('admin.hotel.register', $data);
    }

    function saveHotel(Request $request){
        // return $request -> input();

        $request -> validate([
            'name' => 'required',
            'address' => 'required',
            'city' => 'required',
            'manager' => 'required',
            'email' => 'required | email',
            'mobile' => 'required | min:10 | max:13',
            'authorizedPerson' => 'required',
            'personEmail' => 'required | email',
            'personMobile' => 'required | min:10 | max:13',
            'roomOption' => 'required',
            'availableRoom' => 'required',
            'roomPrice' => 'required',
            'specialRoomPrice' => 'required',
            'facilities' => 'required',
            'restaurant' => 'required'
        ]);

        $hotel = new Hotel;
        $hotel -> name = $request -> name;
        $hotel -> address = $request -> address;
        $hotel -> city = $request -> city;
        $hotel -> manager = $request -> manager;
        $hotel -> email = $request -> email;
        $hotel -> mobile = $request -> mobile;
        $hotel -> authorizedPerson = $request -> authorizedPerson;
        $hotel -> personEmail = $request -> personEmail;
        $hotel -> personMobile = $request -> personMobile;
        $hotel -> website = $request -> website;
        $hotel -> roomOption = $request -> roomOption;
        $hotel -> availableRoom = $request -> availableRoom;
        $hotel -> roomPrice = $request -> roomPrice;
        $hotel -> specialRoomPrice = $request -> specialRoomPrice;
        $hotel -> facilities = $request -> facilities;
        $hotel -> restaurant = $request -> restaurant;
        $hotel -> status = false;

        $hotelData = $hotel -> save();

        if($hotelData){
            return back() -> with('success', "You have been registered hotel successfully!");
        }
        else{
            return back() -> with('fail', 'Something went wrong, please try again letter!');
        }
    }

    function hotelApproval(){
        $data = ['LoggedUserInfo' => Admin::where('id', "=", session('LoggedUser')) -> first()];
        // return view('admin.feedback', $data);

        $list = Hotel::all();
        return view('admin.hotel.approval',['hotels' => $list], $data);
    }

    function status(Request $request, $id){
        $data = Hotel::find($id);

        if($data -> status == 0){
            $data -> status = 1;
        }
        else{
            $data -> status = 0;
        }

        $data -> save();

        return back() -> with('success', $data -> name . " has been successfully update the status!");
    }

    function hotelList(){
        $data = ['LoggedUserInfo' => Admin::where('id', "=", session('LoggedUser')) -> first()];

        $list = Hotel::all() -> where('status', "=", 1);
        return view('admin.hotel.list',['hotels' => $list], $data);
    }

    public function bookingDetails($id){
        $data = ['LoggedUserInfo' => Admin::where('id', "=", session('LoggedUser')) -> first()];

        $list = Booking::all() -> where('id', $id) -> first();

        $person = Personel::all();
        $inventory = Inventory::all();
        return view('admin.bookingDetails', ['bookings' => $list, 'personels' => $person, 'inventories' => $inventory], $data);

    }

    public function visitorBookingDetails($id){
        $data = ['LoggedUserInfo' => Admin::where('id', "=", session('LoggedUser')) -> first()];

        $list = Visitor::all() -> where('id', $id) -> first();

        return view('admin.visitorbookdetails', ['visitors' => $list], $data);

    }

    public function receiptDownload($id){
        $data = ['LoggedUserInfo' => Admin::where('id', "=", session('LoggedUser')) -> first()];
        // $pdf =public_path('https://hblfshow.com/system/storage/app/receipt-pdf/');
        $list = Visitor::all() -> where('id', $id) -> first();

        return response()->download('https://hblfshow.com/system/storage/app/receipt-pdf/' . 'receipt'.  $data["visitorID"] .'.pdf');

        return view('admin.visitorbookdetails', ['visitors' => $list], $data);
    }

    public function sponsorRegistrationDetails($id){
        $data = ['LoggedUserInfo' => Admin::where('id', "=", session('LoggedUser')) -> first()];

        $list = Sponsor::all() -> where('id', $id) -> first();

        return view('admin.sponsordetails', ['sponsors' => $list], $data);

    }

    public function exhibitorBookingDetails($id){
        $data = ['LoggedUserInfo' => Admin::where('id', "=", session('LoggedUser')) -> first()];

        $list = Spacebooking::all() -> where('id', $id) -> first();

        return view('admin.exhibitorbookdetails', ['exhbitors' => $list], $data);

    }

    public function pdfGenerate(){

        $data = ['LoggedExhibitorInfo' => Exhibitor::where('id', "=", session('LoggedExhibitor')) -> first()];
        $sign = Signature::all();

        // $details['dateOne'] = "test";


        // return view('test', $data);
        $pdf = PDF::loadView('test',$data);
        return $pdf->download('rules-regulation.pdf');

    }

    public function pdfGenerateTwo(){

        $data = ['LoggedExhibitorInfo' => Exhibitor::where('id', "=", session('LoggedExhibitor')) -> first()];
        $payments = Payment::all();

        // $details['dateOne'] = "test";


        // return view('test', $data);
        $pdf = PDF::loadView('testTwo',$data, ['payments' => $payments]);
        return $pdf->download('exhibitor-details.pdf');

    }


}
