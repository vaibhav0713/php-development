<?php

<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;


use Illuminate\Http\Request;
use App\Models\Inquiry;
use App\Models\Term;
use App\Models\Category;
use App\Models\Country;
use App\Models\Sourcefund;
use App\Models\Student;
use App\Models\Document;
use App\Models\Payment;

use Illuminate\Support\Facades\Storage;
class AccurateController extends Controller
{
    //

    public function add_student(){
        // $data = ['LoggedUserInfo' => Admin::where('id', "=", session('LoggedUser')) -> first()];
        // return view('admin.add-user', $data);
        return view('add-student');
    }

    public function save_student(Request $request){
            // return $request -> input();

        // validation
        // $request -> validate([
        //     'formDate' => 'required | date',
        //     'firstName' => 'required',
        //     'lastName' => 'required',
        //     'dateOfBirth' => 'required | date',
        //     'passportRequired' => 'required',
        //     'address' => 'required',
        //     'studentEmail' => 'required | email',
        //     'studentMobile' => 'required',
        //     'sscSubject' => 'required',
        //     'sscPassYear' => 'required',
        //     'sscAggregate' => 'required',
        //     'hscSubject' => 'required',
        //     'hscPassYear' => 'required',
        //     'hscAggregate' => 'required',
        //     'graduSubject' => 'required',
        //     'graduPassYear' => 'required',
        //     'graduAggregate' => 'required',
        //     'mediumOfInstruction' => 'required',
        //     'courseOne' => 'required',
        //     'universityOne' => 'required',
        //     'studyLevel' => 'required',
        //     'otherStudyLevel' => 'required',
        //     'termsMonth' => 'required | min:2',
        //     'visaCategory' => 'required | min:1',
        //     'interestedCountry' => 'required | min:3',
        //     'visaRefusal' => 'required',
        //     'fundSource' => 'required | min:1',
        //     'ieltsToeflScore' => 'required',
        //     'satGreGmtScore' => 'required',
        // ]);



        $student = new Student;
        $student -> formDate = $request -> formDate;

            $fileName = $request -> firstName;
            $extension = $request -> file('studentPic') -> getClientOriginalExtension();
            $fileNameToStore = str_replace(' ', '', $fileName).'.'.$extension;



        $student -> studentPic = $fileNameToStore;
        $student -> firstName = $request -> firstName;
        $student -> lastName = $request -> lastName;
        $student -> dateOfBirth = $request -> dateOfBirth;
        $student -> passportRequired = $request -> passportRequired;
        $student -> address = $request -> address;
        $student -> studentEmail = $request -> studentEmail;
        $student -> studentMobile = $request -> studentMobile;
        $student -> sscSubject = $request -> sscSubject;
        $student -> sscPassYear = $request -> sscPassYear;
        $student -> sscAggregate = $request -> sscAggregate;
        $student -> hscSubject = $request -> hscSubject;
        $student -> hscPassYear = $request -> hscPassYear;
        $student -> hscAggregate = $request -> hscAggregate;
        $student -> graduSubject = $request -> graduSubject;
        $student -> graduPassYear = $request -> graduPassYear;
        $student -> graduAggregate = $request -> graduAggregate;
        $student -> otherPassYear = $request -> otherPassYear;
        $student -> otherSubject = $request -> otherSubject;
        $student -> otherAggregate = $request -> otherAggregate;
        $student -> mediumOfInstruction = $request -> mediumOfInstruction;
        $student -> courseOne = $request -> courseOne;
        $student -> courseTwo = $request -> courseTwo;
        $student -> universityOne = $request -> universityOne;
        $student -> universityTwo = $request -> universityTwo;
        $student -> studyLevel = $request -> studyLevel;
        $student -> otherStudyLevel = $request -> otherStudyLevel;
        $student -> visaRefusal = $request -> visaRefusal;
        $student -> refusalVisaCountry = $request -> refusalVisaCountry;
        $student -> ieltsToeflScore = $request -> ieltsToeflScore;
        $student -> satGreGmtScore = $request -> satGreGmtScore;
        $student -> workExperience = $request -> workExperience;
        $student -> referenceSource = $request -> referenceSource;
        $student->sttaus = FALSE;
        $studentData = $student -> save();

        $request -> file('studentPic') -> move('student/profile/'.$student -> id, $fileNameToStore);

        $studentId = $student -> id;


        if (is_array($request->termsMonth) || is_object($request->termsMonth))
        {
            // TERMS MONTH CHEKBOX VALUE
            foreach ($request->termsMonth as $key => $value) {
                $termsMonth = new Term;
                // $payment->studentId = $studentId;
                $termsMonth->studentId = $studentId;
                $termsMonth->terms = $request->termsMonth[$key];
                $termsMonth->status = FALSE;
                $termsMonth->save();
            }
        }

        if (is_array($request->visaCategory) || is_object($request->visaCategory))
        {
            // VISA CATEGORY CHEKBOX VALUE
            foreach ($request->visaCategory as $key => $value) {
                $visaCategory = new Category;
                $visaCategory->studentId = $studentId;
                $visaCategory->categories = $request->visaCategory[$key];
                $visaCategory->status = FALSE;
                $visaCategory->save();
            }
        }

        if (is_array($request->interestedCountry) || is_object($request->interestedCountry))
        {
            // INTERESTED COUNTRY CHEKBOX VALUE
            foreach ($request->interestedCountry as $key => $value) {
                $interestedCountry = new Country;
                $interestedCountry->studentId = $studentId;
                $interestedCountry->countries = $request->interestedCountry[$key];
                $interestedCountry->status = FALSE;
                $interestedCountry->save();
            }
        }

        if (is_array($request->fundSource) || is_object($request->fundSource))
        {
            // FUNDING SOURCE CHEKBOX VALUE
            foreach ($request->fundSource as $key => $value) {
                $fundSource = new Sourcefund;
                $fundSource->studentId = $studentId;
                $fundSource->sourcefunds = $request->fundSource[$key];
                $fundSource->status = FALSE;
                $fundSource->save();
            }
        }

        if($studentData){
            return back() -> with('success', "You are registered successfully!");
        }
        else{
            return back() -> with('fail', 'Something went wrong, please try again letter!');
        }
    }

    public function submit_documents($id){
        $list = Student::find($id);
        return view('upload-document',['documents' => $list]);
    }

    public function inquiry_list(){
        $list = Inquiry::all();
        return view('student-list',['inquiries' => $list]);
    }

    public function student_list(){
        $studentList = Student::all();
        return view('student-list',['students' => $studentList]);
    }

    public function payment_list(){
        $paymentList = Student::all();
        return view('payment-list',['payments' => $paymentList]);
    }

    public function payment_receipt($id){
        $list = Student::find($id);
        return view('payment-receipt',['paymentData' => $list]);
    }

    public function submit_payment(Request $request){
        $payment = new Payment;
        $payment -> invoiceNumber = $request -> invoiceNumber;
        $payment -> studentName = $request -> studentName;
        $payment -> studentId = $request -> studentId;
        $payment -> studentMobile = $request -> studentMobile;
        $payment -> studentEmail = $request -> studentEmail;
        $payment -> paymentMethod = $request -> paymentMethod;
        $payment -> paymentDate = $request -> paymentDate;
        $payment -> paymentType = $request -> paymentType;
        $payment -> chequeDdNumber = $request -> chequeDdNumber;
        $payment -> paymentAmount = $request -> paymentAmount;
        $payment -> paymentFor = $request -> paymentFor;
        $payment -> status = FALSE;
        $paymentData = $payment -> save();

        if($paymentData){
            return back() -> with('success', "You are registered successfully!");
        }
        else{
            return back() -> with('fail', 'Something went wrong, please try again letter!');
        }
    }

    public function student_details($id){
        $list = Student::find($id);
        $docList = Document::all();
        $termList = Term::all();
        $categoryList = Category::all();
        $sourceFundList = Sourcefund::all();
        $countryList = Country::all();
        return view('student-details',['studentDetails' => $list,
                                        'documents' => $docList,
                                        'termsData' => $termList,
                                        'categories' => $categoryList,
                                        'sourcefunds' => $sourceFundList,
                                        'countries' => $countryList
                                    ]);
    }










    // return $request -> file('image')-> store('docs');
    public function ssc_document(Request $request){
        if($request -> hasFile('sscMarksheet')){
            $fileName = 'sscMarksheet';
            $extension = $request -> file('sscMarksheet') -> getClientOriginalExtension();
            $fileNameToStore = $fileName.'_'.date('h-i-s').'.'.$extension;
            $path = $request -> file('sscMarksheet') -> move('public/document/sscMarksheet/'.$request -> studentId, $fileNameToStore);
        }

        $document = new Document;
        $document -> studentId = $request -> studentId;
        $document -> fileName = $fileNameToStore;
        $document -> filePath = $path;
        $document -> documentFor = $request -> documentFor;
        $document -> status = FALSE;
        $stdDocument = $document -> save();

        if($stdDocument){
            return back() -> with('success', "Document Uploaded Successfully!");
        }
    }

    public function hsc_document(Request $request){
        if($request -> hasFile('hscMarksheet')){
            $fileName = 'hscMarksheet';
            $extension = $request -> file('hscMarksheet') -> getClientOriginalExtension();
            $fileNameToStore = $fileName.'_'.date('h-i-s').'.'.$extension;
            $path = $request -> file('hscMarksheet') -> move('public/document/hscMarksheet/'.$request -> studentId, $fileNameToStore);
        }

        $document = new Document;
        $document -> studentId = $request -> studentId;
        $document -> fileName = $fileNameToStore;
        $document -> filePath = $path;
        $document -> documentFor = $request -> documentFor;
        $document -> status = FALSE;
        $stdDocument = $document -> save();

        if($stdDocument){
            return back() -> with('success', "Document Uploaded Successfully!");
        }
    }

    public function lc_document(Request $request){
        if($request -> hasFile('schoolLc')){
            $fileName = 'schoolLc';
            $extension = $request -> file('schoolLc') -> getClientOriginalExtension();
            $fileNameToStore = $fileName.'_'.date('h-i-s').'.'.$extension;
            $path = $request -> file('schoolLc') -> move('public/document/schoolLc/'.$request -> studentId, $fileNameToStore);
        }

        $document = new Document;
        $document -> studentId = $request -> studentId;
        $document -> fileName = $fileNameToStore;
        $document -> filePath = $path;
        $document -> documentFor = $request -> documentFor;
        $document -> status = FALSE;
        $stdDocument = $document -> save();

        if($stdDocument){
            return back() -> with('success', "Document Uploaded Successfully!");
        }
    }

    public function graduation_document(Request $request){
        if($request -> hasFile('graduationCerti')){
            $fileName = 'graduationCerti';
            $extension = $request -> file('graduationCerti') -> getClientOriginalExtension();
            $fileNameToStore = $fileName.'_'.date('h-i-s').'.'.$extension;
            $path = $request -> file('graduationCerti') -> move('public/document/graduationCerti/'.$request -> studentId, $fileNameToStore);
        }

        $document = new Document;
        $document -> studentId = $request -> studentId;
        $document -> fileName = $fileNameToStore;
        $document -> filePath = $path;
        $document -> documentFor = $request -> documentFor;
        $document -> status = FALSE;
        $stdDocument = $document -> save();

        if($stdDocument){
            return back() -> with('success', "Document Uploaded Successfully!");
        }
    }

    public function passport_document(Request $request){
        if($request -> hasFile('passportDocs')){
            $fileName = 'passportDocs';
            $extension = $request -> file('passportDocs') -> getClientOriginalExtension();
            $fileNameToStore = $fileName.'_'.date('h-i-s').'.'.$extension;
            $path = $request -> file('passportDocs') -> move('public/document/passportDocs/'.$request -> studentId, $fileNameToStore);
        }

        $document = new Document;
        $document -> studentId = $request -> studentId;
        $document -> fileName = $fileNameToStore;
        $document -> filePath = $path;
        $document -> documentFor = $request -> documentFor;
        $document -> status = FALSE;
        $stdDocument = $document -> save();

        if($stdDocument){
            return back() -> with('success', "Document Uploaded Successfully!");
        }
    }

    public function visa_document(Request $request){
        if($request -> hasFile('visaCopy')){
            $fileName = 'visaCopy';
            $extension = $request -> file('visaCopy') -> getClientOriginalExtension();
            $fileNameToStore = $fileName.'_'.date('h-i-s').'.'.$extension;
            $path = $request -> file('visaCopy') -> move('public/document/visaCopy/'.$request -> studentId, $fileNameToStore);
        }

        $document = new Document;
        $document -> studentId = $request -> studentId;
        $document -> fileName = $fileNameToStore;
        $document -> filePath = $path;
        $document -> documentFor = $request -> documentFor;
        $document -> status = FALSE;
        $stdDocument = $document -> save();

        if($stdDocument){
            return back() -> with('success', "Document Uploaded Successfully!");
        }
    }

    public function residential_one_document(Request $request){
        if($request -> hasFile('residentialProofOne')){
            $fileName = 'residentialProofOne';
            $extension = $request -> file('residentialProofOne') -> getClientOriginalExtension();
            $fileNameToStore = $fileName.'_'.date('h-i-s').'.'.$extension;
            $path = $request -> file('residentialProofOne') -> move('public/document/residentialProofOne/'.$request -> studentId, $fileNameToStore);
        }

        $document = new Document;
        $document -> studentId = $request -> studentId;
        $document -> fileName = $fileNameToStore;
        $document -> filePath = $path;
        $document -> documentFor = $request -> documentFor;
        $document -> status = FALSE;
        $stdDocument = $document -> save();

        if($stdDocument){
            return back() -> with('success', "Document Uploaded Successfully!");
        }
    }

    public function residential_two_document(Request $request){
        if($request -> hasFile('residentialProofTwo')){
            $fileName = 'residentialProofTwo';
            $extension = $request -> file('residentialProofTwo') -> getClientOriginalExtension();
            $fileNameToStore = $fileName.'_'.date('h-i-s').'.'.$extension;
            $path = $request -> file('residentialProofTwo') -> move('public/document/residentialProofTwo/'.$request -> studentId, $fileNameToStore);
        }

        $document = new Document;
        $document -> studentId = $request -> studentId;
        $document -> fileName = $fileNameToStore;
        $document -> filePath = $path;
        $document -> documentFor = $request -> documentFor;
        $document -> status = FALSE;
        $stdDocument = $document -> save();

        if($stdDocument){
            return back() -> with('success', "Document Uploaded Successfully!");
        }
    }

    public function photo_id_one_document(Request $request){
        if($request -> hasFile('photoIdOne')){
            $fileName = 'photoIdOne';
            $extension = $request -> file('photoIdOne') -> getClientOriginalExtension();
            $fileNameToStore = $fileName.'_'.date('h-i-s').'.'.$extension;
            $path = $request -> file('photoIdOne') -> move('public/document/photoIdOne/'.$request -> studentId, $fileNameToStore);
        }

        $document = new Document;
        $document -> studentId = $request -> studentId;
        $document -> fileName = $fileNameToStore;
        $document -> filePath = $path;
        $document -> documentFor = $request -> documentFor;
        $document -> status = FALSE;
        $stdDocument = $document -> save();

        if($stdDocument){
            return back() -> with('success', "Document Uploaded Successfully!");
        }
    }

    public function photo_id_two_document(Request $request){
        if($request -> hasFile('photoIdTwo')){
            $fileName = 'photoIdTwo';
            $extension = $request -> file('photoIdTwo') -> getClientOriginalExtension();
            $fileNameToStore = $fileName.'_'.date('h-i-s').'.'.$extension;
            $path = $request -> file('photoIdTwo') -> move('public/document/photoIdTwo/'.$request -> studentId, $fileNameToStore);
        }

        $document = new Document;
        $document -> studentId = $request -> studentId;
        $document -> fileName = $fileNameToStore;
        $document -> filePath = $path;
        $document -> documentFor = $request -> documentFor;
        $document -> status = FALSE;
        $stdDocument = $document -> save();

        if($stdDocument){
            return back() -> with('success', "Document Uploaded Successfully!");
        }
    }

    public function experience_document(Request $request){
        if($request -> hasFile('experienceCerti')){
            $fileName = 'experienceCerti';
            $extension = $request -> file('experienceCerti') -> getClientOriginalExtension();
            $fileNameToStore = $fileName.'_'.date('h-i-s').'.'.$extension;
            $path = $request -> file('experienceCerti') -> move('public/document/experienceCerti/'.$request -> studentId, $fileNameToStore);
        }

        $document = new Document;
        $document -> studentId = $request -> studentId;
        $document -> fileName = $fileNameToStore;
        $document -> filePath = $path;
        $document -> documentFor = $request -> documentFor;
        $document -> status = FALSE;
        $stdDocument = $document -> save();

        if($stdDocument){
            return back() -> with('success', "Document Uploaded Successfully!");
        }
    }

    public function resume_document(Request $request){
        if($request -> hasFile('resumeCv')){
            $fileName = 'resumeCv';
            $extension = $request -> file('resumeCv') -> getClientOriginalExtension();
            $fileNameToStore = $fileName.'_'.date('h-i-s').'.'.$extension;
            $path = $request -> file('resumeCv') -> move('public/document/resumeCv/'.$request -> studentId, $fileNameToStore);
        }

        $document = new Document;
        $document -> studentId = $request -> studentId;
        $document -> fileName = $fileNameToStore;
        $document -> filePath = $path;
        $document -> documentFor = $request -> documentFor;
        $document -> status = FALSE;
        $stdDocument = $document -> save();

        if($stdDocument){
            return back() -> with('success', "Document Uploaded Successfully!");
        }
    }




}


?>
