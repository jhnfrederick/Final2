<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Sitemap\SitemapGenerator;
use Illuminate\Support\Facades\Route;
use Response;
use Carbon\Carbon;
use DB;
use Auth;
use Log;
use Image;
use File;
use App\Inquiry;
use App\Mail\ThankyouMail;
use App\Mail\ThankyouMailv2;
use Illuminate\Support\Facades\Mail;

class PagesController extends Controller
{
    public function index()
    {
        $ipaddress = $_SERVER['REMOTE_ADDR'];
        $banner = Banner::where('pagetype', 0)->orderBy('id', 'ASC')->get();
        $homepageText = Homepage::where('type', 1)->first();

        if (empty($ipaddress)) {
            $ipaddress = "0.0.0.0";
        }

        $now = Carbon::now('asia/singapore');
        
        // $datenow = $now->toDateString();

        $dayname = $now->translatedFormat('l, F d');
        $timenow = date('H:i', strtotime($now));

        $tomorrow = Carbon::tomorrow('asia/singapore');
        $tomorrowname = $tomorrow->translatedFormat('l, F d');

        //Log::debug("Now:".$now);
        //Log::debug("timenow:".$timenow);

        $startmatches = Matches::whereDate('datetime', '=', $now)->whereTime('datetime','<=',\Carbon\Carbon::parse($timenow))->orderBy('datetime')->get();
        $nstartmatches = Matches::whereDate('datetime', '=', $now)->whereTime('datetime','>',\Carbon\Carbon::parse($timenow))->orderBy('datetime')->get();

        $tomorrowmatches = Matches::whereDate('datetime', '=', $tomorrow)->orderBy('datetime')->get();
        // $comparedate = $startmatches->startAt;
        // $totalDuration = $now->diffInMinutes($comparedate);

        $liveminutes = [];
        foreach($startmatches as $key => $value) {
            $comparedate = $value->datetime;
            $computation = $now->diffInMinutes($comparedate);

            $liveminutes[$key] = $computation;
        }

        $notliveminutes = [];
        foreach($nstartmatches as $key => $value) {
            $comparedate = $value->datetime;
            $computation = $now->diffInMinutes($comparedate);

            $notliveminutes[$key] = $computation;
        }

        // tabs category
        $tabCategory = Matches::select('category_name', 'category_id')->groupBy('category_name', 'category_id')->get();

        // Newsimages
        $newest_newsimg = News::where("status", 1)->where("type", "article")->orderBy('id', 'DESC')->first();
        $newest_newsimges = News::where("status", 1)->where("type", "article")->orderBy('id', 'DESC')->where("id","!=", $newest_newsimg->id)->take(4)->get();

        $news_articles = News::where("status", 1)->where("type", "article")->orderBy('id', 'DESC')->take(8)->get();

        // dd($newest_newsimg);

        return view('home', compact('ipaddress', 'banner', 'homepageText','startmatches','nstartmatches','dayname','liveminutes','notliveminutes','newest_newsimg','newest_newsimges','news_articles','tabCategory','tomorrowname','tomorrowmatches'));
    }

    public function getHomepage(Request $request)
    {
        $homepage = Homepage::where('type', 0)->get();

        return json_encode($homepage);
    }
    
     public function sitemap2() 
    {
     $xmlString = file_get_contents(public_path('/sitemap/sitemap.xml'));
        $xmlObject = simplexml_load_string($xmlString);
                   
        $json = json_encode($xmlObject);
        $phpArray = json_decode($json, true); 

        return response()
             ->view('sitemap', compact('phpArray'))
         ->header('Content-Type', 'text/xml', 'charset=utf-8');
    }

    public function homepageFilter($category_name) 
    {
        $ipaddress = $_SERVER['REMOTE_ADDR'];
        $banner = Banner::where('pagetype', 0)->orderBy('id', 'ASC')->get();
        $homepageText = Homepage::where('type', 1)->first();

        if (empty($ipaddress)) {
            $ipaddress = "0.0.0.0";
        }

        $now = Carbon::now('asia/singapore');

        $dayname = $now->translatedFormat('l, F d');
        $timenow = date('H:i', strtotime($now));

        $tomorrow = Carbon::tomorrow('asia/singapore');
        $tomorrowname = $tomorrow->translatedFormat('l, F d');

        if($category_name == '足球') {
            $category_name = 'zuqiu';
        } elseif($category_name == '电竞') {
            $category_name = 'dianzijingji';
        } elseif ($category_name == '篮球') {
            $category_name = 'lanqiu';
        }

        $startmatches = Matches::where('category_id', $category_name)->whereDate('datetime', '=', $now)->whereTime('datetime','<=',\Carbon\Carbon::parse($timenow))->orderBy('datetime')->get();
        $nstartmatches = Matches::where('category_id', $category_name)->whereDate('datetime', '=', $now)->whereTime('datetime','>',\Carbon\Carbon::parse($timenow))->orderBy('datetime')->get();
        $tomorrowmatches = Matches::where('category_id', $category_name)->whereDate('datetime', '=', $tomorrow)->orderBy('datetime')->get();

        $liveminutes = [];
        foreach($startmatches as $key => $value) {
            $comparedate = $value->datetime;
            $computation = $now->diffInMinutes($comparedate);

            $liveminutes[$key] = $computation;
        }

        $notliveminutes = [];
        foreach($nstartmatches as $key => $value) {
            $comparedate = $value->datetime;
            $computation = $now->diffInMinutes($comparedate);

            $notliveminutes[$key] = $computation;
        }

        // tabs category
        $tabCategory = Matches::select('category_name', 'category_id')->groupBy('category_name', 'category_id')->get();

        // Newsimages
        $newest_newsimg = News::where("status", 1)->where("type", "article")->orderBy('id', 'DESC')->first();
        $newest_newsimges = News::where("status", 1)->where("type", "article")->orderBy('id', 'DESC')->where("id","!=", $newest_newsimg->id)->take(3)->get();
        $news_articles = News::where("status", 1)->where("type", "article")->orderBy('id', 'DESC')->take(8)->get();

        return view('home', compact('ipaddress', 'banner', 'homepageText', 'startmatches','nstartmatches','dayname','liveminutes','notliveminutes','newest_newsimg','newest_newsimges','news_articles','tabCategory','category_name','tomorrowname','tomorrowmatches'));
    }

    public function homepageTabFilter(Request $request)
    {
        $now = Carbon::now('asia/singapore');

        $dayname = $now->translatedFormat('l, F d');
        $timenow = date('H:i', strtotime($now));

        $tomorrow = Carbon::tomorrow('asia/singapore');
        $tomorrowname = $tomorrow->translatedFormat('l, F d');

        $startmatches = Matches::where('category_name', $request->category_name)->whereDate('datetime', '=', $now)->whereTime('datetime','<=',\Carbon\Carbon::parse($timenow))->orderBy('datetime')->get();
        $nstartmatches = Matches::where('category_name', $request->category_name)->whereDate('datetime', '=', $now)->whereTime('datetime','>',\Carbon\Carbon::parse($timenow))->orderBy('datetime')->get();

        $tomorrowmatches = Matches::where('category_id', $request->category_name)->whereDate('datetime', '=', $tomorrow)->orderBy('datetime')->get();

        $liveminutes = [];
        foreach($startmatches as $key => $value) {
            $comparedate = $value->datetime;
            $computation = $now->diffInMinutes($comparedate);

            $liveminutes[$key] = $computation;
        }

        $notliveminutes = [];
        foreach($nstartmatches as $key => $value) {
            $comparedate = $value->datetime;
            $computation = $now->diffInMinutes($comparedate);

            $notliveminutes[$key] = $computation;
        }

        $tomorrowliveminutes = [];
        foreach($nstartmatches as $key => $value) {
            $comparedate = $value->datetime;
            $computation = $now->diffInMinutes($comparedate);

            $tomorrowliveminutes[$key] = $computation;
        }


        $data = [];
        $data[0] = $startmatches;
        $data[1] = $liveminutes;
        $data[2] = $nstartmatches;
        $data[3] = $notliveminutes;
        $data[4] = $tomorrowliveminutes;
        return json_encode($data);
    }


        public function dashboard()
    {


        return view('dashboard');        
    }

    public function saveinquiry(Request $request) 
    {
            $inquiry = new Inquiry;
            $inquiry->email = $request->email;
            $inquiry->inquiry = $request->inquiry;
            $inquiry->offer = $request->offer;
            $inquiry->weeks = $request->weeks;
            
            $to = 'cram.monkey2022@gmail.com';

          $inquiry->save();
          Mail::to($to)->send(new ThankyouMail($request->email,$request->inquiry,$request->offer,$request->weeks));
          
          Mail::to($inquiry->email)->send(new ThankyouMailv2($request->email,$request->inquiry,$request->offer,$request->weeks));
          
        return response()->json(array('status'=> 'OK'));
    }
    
    public function saveabuse(Request $request) 
    {
            $report = new Report;
            $report->report = $request->choice;
            $report->other = $request->othertext;
            $report->description = $request->desc;
            $report->email = $request->email;
            $report->projectid = $request->project;

          $report->save();
          Mail::to($request->email)->send(new ReportMail($request->email));
        return response()->json(array('status'=> 'OK'));
    }

    public function updatefee(Request $request) 
    {
        DB::table('projects')
        ->where('id',$request->id)
        ->update([
          'actual_amount'=>$request->fee
        ]);

        return response()->json(array('status'=> 'OK'));
    }

    public function payfee(Request $request) 
    {
        DB::table('payments')
            ->insert([
                'projectid'=>$request->id
                ,'userid'=>Auth::user()->id
                ,'tutorid'=>$request->tutorid
                ,'amount'=>$request->fee
            ]);

        DB::table('users')
                ->where('id',$request->tutorid)
                ->increment('balance',$request->fee);

        DB::table('users')
                ->where('id',Auth::user()->id)
                ->decrement('balance',$request->fee);

        DB::table('projects')
        ->where('id',$request->id)
        ->update([
          'payment'=>1
        ]);

        return response()->json(array('status'=> 'OK'));
    }

    public function closepnow(Request $request) 
    {
        DB::table('ratings')
            ->insert([
                'tutorid'=>$request->tutorid
                ,'rate'=>$request->rate
                ,'comment'=>$request->reason
            ]);

        DB::table('projects')
        ->where('id',$request->projectid)
        ->update([
          'status'=>1
        ]);

        return response()->json(array('status'=> 'OK'));
    }

    public function reqcloseproj(Request $request) 
    {
            $petition = new Petition;
            $petition->projectid = $request->projectid;
            $petition->reason = $request->reason;
            $petition->details = $request->details;

          $petition->save();
        return response()->json(array('status'=> 'OK'));
    }
    
    public function saveapplication(Request $request) 
    {
        $now = Carbon::now('asia/singapore');
        $datenow = $now->format('Y-m-d H:i:s');
        $filename="";
        $filename2="";
        $filename3="";
        
        if($request->hasFile('photo')) {
	        $bannerImg = $request->file('photo');
	        $filename     = time() . '1.' . $bannerImg->getClientOriginalExtension();
	        $bannerImg->storeAs('toPath',$filename, ['disk' => 'my_files']);
	    }
	    
	    if($request->hasFile('diploma')) {
	        $bannerImg = $request->file('diploma');
	        $filename2     = time() . '2.' . $bannerImg->getClientOriginalExtension();
	        $bannerImg->storeAs('toPath',$filename2, ['disk' => 'my_files']);
	    }
	    
	    if($request->hasFile('resume')) {
            
	        $bannerImg = $request->file('resume');
	        $filename3     = time() . '3.' . $bannerImg->getClientOriginalExtension();
	        $bannerImg->storeAs('toPath',$filename3, ['disk' => 'my_files']);
	    }
	    
            $application = new Application;
            $application->username = $request->name;
            $application->contact = $request->number;
            $application->email = $request->email;
            $application->photo = $filename;
            $application->educationlevel = $request->education;
            $application->country = $request->country;
            $application->university = $request->university;
            $application->majors = $request->majors;
            $application->graduationyear = $request->graduation;
            $application->diploma = $filename2;
            $application->fields = $request->fields;
            $application->about = $request->aboutself;
            $application->resume = $filename3;
            $application->social = $request->social;

          $application->save();
          Mail::to($request->email)->send(new ApplicationMail($request->email));
        return response()->json(array('status'=> 'OK'));
    }
    
        public function submitproject(Request $request) 
    {
            $project = new Project;
            $project->userid = $request->userid;
            $project->classification = $request->classification;
            $project->category = $request->category;
            $project->budget = $request->budget;
            $project->days = $request->days;
            $project->details = $request->details;

          $project->save();
        return response()->json(array('status'=> 'OK'));
    }
    
        public function submitwithdrawal(Request $request) 
    {
            $withdrawal = new Withdrawal;
            $withdrawal->tutorid = $request->userid;
            $withdrawal->amount = $request->balance;
            $withdrawal->accounttype = $request->accounttype;
            $withdrawal->accountname = $request->accountname;
            $withdrawal->accountnumber = $request->accountnumber;
            $withdrawal->details = $request->details;

          $withdrawal->save();
        return response()->json(array('status'=> 'OK'));
    }

    public function getProjectDetails($projectid,$userid){
        $request = request();
        $url = $request->fullUrl();
        Log::debug($url);

        $matchinfo = DB::table('projects')
                    ->where('id',$projectid)
                    ->where('userid',$userid)
                    ->first();

        $comments = DB::table('comments')
                    ->where('projectid',$projectid)
                    ->where('userid',$userid)
                    ->first();
        if($comments){
            $commentsid = $comments->id;
        }
        else{
            $commentsid = DB::table('comments')
                ->insertGetId([
                    'projectid'=>$projectid
                    ,'userid'=>$userid
                ]);
        }
        $commentcontents = DB::table('comment_contents')
                        ->where('commentid',$commentsid)
                        ->orderBy('createdate','desc')
                        ->get();

                        $chat = DB::table('chats')
                        ->where('id',$projectid)
                        ->where('userid',$userid)
                        ->first();

            if($chat){
                $chatid = $chat->id;
            }
            else{
                $chatid = DB::table('chats')
                    ->insertGetId([
                        'projectid'=>$projectid
                        ,'userid'=>$userid
                    ]);
            }
            $chatcontents = DB::table('chat_contents')
                            ->where('chatid',$chatid)
                            ->orderBy('createdate')
                            ->get();
                        
        if($matchinfo->tutorid==null){
        $views = 'bidding';
        }else{
        $views = 'chat';
    } 
        
        return view($views, compact('projectid','userid','matchinfo','comments','commentsid','commentcontents','chatid','chatcontents'));
    }

    public function inputComment(){
        $request = request();
        $commentid = $request->id;
        $contents = $request->contents;

        if(Auth::check()){

            DB::table('comment_contents')
                ->insert([
                    'commentid'=>$commentid
                    ,'id_user'=>Auth::user()->id
                    ,'contents'=>$contents
                ]);

            $commentcontents = DB::table('comment_contents as c')
                        ->join('users as u','u.id','=','c.id_user')
                        ->select('c.contents','u.name')
                        ->where('c.commentid',$commentid)
                        ->orderBy('c.createdate')
                        ->get();
            return json_encode(array('status'=>'OK','contents'=>$commentcontents));
        }
        else{
            return json_encode(array('status'=>'Please login first.'));
        }
    }

    public function getComment(){
        $request = request();
        $commentid = $request->id;
        $commentcontents = DB::table('comment_contents')
                        ->where('commentid',$commentid)
                        ->orderBy('createdate','desc')
                        ->get();

        $output='';
        $count=0;

        foreach($commentcontents as $c){
        

        $output .= '<div class="comment-box">
        <span class="commenter-pic">
          <img src="/img/new/'.General::getIcon($c->id_user).'.png" class="img-fluid">
        </span>
        <span class="commenter-name">
          <a href="#">'.General::getName($c->id_user).'</a> <span class="comment-time">'.Carbon::parse($c->createdate)->diffForHumans().'</span>
        </span>       
        <p class="comment-txt more">'.$c->contents.'</p>         
          </div>';
        }

        return json_encode(array('status'=>'OK','contents'=>$commentcontents,'output'=>$output));
    }

    public function getChat(){
        $request = request();
        $chatid = $request->id;
        $chatcontents = DB::table('chat_contents')
                        ->where('chatid',$chatid)
                        ->orderBy('createdate','asc')
                        ->get();

        $output='';
        $count=0;
        $a=0;
        $b=0;

        if(Auth::user()->type==0){
            $img = 'tutor';
        }else{
            $img = 'user';
        }

        if(count($chatcontents)>0){
        foreach($chatcontents as $c){
        $count++;

        if(Auth::user()->id != $c->userid){

            if($count>1){
                if($a==0 && $b>1){
                    $output .='<p class="meta"><time style="color: gray">'.Carbon::parse($c->createdate)->diffForHumans().'</time></p>
            </div>
            </div>';
                }

            
            }

            $a++;
            $b=0;

            if($a==1){
            $output .= '<div class="media media-chat"> <img class="avatar" src="'.url('/').'/img/new/'.$img.'.png" alt="...">';
            if(str_contains($c->contents, '.png') || str_contains($c->contents,'.xlsx') || str_contains($c->contents,'.docx') || str_contains($c->contents,'.pdf') || str_contains($c->contents,'.jpeg') || str_contains($c->contents,'.jpg') || str_contains($c->contents,'.ppt') || str_contains($c->contents,'.pptx') || str_contains($c->contents,'.txt') || str_contains($c->contents,'.html') || str_contains($c->contents,'.csv')){
                $output .='<p><a download href="/img/TDK/Chats/toPath/'.$c->contents.'">'.$c->contents.'</a></p>'; 
            }else{
            $output .='<div class="media-body"><p>'.$c->contents.'</p>';
            }
            }else{
                if(str_contains($c->contents, '.png') || str_contains($c->contents,'.xlsx') || str_contains($c->contents,'.docx') || str_contains($c->contents,'.pdf') || str_contains($c->contents,'.jpeg') || str_contains($c->contents,'.jpg') || str_contains($c->contents,'.ppt') || str_contains($c->contents,'.pptx') || str_contains($c->contents,'.txt') || str_contains($c->contents,'.html') || str_contains($c->contents,'.csv')){
                    $output .='<p><a download href="/img/TDK/Chats/toPath/'.$c->contents.'">'.$c->contents.'</a></p>';  
                }else{
            $output .='<p>'.$c->contents.'</p>';  
                }
            }           

        }else{

            if($count>1){
                if($b==0 && $a>1){
                    $output .='<p class="meta"><time datetime="2018">'.Carbon::parse($c->createdate)->diffForHumans().'</time></p>
            </div>
            </div>';
                }

            }

            $b++;
            $a=0;

            if($b==1){
            $output .= '<div class="media media-chat media-chat-reverse">
            <div class="media-body">';
            if(str_contains($c->contents, '.png') || str_contains($c->contents,'.xlsx') || str_contains($c->contents,'.docx') || str_contains($c->contents,'.pdf') || str_contains($c->contents,'.jpeg') || str_contains($c->contents,'.jpg') || str_contains($c->contents,'.ppt') || str_contains($c->contents,'.pptx') || str_contains($c->contents,'.txt') || str_contains($c->contents,'.html') || str_contains($c->contents,'.csv')){
                $output .='<p><a download href="/img/TDK/Chats/toPath/'.$c->contents.'">'.$c->contents.'</a></p>'; 
            }else{
            $output .= '<p>'.$c->contents.'</p>';
            }
            }else{
            if(str_contains($c->contents, '.png') || str_contains($c->contents,'.xlsx') || str_contains($c->contents,'.docx') || str_contains($c->contents,'.pdf') || str_contains($c->contents,'.jpeg') || str_contains($c->contents,'.jpg') || str_contains($c->contents,'.ppt') || str_contains($c->contents,'.pptx') || str_contains($c->contents,'.txt') || str_contains($c->contents,'.html') || str_contains($c->contents,'.csv')){
                $output .='<p><a download href="/img/TDK/Chats/toPath/'.$c->contents.'">'.$c->contents.'</a></p>'; 
            }else{
            $output .='<p>'.$c->contents.'</p>';
            }
            }

        }
        
        }

        $output .='<p class="meta"><time style="color: gray">'.Carbon::parse($c->createdate)->diffForHumans().'</time></p>
        </div>
        </div>';
    }

        return json_encode(array('status'=>'OK','contents'=>$chatcontents,'output'=>$output));
    }

    public function inputChat(){
        $request = request();
        $chatid = $request->id;
        $contents = $request->contents;

        if(Auth::check()){

            DB::table('chat_contents')
                ->insert([
                    'chatid'=>$chatid
                    ,'userid'=>Auth::user()->id
                    ,'contents'=>$contents
                ]);

            $chatcontents = DB::table('chat_contents as c')
                        ->join('users as u','u.id','=','c.userid')
                        ->select('c.contents','u.name')
                        ->where('c.chatid',$chatid)
                        ->orderBy('c.createdate')
                        ->get();
            return json_encode(array('status'=>'OK','contents'=>$chatcontents));
        }
        else{
            return json_encode(array('status'=>'Please login first.'));
        }
    }

    public function inputChat2(){
        $request = request();
        $chatid = $request->chatid;
        $contents = $request->contents;

        if(Auth::check()){

                $bannerImg = $request->file('file');
                $filename     = time() . '1.' . $bannerImg->getClientOriginalExtension();
                $bannerImg->storeAs('toPath',$filename, ['disk' => 'my_files2']);

            DB::table('chat_contents')
                ->insert([
                    'chatid'=>$chatid
                    ,'userid'=>Auth::user()->id
                    ,'contents'=>$filename
                ]);

            $chatcontents = DB::table('chat_contents as c')
                        ->join('users as u','u.id','=','c.userid')
                        ->select('c.contents','u.name')
                        ->where('c.chatid',$chatid)
                        ->orderBy('c.createdate')
                        ->get();
            return json_encode(array('status'=>'OK','contents'=>$chatcontents));
        }
        else{
            return json_encode(array('status'=>'Please login first.'));
        }
    }


     public function page3()
    {
        $banner = Banner::where('pagetype', 0)->orderBy('id', 'ASC')->get();
        $ipaddress = $_SERVER['REMOTE_ADDR'];

        if (empty($ipaddress)) {
            $ipaddress = "0.0.0.0";
        }
        
        $closeproject = DB::table('projects')
                        ->where('status',1)
                        ->orderBy('id','desc')
                        ->get();

        return view('forcrammers', compact('ipaddress','banner','closeproject'));        
    }
    
         public function page6()
    {
        $banner = Banner::where('pagetype', 0)->orderBy('id', 'ASC')->get();
        $ipaddress = $_SERVER['REMOTE_ADDR'];

        if (empty($ipaddress)) {
            $ipaddress = "0.0.0.0";
        }


        return view('fortutors', compact('ipaddress','banner'));        
    }
    
    public function getcloseproject(){
        $request = request();
        $status = $request->status;
        $userid = $request->userid;
        
        //DB::enableQueryLog();
        
            $projects = Project::where('status',$status)->where('userid',$userid)
                    ->get();
                    
                    
        $output='';
        $output2='';
        if(count($projects)>0){
            $cnt = 0;
           foreach($projects as $index => $m){
                    
                        $cnt++;
                        
                        $username = General::getName($userid);
                        $tutorname = General::getName($m->tutorid);
                        
                        if($m->payment==0){
                            $paymentstatus="unpaid";
                        }else{
                            $paymentstatus="paid";
                        }
                        
                        if($m->status==0){
                            $status="open";
                        }else{
                            $status="close";
                        }
                        
                        if($m->tutorid == null){
                            $livestreamlink1 = "http://127.0.0.1:8000/bidding/";
                        }else{
                            $livestreamlink1 = "http://127.0.0.1:8000/chatpage/";
                        }
                        
                        $output .= '
                                    <div class="live" style="color: rgb(21 44 72);" onclick="window.open(\''.$livestreamlink1.''.$m->id.'/'.$m->userid.'\');"> ';
                                    
                        $output2 .= '
                                    <div class="live" style="color: rgb(21 44 72);" onclick="window.open(\''.$livestreamlink1.''.$m->id.'/'.$m->userid.'\');"> ';


                        $output .= '<div class="headRow">
                                                <div class="column2" style="float:left; display:table-column; width:33% ; padding-top: 5px; padding-bottom: 5px;" align="left">Project ID: '.$m->id.'</div>
                                                <div class="column2" style="float:left; display:table-column; width:33%; padding-top: 5px; padding-bottom: 5px;" align="left">Payment Status: '.$paymentstatus.'</div>
                                                <div class="column2" style="float:left; display:table-column; width:33%; padding-top: 5px; padding-bottom: 5px;" align="left">Status: '.$status.'</div>
                                            </div>
                                            
                                    <div class="headRow">            
                                                <div class="column2" style="float:left; display:table-column; width:100%; padding-top: 5px;" align="left">User Name: '.$username.'</div>
                                                </div>
                                                
                                    <div class="headRow">            
                                                <div class="column2" style="float:left; display:table-column; width:100%; padding-top: 5px;" align="left">Tutor Name: '.$tutorname.'</div>
                                                </div>

                                                <div style="display:table-row; padding-bottom: 5px;">
                                                <div class="column2" style="float:left; display:table-column; width:90%; padding-top: 5px; " align="left">Details: '.$m->details.' </div>
                                                </div>';

                        $output2 .= '<div class="headRow">
                                                <div class="column2" style="float:left; display:table-column; width:33% ; padding-top: 5px; padding-bottom: 5px;" align="left">Project ID: '.$m->id.'</div>
                                                <div class="column2" style="float:left; display:table-column; width:66%; padding-top: 5px; padding-bottom: 5px;" align="left">Payment Status: '.$paymentstatus.'</div>
                                                </div>
                                            
                                    <div class="headRow">            
                                                <div class="column2" style="float:left; display:table-column; width:99%; padding-top: 5px; padding-bottom: 5px;" align="left">Status: '.$status.'</div>
                                                </div>
                                            
                                    <div class="headRow">            
                                                <div class="column2" style="float:left; display:table-column; width:99%; padding-top: 5px;" align="left">User Name: '.$username.'</div>
                                                </div>
                                                
                                    <div class="headRow">            
                                                <div class="column2" style="float:left; display:table-column; width:99%; padding-top: 5px;" align="left">Tutor Name: '.$tutorname.'</div>
                                                </div>

                                                <div style="display:table-row; padding-bottom: 5px;">
                                                <div class="column2" style="float:left; display:table-column; width:70%; padding-top: 5px; " align="left">Details: '.$m->details.' </div>
                                                </div>';

                        
                        $output .= '
                                        </div>
                             
                        ';
                        
                        $output2 .= '
                                        </div>
                             
                        ';
                    
                
            }

            if($cnt < 1){
                $output = '<div class="live" style="color: #F8D12B; height: 100px; font-size: 30px;"><b>No Projects</b></div>';
                $output2 = '<div class="live" style="color: #F8D12B; height: 100px; font-size: 30px;"><b>No Projects</b></div>';
            }
        }
        else{
            $output = '<div class="live" style="color: #F8D12B; height: 100px; font-size: 30px;"><b>No Projects</b></div>';
            $output2 = '<div class="live" style="color: #F8D12B; height: 100px; font-size: 30px;"><b>No Projects</b></div>';
        }
        return json_encode(array('contents'=>$output,'output'=>$output2));
    }

    public function getcloseproject2(){
        $request = request();
        $status = $request->status;
        $tutorid = $request->tutorid;
        
        //DB::enableQueryLog();
        
        $tutorclassifications = General::getTutorClassifications($tutorid);
        
        $tutorclass = explode("|",$tutorclassifications);
        
        if($status == "2"){
            $projects = Project::where('status',"=",0)
                    ->where('tutorid',null)
                    ->get();
        }else{
            $projects = Project::where('status',$status)->where('tutorid',$tutorid)
                    ->get();
        }

                    
                    
        $output='';
        $output2='';
        if(count($projects)>0){
            $cnt = 0;
           foreach($projects as $index => $m){
               foreach($tutorclass as $t){
                   
                   if($m->classification==$t){
                    
                        $cnt++;
                        
                        $username = General::getName($m->userid);
                        $tutorname = General::getName($m->tutorid);
                        $classification = General::getClassificationName($m->classification);
                        
                         if($m->payment==0){
                            $paymentstatus="unpaid";
                        }else{
                            $paymentstatus="paid";
                        }
                        
                        if($m->status==0){
                            $status="open";
                        }else{
                            $status="close";
                        }
                        
                        if($m->tutorid == null){
                            $livestreamlink1 = "http://127.0.0.1:8000/bidding/";
                        }else{
                            $livestreamlink1 = "http://127.0.0.1:8000/chatpage/";
                        }
                        
                        $output .= '
                        <div class="live" style="color: rgb(21 44 72);" onclick="window.open(\''.$livestreamlink1.''.$m->id.'/'.$m->userid.'\');"> ';
                                    
                        $output2 .= '
                                    <div class="live" style="color: rgb(21 44 72);"  onclick="window.open(\''.$livestreamlink1.''.$m->id.'/'.$m->userid.'\');"> ';


                        $output .= '<div class="headRow">
                                                <div class="column2" style="float:left; display:table-column; width:33% ; padding-top: 5px; padding-bottom: 5px;" align="left">Project ID: '.$m->id.'</div>
                                                <div class="column2" style="float:left; display:table-column; width:33%; padding-top: 5px; padding-bottom: 5px;" align="left">Payment Status: '.$paymentstatus.'</div>
                                                <div class="column2" style="float:left; display:table-column; width:33%; padding-top: 5px; padding-bottom: 5px;" align="left">Status: '.$status.'</div>
                                            </div>
                                            
                                    <div class="headRow">            
                                                <div class="column2" style="float:left; display:table-column; width:100%; padding-top: 5px;" align="left">User Name: '.$username.'</div>
                                                </div>
                                                
                                    <div class="headRow">            
                                                <div class="column2" style="float:left; display:table-column; width:100%; padding-top: 5px;" align="left">Tutor Name: '.$tutorname.'</div>
                                                </div>
                                                
                                    <div class="headRow">            
                                                <div class="column2" style="float:left; display:table-column; width:100%; padding-top: 5px;" align="left">Category: '.$classification.'</div>
                                                </div>

                                                <div style="display:table-row; padding-bottom: 5px;">
                                                <div class="column2" style="float:left; display:table-column; width:90%; padding-top: 5px; " align="left">Details: '.$m->details.' </div>
                                                </div>';
                        
                        $output2 .= '<div class="headRow">
                                                <div class="column2" style="float:left; display:table-column; width:33% ; padding-top: 5px; padding-bottom: 5px;" align="left">Project ID: '.$m->id.'</div>
                                                <div class="column2" style="float:left; display:table-column; width:33%; padding-top: 5px; padding-bottom: 5px;" align="left">Payment Status: '.$paymentstatus.'</div>
                                                <div class="column2" style="float:left; display:table-column; width:33%; padding-top: 5px; padding-bottom: 5px;" align="left">Status: '.$status.'</div>
                                            </div>
                                            
                                    <div class="headRow">            
                                                <div class="column2" style="float:left; display:table-column; width:100%; padding-top: 5px;" align="left">User Name: '.$username.'</div>
                                                </div>
                                                
                                    <div class="headRow">            
                                                <div class="column2" style="float:left; display:table-column; width:100%; padding-top: 5px;" align="left">Tutor Name: '.$tutorname.'</div>
                                                </div>
                                                
                                    <div class="headRow">            
                                                <div class="column2" style="float:left; display:table-column; width:100%; padding-top: 5px;" align="left">Category: '.$classification.'</div>
                                                </div>

                                                <div style="display:table-row; padding-bottom: 5px;">
                                                <div class="column2" style="float:left; display:table-column; width:90%; padding-top: 5px; " align="left">Details: '.$m->details.' </div>
                                                </div>';

                        
                        $output .= '
                                        </div>
                             
                        ';
                        
                        $output2 .= '
                                        </div>
                             
                        ';
                   }
                    
               }
            }

            if($cnt < 1){
                $output = '<div class="live" style="color: #F8D12B; height: 100px; font-size: 30px;"><b>No Projects</b></div>';
                $output2 = '<div class="live" style="color: #F8D12B; height: 100px; font-size: 30px;"><b>No Projects</b></div>';
            }
        }
        else{
            $output = '<div class="live" style="color: #F8D12B; height: 100px; font-size: 30px;"><b>No Projects</b></div>';
            $output2 = '<div class="live" style="color: #F8D12B; height: 100px; font-size: 30px;"><b>No Projects</b></div>';
        }
        return json_encode(array('contents'=>$output,'output'=>$output2));
    }
    
    
    public function getTopupHistory(){
        $request = request();
        $userid = $request->userid;
        
        //DB::enableQueryLog();
        
            $deposit = DB::table('depositlogs')
                    ->where('userid',$userid)
                    ->get();
                    
                    
        $output='';
        $output2='';
        if(count($deposit)>0){
            $cnt = 0;
           foreach($deposit as $index => $m){
                    
                        $cnt++;
                        
                        $output .= '
                                    <div class="live" style="color: rgb(21 44 72);"> ';
                                    
                        $output2 .= '
                                    <div class="live" style="color: rgb(21 44 72);"> ';


                        $output .= '<div class="headRow">
                                                <div class="column2" style="float:left; display:table-column; width:33% ; padding-top: 5px; padding-bottom: 5px;" align="left">&emsp; Amount: '.round($m->amount,2).'</div>
                                                <div class="column2" style="float:left; display:table-column; width:33%; padding-top: 5px; padding-bottom: 5px;" align="left">Reference ID: '.$m->referenceid.'</div>
                                                <div class="column2" style="float:left; display:table-column; width:33%; padding-top: 5px; padding-bottom: 5px;" align="left">Datetime: '.$m->created_at.'</div>
                                            </div>';
                                            
                        $output2 .= '<div class="headRow">
                                                <div class="column2" style="float:left; display:table-column; width:33% ; padding-top: 5px; padding-bottom: 5px;" align="left">&emsp; Amount: '.round($m->amount,2).'</div>
                                                <div class="column2" style="float:left; display:table-column; width:33%; padding-top: 5px; padding-bottom: 5px;" align="left">Reference ID: '.$m->referenceid.'</div>
                                                <div class="column2" style="float:left; display:table-column; width:33%; padding-top: 5px; padding-bottom: 5px;" align="left">Datetime: '.$m->created_at.'</div>
                                            </div>';


                        
                        $output .= '
                                        </div>
                             
                        ';
                        
                        $output2 .= '
                                        </div>
                             
                        ';
                    
                
            }

            if($cnt < 1){
                $output = '<div class="live" style="color: #F8D12B; height: 100px; font-size: 30px;"><b>No History</b></div>';
                $output2 = '<div class="live" style="color: #F8D12B; height: 100px; font-size: 30px;"><b>No History</b></div>';
            }
        }
        else{
            $output = '<div class="live" style="color: #F8D12B; height: 100px; font-size: 30px;"><b>No History</b></div>';
            $output2 = '<div class="live" style="color: #F8D12B; height: 100px; font-size: 30px;"><b>No History</b></div>';
        }
        return json_encode(array('contents'=>$output,'output'=>$output2));
    }

    public function getPaymentHistory(){
        $request = request();
        $userid = $request->userid;
        
        //DB::enableQueryLog();
        
            $deposit = DB::table('payments')
                    ->where('userid',$userid)
                    ->get();
                    
                    
        $output='';
        $output2='';
        if(count($deposit)>0){
            $cnt = 0;
           foreach($deposit as $index => $m){
                    
                        $cnt++;

                        $tutorname = General::getName($m->tutorid);
                        
                        $output .= '
                                    <div class="live" style="color: rgb(21 44 72);"> ';
                                    
                        $output2 .= '
                                    <div class="live" style="color: rgb(21 44 72);"> ';


                        $output .= '<div class="headRow">
                                                <div class="column2" style="float:left; display:table-column; width:25% ; padding-top: 5px; padding-bottom: 5px;" align="left">&emsp; Amount: '.round($m->amount,2).'</div>
                                                <div class="column2" style="float:left; display:table-column; width:25%; padding-top: 5px; padding-bottom: 5px;" align="left">Project ID: '.$m->projectid.'</div>
                                                <div class="column2" style="float:left; display:table-column; width:25%; padding-top: 5px; padding-bottom: 5px;" align="left">Tutor: '.$tutorname.'</div>
                                                <div class="column2" style="float:left; display:table-column; width:25%; padding-top: 5px; padding-bottom: 5px;" align="left">Datetime: '.$m->created_at.'</div>
                                            </div>';
                                            
                        $output2 .= '<div class="headRow">
                                                <div class="column2" style="float:left; display:table-column; width:25% ; padding-top: 5px; padding-bottom: 5px;" align="left">&emsp; Amount: '.round($m->amount,2).'</div>
                                                <div class="column2" style="float:left; display:table-column; width:25%; padding-top: 5px; padding-bottom: 5px;" align="left">Project ID: '.$m->projectid.'</div>
                                                <div class="column2" style="float:left; display:table-column; width:25%; padding-top: 5px; padding-bottom: 5px;" align="left">Tutor: '.$tutorname.'</div>
                                                <div class="column2" style="float:left; display:table-column; width:25%; padding-top: 5px; padding-bottom: 5px;" align="left">Datetime: '.$m->created_at.'</div>
                                            </div>';


                        
                        $output .= '
                                        </div>
                             
                        ';
                        
                        $output2 .= '
                                        </div>
                             
                        ';
                    
                
            }

            if($cnt < 1){
                $output = '<div class="live" style="color: #F8D12B; height: 100px; font-size: 30px;"><b>No History</b></div>';
                $output2 = '<div class="live" style="color: #F8D12B; height: 100px; font-size: 30px;"><b>No History</b></div>';
            }
        }
        else{
            $output = '<div class="live" style="color: #F8D12B; height: 100px; font-size: 30px;"><b>No History</b></div>';
            $output2 = '<div class="live" style="color: #F8D12B; height: 100px; font-size: 30px;"><b>No History</b></div>';
        }
        return json_encode(array('contents'=>$output,'output'=>$output2));
    }

    public function getPaymentHistory2(){
        $request = request();
        $tutorid = $request->tutorid;
        
        //DB::enableQueryLog();
        
            $deposit = DB::table('payments')
                    ->where('tutorid',$tutorid)
                    ->get();
                    
                    
        $output='';
        $output2='';
        if(count($deposit)>0){
            $cnt = 0;
           foreach($deposit as $index => $m){
                    
                        $cnt++;

                        $crammername = General::getName($m->userid);
                        
                        $output .= '
                                    <div class="live" style="color: rgb(21 44 72);"> ';
                                    
                        $output2 .= '
                                    <div class="live" style="color: rgb(21 44 72);"> ';


                        $output .= '<div class="headRow">
                                                <div class="column2" style="float:left; display:table-column; width:25% ; padding-top: 5px; padding-bottom: 5px;" align="left">&emsp; Amount: '.round($m->amount,2).'</div>
                                                <div class="column2" style="float:left; display:table-column; width:25%; padding-top: 5px; padding-bottom: 5px;" align="left">Project ID: '.$m->projectid.'</div>
                                                <div class="column2" style="float:left; display:table-column; width:25%; padding-top: 5px; padding-bottom: 5px;" align="left">Crammer: '.$crammername.'</div>
                                                <div class="column2" style="float:left; display:table-column; width:25%; padding-top: 5px; padding-bottom: 5px;" align="left">Datetime: '.$m->created_at.'</div>
                                            </div>';
                                            
                        $output2 .= '<div class="headRow">
                                                <div class="column2" style="float:left; display:table-column; width:25% ; padding-top: 5px; padding-bottom: 5px;" align="left">&emsp; Amount: '.round($m->amount,2).'</div>
                                                <div class="column2" style="float:left; display:table-column; width:25%; padding-top: 5px; padding-bottom: 5px;" align="left">Project ID: '.$m->projectid.'</div>
                                                <div class="column2" style="float:left; display:table-column; width:25%; padding-top: 5px; padding-bottom: 5px;" align="left">Crammer: '.$crammername.'</div>
                                                <div class="column2" style="float:left; display:table-column; width:25%; padding-top: 5px; padding-bottom: 5px;" align="left">Datetime: '.$m->created_at.'</div>
                                            </div>';


                        
                        $output .= '
                                        </div>
                             
                        ';
                        
                        $output2 .= '
                                        </div>
                             
                        ';
                    
                
            }

            if($cnt < 1){
                $output = '<div class="live" style="color: #F8D12B; height: 100px; font-size: 30px;"><b>No History</b></div>';
                $output2 = '<div class="live" style="color: #F8D12B; height: 100px; font-size: 30px;"><b>No History</b></div>';
            }
        }
        else{
            $output = '<div class="live" style="color: #F8D12B; height: 100px; font-size: 30px;"><b>No History</b></div>';
            $output2 = '<div class="live" style="color: #F8D12B; height: 100px; font-size: 30px;"><b>No History</b></div>';
        }
        return json_encode(array('contents'=>$output,'output'=>$output2));
    }
    
    public function getWithdrawalHistory(){
        $request = request();
        $tutorid = $request->tutorid;
        
        //DB::enableQueryLog();
        
            $withdrawal = DB::table('withdrawals')
                    ->where('tutorid',$tutorid)
                    ->orderby('created_at','desc')
                    ->get();
                    
                    
        $output='';
        if(count($withdrawal)>0){
            $cnt = 0;
           foreach($withdrawal as $index => $m){
                    
                        $cnt++;
                        
                        if($m->status==0){
                            $status="pending";
                        }else if($m->status==1){
                            $status="approved";
                        }else{
                            $status="rejected";
                        }


                        $output .= '
                                    <div class="live" style="color: rgb(21 44 72);"> ';


                        $output .= '<div class="headRow">
                                                <div class="column2" style="float:left; display:table-column; width:33% ; padding-top: 5px; padding-bottom: 5px;" align="left">Amount: '.round($m->amount,2).'</div>
                                                <div class="column2" style="float:left; display:table-column; width:33%; padding-top: 5px; padding-bottom: 5px;" align="left">Account Type: '.$m->accounttype.'</div>
                                                <div class="column2" style="float:left; display:table-column; width:33%; padding-top: 5px; padding-bottom: 5px;" align="left">Datetime: '.$m->created_at.'</div>
                                            </div>
                                            
                                    <div class="headRow">
                                                <div class="column2" style="float:left; display:table-column; width:33% ; padding-top: 5px; padding-bottom: 5px;" align="left">Account Name: '.$m->accountname.'</div>
                                                <div class="column2" style="float:left; display:table-column; width:33%; padding-top: 5px; padding-bottom: 5px;" align="left">Account Number: '.$m->accountnumber.'</div>
                                                <div class="column2" style="float:left; display:table-column; width:33%; padding-top: 5px; padding-bottom: 5px;" align="left">Status: '.$status.'</div>
                                            </div>

                                                <div style="display:table-row; padding-bottom: 5px;">
                                                <div class="column2" style="float:left; display:table-column; width:90%; padding-top: 5px; " align="left">Details: '.$m->details.' </div>
                                                </div>';


                        
                        $output .= '
                                        </div>
                             
                        ';
                    
                
            }

            if($cnt < 1){
                $output = '<div class="live" style="color: rgb(21 44 72); height: 100px; font-size: 30px;">No History</div>';
            }
        }
        else{
            $output = '<div class="live" style="color: rgb(21 44 72); height: 100px; font-size: 30px;">No History</div>';
        }
        return json_encode(array('contents'=>$output));
    }
    
    // Contact us
    public function page4()
    {
        //$translation = Config::get('app.locale');
        //$lang = Language::where('code', $translation)->first();

        $banner = Banner::where('pagetype', 3)->orderBy('id', 'ASC')->get();
        //$contact_title = About::where('link', 'contact')->where('lang_id', $lang->id)->first();
        //$contacts = About::where('link', 'contact')->where('lang_id', $lang->id)->first();
    $contact_title = About::where('link', 'contact')->first();
    $contacts = About::where('link', 'contact')->first();

        return view('contact', compact('contacts', 'contact_title','banner'));
    }

    public function page5()
    {
        
        $abouts = About::all();

        $about_datas = About::where('link', 'about')->first();

        return view('/about', compact('abouts','about_datas'));
    }
}
