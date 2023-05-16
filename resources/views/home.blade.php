@extends('layouts.layout')
@section('content')
 <div class="container">
        <div class="row align-items-start">
            <div class="col">
            <h1 class="mb-3">Bucket Form</h1>
            </div>
            <div class="col">
            <h1 class="mb-3">Ball Form</h1>
            </div>
        </div>
        <div class="row align-items-center">
            <div class="col">
                <form class="needs-validation">
                    <div class="form-row">
                        <div class="col-md-4 mb-3">
                            <label for="bucket_name">Bucket Name:</label>
                            <input type="text" class="form-control" id="bucket_name" placeholder="Bucket Name" value="" required />
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="bucket_volume">Bucket Volume  (in Inches):</label>
                            <input type="number" class="form-control" id="bucket_volume" placeholder="Bucket Volume" value="" min="0.01" step="1" />
                        </div>
                    </div>
                    <button class="btn btn-primary" type="button" id="btnBucketSave" data-type="bucket">Save</button>
                </form>
            </div>
            <div class="col">
                <form class="needs-validation">
                    <div class="form-row">
                        <div class="col-md-4 mb-3">
                            <label for="ball_name">Ball Name:</label>
                            <input type="text" class="form-control" id="ball_name" placeholder="Ball Name" value="" required />
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="ball_volume">Ball Volume  (in Inches):</label>
                            <input type="number" class="form-control" id="ball_volume" placeholder="Ball Volume" value="" min="0.01" step="1" />
                        </div>
                    </div>
                    <button class="btn btn-primary" type="button" id="btnBallSave" data-type="ball">Save</button>
                </form>
            </div>
            <div class="row align-items-start">
                <div class="col" id="suggest_ball">
                
                </div>
                <div class="col" id="display_suggest_result">
                </div>
            </div>
        </div>
    </div>
    <script>
	    $(document).ready(function(){
	        $('body').on('click', '#btnBallSave, #btnBucketSave', function() {
	            let type = $(this).data('type');
	            let name = $(`#${type}_name`).val();
	            let volume = $(`#${type}_volume`).val();
	            if($.trim(name) == ''){
	                alert(`please enter ${type} name`);
	                return;
	            }
	            if($.trim(volume) == '' ){
	                alert(`please enter ${type} volume`);
	                return;
	            } else if(volume<=0 ){
	                alert(`please enter ${type} volume greater zero `);
	                return;
	            }
	            $.ajax({
	            	headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					},
	                type: "PUT",
	                url: "buckets/save_form",
	                data: {type: type, name: name, volume:volume},
	                success: function(response){ 
	                    if(type== 'ball'){
	                        display_suggest_form(type);
	                    }
	                   alert('data saved');
	                }
	            });
	        });
	        $('body').on('click', '#btnSuggestBucket', function(){
	            let ball_id =[];
	            let ball_name =[];
	            let ball_q =[];
	            let ball_volume =[];
	            let error = 0;
	            $.each($('.txtBall'), function(idx, value){
	                if($(value).val()>0){
	                    ball_id.push($(value).data('id')); 
	                    ball_name.push($(value).data('name')); 
	                    ball_q.push($(value).val()); 
	                    ball_volume.push($(value).data('volume')); 
	                } else {
	                    alert('No of ball is greater than zero ');
	                    error = 1;
	                }
	            });
	            if(error ==0){
	                $.ajax({
	                	headers: {
							'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
						},
	                    type: "PUT",
	                    url: "buckets/save_form",
	                    data: {type: 'suggestion', name: ball_name, ball_q:ball_q, ball_id:ball_id, ball_volume:ball_volume},
	                    success: function(response){ 
	                        display_suggest_result(response);
	                    }
	                });
	            }
	        });
	        display_suggest_form('ball');
	        display_suggest_result(0);
	    });
	    function display_suggest_form(type){
	        $.ajax({
	        	headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
	            type: "GET",
	            url: "buckets/get_ball",
	            data: {type: type},
	            success: function(response){ 
	                if(response !='[]'){
	                    suggest_form_html(response);
	                }	               
	            }
	        });
	    }
	    function suggest_form_html(data){
	        let html = `<div class="container"><div class="row"><div class="col-sm"><h1 class="mb-3">Bucket Suggestion</h1></div></div>`;
	        $.each(data, function(key,value) {
	            html += `<div class="row"><div class="col-sm">${value.name}</div><div class="col-sm"><input type="number" min="1" step="1" class="txtBall form-control" id="ball_${value.id}" data-volume="${value.volume}" data-id="${value.id}" data-name="${value.name}" /></div></div>`;
	        });
	        html += `<div class="row"><div class="col-sm"><button class="btn btn-primary" type="button" id="btnSuggestBucket" data-type="ball">SUGGEST ME BUCKETS</button></div></div></div>`;
	        $('#suggest_ball').html(html);
	    }

	    function display_suggest_result(data){
	        if(data==1){
	            alert('Space is not available');
	        } else {
	        	$.ajax({
	                	headers: {
							'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
						},
	                    type: "GET",
	                    url: "buckets/get_ball",
	                    data: {type: 'suggestion'},
	                    success: function(response){ 
	                        let html = `<div class="container"><div class="row"><div class="col-sm"><h1 class="mb-3">RESULT</h1></div></div><div class="row"><div class="col-sm"><h3 class="mb-3">Following are the suggested buckets:</h3></div></div><div class="row"><div class="col-sm">BUCKET</div><div class="col-sm">BUCKET VOLUME</div><div class="col-sm">BALL</div><div class="col-sm">TOTAL BALLS IN BUCKET</div></div>`;
					        $.each(response, function(key,value) {
					            html += `<div class="row"><div class="col-sm">${value.name}</div><div class="col-sm">${value.volume}</div><div class="col-sm">${value.ball_name}</div><div class="col-sm">${value.no_of_ball}</div></div>`; 
					        });
					        html += `</div>`;
					        $('#display_suggest_result').html(html);
	                    }
	                });
	        }
	    }
	</script>
@endsection
