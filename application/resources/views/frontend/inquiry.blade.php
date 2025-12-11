@extends('layouts.app')

@section('content')                
        
    <section class="banner_area">
    <div class="banner_inner d-flex align-items-center">
        <div class="container">
            <div class="banner_content">
                <div class="page_link">
                    <a href="{{ route('home') }}">Home</a>
                    <a href="{{ route('inquiry') }}">Inquiry</a>
                </div>
            </div>
        </div>
    </div>
    </section>
    <section class="welcome_area">
        <div class="container">
            <div class="welcome_text">
                <h4>Inquiry</h4>
            </div>
            <form class="form-horizontal" method="POST" action="{{ route('student.inquirysubmit') }}">
                {{ csrf_field() }}  
                         
                <div class="row">  
                    <div class="col-lg-6 col-sm-12">
                         <div class="advanced_search mb-4">
                           <div class="search_select">
                                <input type="text" class="s_select" name="fullname" placeholder="Your Name">
                                <input type="text" class="s_select" name="contact" placeholder="Contact No">
                                <input type="text" class="s_select" name="email" placeholder="Email">        
                                <input type="text" class="s_select" name="your_country" placeholder="Your Country">
                                <input type="text" class="s_select" name="city" placeholder="Your City">
                                <select name="course_level" id="course_level" class="s_select">
                                    <option value="">You are interested for a</option>
                                    <option value="Diploma">Diploma</option>
                                    <option value="Bachelor Degree">Bachelor Degree</option>
                                    <option value="Master’s Degree">Master’s Degree</option>
                                    <option value="PhD">PhD</option>
                                    <option value="Others">Others</option>
                                </select>
                                <input type="text" class="s_select" name="course" placeholder="Write the Course Name">
                                <select name="country" id="country" class="s_select">
                                    <option value="">Select a country</option>
                                    @foreach($countries as $country)
                                    <option value="{{ $country->id }}" @if($country->defaults == 1) selected @endif>{{ $country->name }}</option>
                                    @endforeach
                                </select>
                                <input type="text" class="s_select" name="university_name" placeholder="Write the university name">
                                <select name="contact_by" id="country" class="s_select">
                                    <option value="">How may we contact you?</option>
                                    <option value="Email">Email</option>
                                    <option value="WhatsApp">WhatsApp</option>
                                    <option value="Phone Call">Phone Call</option>
                                </select>
                                <input type="text" class="s_select" name="english_preficiency_score" placeholder="Any English Proficiency Score?">
                                <input type="text" class="s_select" name="study_gap" placeholder="Do you have study-gap ?">
                                <input type="text" class="s_select" name="last_qualification" placeholder="Your latest qualification">
                                <input type="text" class="s_select" name="result" placeholder="Result of Last Qualification">
                            </div>
                            <button type="submit" value="submit" class="btn submit_btn">Submit</button>
                        </div> 
                    </div>
                </div>            
            </form>  
        </div>
    </section>
@endsection
@section('footer')
    @parent
@endsection