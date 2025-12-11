@extends('layouts.app')

@section('title', $seotitles)

@section('sidebar')
    @parent
@endsection
<style>
	.sitemapclass{
		width:100%;
		height:auto;	
	}
	.sitemapclass ul{
		width:100%;
		height:auto;
	}
	.sitemapclass ul li{
		width:100%;
		height:auto;
		display:block;
	}
	.sitemapclass ul li a{
		width:100%;
		height:auto;
		float:left;
		text-decoration:none;
	}
</style>
@section('content')        
      <div id="page" class="page">
        <div class="content-wrapper-inner"> 
          <div class="content-section">
            <div class="container sitemapclass">
              <div class="row">
              		
     				 <div class="col-sm-12">
                        <h3>Menus</h3>
                        	<ul>
                           @foreach($menus as $wm)
                               <li><a href="{{ route('content',$wm->uri)}}">&raquo; {{ $wm->title }}</a></li>
                                    <?php $smenus = App\Models\Menu::where('parent_id', $wm->id)->get(); ?>
                                    <ul>
                                        @foreach($smenus as $sm)
                                            @if($sm->type=='url')
                                                <li style="display:block"><a href="{{ $sm->urls }}" style="color:#333; font-size:13px;">&raquo; {{ $sm->title }}</a></li>
                                            @elseif($sm->type=='faq')
                                                <li style="display:block"><a href="{{ route('faq') }}" style="color:#333; font-size:13px;">&raquo; {{ $sm->title }}</a></li>
                                            @else
                                                <li style="display:block"><a href="{{ route('article', [$sm->uri] ) }}" style="color:#333; font-size:13px;">&raquo; {{ $sm->title }}</a></li>
                                            @endif
                                        @endforeach
                                    </ul>
                         @endforeach
                      </ul>
                 </div>
                 	 <div class="col-sm-12">
                            <h3>Banner</h3>
                            <ul>
                                @foreach($banners as $banner)                                
                                  <li><a href="#">&raquo; {{ $banner->name }}</a></li>
                                @endforeach
                            </ul>
                     </div>
                	 <div class="col-sm-12">
                            <h3>Our Services</h3>
                            <ul>
                                @foreach($services as $serv)                                
                                  <li><a href="{{ route('service.details',[$serv->url]) }}">&raquo; {{ $serv->name }}</a></li>
                                @endforeach
                            </ul>
                     </div>
                     <div class="col-sm-12">
                            <h3>News</h3>
                            <ul>
                                @foreach($newsevents as $news)                                
                                  <li><a href="{{ route('news.details',[$news->url]) }}">&raquo; {{ $news->name }}</a></li>
                                @endforeach
                            </ul>
                     </div>
                     <div class="col-sm-12">
                            <h3>Events</h3>
                            <ul>
                                @foreach($allevents as $event)                                
                                  <li><a href="{{ route('event.details',[$serv->url]) }}">&raquo; {{ $event->name }}</a></li>
                                @endforeach
                            </ul>
                     </div>
              </div>
            </div>
          </div>
       </div>
       </div>             
@endsection
@section('footer')
    @parent
    
         <script src="{{ asset('assets/front/js/vendor.js')}}"></script>
	     <script src="{{ asset('assets/front/js/framework.js')}}"></script>
         <script src="{{ asset('assets/front/js/template.js')}}"></script>
@endsection