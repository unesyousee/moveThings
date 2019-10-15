<?php
namespace App\Http\Controllers;
use Carbon\Carbon;
use App\Order;
use App\Comment;
use App\User;
use App\CarrierUser;
use App\Address;
use Illuminate\Http\Request;

class dasboardController extends Controller
{
    public function index(Request $request){

        //region chart
        $regions = ['منطقه ۱', 'منطقه ۲','منطقه ۳','منطقه ۴','منطقه ۵','منطقه ۶','منطقه ۷','منطقه ۸',
        'منطقه ۹','منطقه ۱۰','منطقه ۱۱','منطقه ۱۲','منطقه ۱۳','منطقه ۱۴','منطقه ۱۵','منطقه ۱۶','منطقه ۱۷',
        'منطقه ۱۸','منطقه ۱۹','منطقه ۲۰','منطقه ۲۱','منطقه ۲۲'];
        $areas = [];
        foreach ($regions as $key => $value) {
            
            $address = Address::where('region', '!=', null)->where("region", $value)->count();
            $areas[$value] = $address;
        }

        // last day orders

        $doneInToDay=Order::whereDate('created_at', gmdate('Y-m-d', time()))->where('status', 6)->count();
        $newInToDay=Order::whereDate('created_at', gmdate('Y-m-d', time()))->where('status', 0)->count();
        $canceledInToDay=Order::whereDate('created_at', gmdate('Y-m-d', time()))->where('status', 7)->count();


        
        // last month orders
     $from = 0;
     $to = -86400;
     $lastMonth = [];
  for ($i=0; $i < 31 ; $i++) {

         $order = Order::whereDate('created_at', gmdate('Y-m-d', time()-$from))->where(function($q){
            $q->where('status', 5 );
            $q->orWhere('status', 6 );
         })->count();
         $lastMonth[$i] = [$order , gmdate('Y-m-d', time()-$from)];
         $to += 86400;
         $from += 86400;
  }

// filtered orders 
    if (!$request->has('start_time')) {
        $numDays = 30 ;
        $Day= time();
     
    }else{

        $start_time = Carbon::parse(gmdate('Y-m-d', $request->start_time/1000));
        $end_time = Carbon::parse(gmdate('Y-m-d', $request->end_time/1000));
        $numDays = $end_time->diffInDays($start_time);
        $Day= $request->start_time/1000;
    }
/*dump($start_time);
dd($end_time);*/

$df = $Day;
        $newOnfilteredDate = [];
             for ($i = 0 ; $i <= $numDays ; $i++) {
                 $order = Order::where(function($query) use ($df) {
                   $query->whereDate('updated_at',gmdate('Y-m-d', $df))
                   ->where('status', 0);
                 })->count();
                 $newOnfilteredDate[$i] = [$order, gmdate('Y-m-d', $df)];
                 $df -= 86400;
             }



$df = $Day;
        $doneOnfilteredDate = [];
             for ($i = 0 ; $i <= $numDays ; $i++) {
                 $order = Order::where(function($query) use ($df) {
                   $query->whereDate('updated_at',gmdate('Y-m-d', $df))
                   ->where('status', 6);
                 })->count();
                 $doneOnfilteredDate[$i] = [$order, gmdate('Y-m-d', $df)];
                 $df -= 86400;
             }


$df = $Day;
        $canceleOnfilteredDate = [];
             for ($i = 0 ; $i <= $numDays ; $i++) {
                 $order = Order::where(function($query) use ($df) {
                   $query->whereDate('updated_at',gmdate('Y-m-d', $df))
                   ->where('status', 7);
                 })->count();
                 $canceleOnfilteredDate[$i] = [$order, gmdate('Y-m-d', $df)];
                 $df -= 86400;
             }


// Comments chart
             
             $comment1 = Comment::whereBetween('rating', ['1', '2'])->count();
             $comment2 = Comment::whereBetween('rating', ['2', '3'])->count();
             $comment3 = Comment::whereBetween('rating', ['3', '4'])->count();
             $comment4 = Comment::whereBetween('rating', ['4', '5'])->count();
             $comment5 = Comment::whereBetween('rating', ['5', '6'])->count();
             $Comments = [$comment1, $comment2, $comment3, $comment4, $comment5];
             $commentsAverage = Comment::avg('rating');

            $newCounter = Order::where('status', '1')->orWhere('status', '2')->orWhere('status', '3')->count();
            $userCount = User::count();
            $orderForToday = Order::whereDate('moving_time',gmdate('Y-m-d', time() ))->where(function($q){
                $q->orWhere('status', '=', '2');
                $q->orWhere('status', '=', '3');
                $q->orWhere('status', '=', '1');
            })->count();
            $address = Order::where('status',6)->orderBy('updated_at', 'DESC')->paginate(50);
$lastComments = Comment::orderBy('id', 'desc')->take(15)->get();
     return view('admin.dashboard', compact(
        'lastMonth','doneInToDay', 'newInToDay','canceledInToDay',
         'canceleOnfilteredDate', 'newOnfilteredDate', 'doneOnfilteredDate',
          'Comments', 'newCounter','userCount',
          'commentsAverage', 'orderForToday', 'address',
          'lastComments','areas'
      ));
    }
}

