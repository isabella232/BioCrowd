@extends('layout')

@section('extraheaders')
<script>
$(document).ready(function() {
	$("#leaderboardContentSelector").change(function() {
		window.location = this.value;
	});
});
</script>
@stop

@section('content')
	<div class="section group" id="mainsection">
		<div class="col span_6_of_8" id="main">
			<div class="leaderboardBackground">
				<div>
					{{Form::open(['url' => 'leaderboard'])}}
					{{Form::select('type', array('leaderboard' => 'Top 20 scores of all time', 'scoresday' => 'Top 20 scores of today', 'scoresweek' => 'Top 20 scores of the week', 'scoresmonth' => 'Top 20 scores of this month','20judge' => 'Top 20 #judgements','judgeday' => 'Top 20 #judgements of today','judgeweek' => 'Top 20 #judgements of the week','judgemonth' => 'Top 20 #judgements of the month'), Route::getCurrentRoute()->getPath(), ['id'=>'leaderboardContentSelector'])}}
					{{Form::close()}}
				</div>
				<div>
				<?php $userInfoIsOnPageAlready = false?>
					<table>
						<tr>
							<td>
							Rank
							</td>
							<td>
							Name
							</td>
							<td>
							Level
							</td>
							<td>
							Score
							</td>
						</tr>
						@if($rows != null)
							@foreach ($rows as $row)
								@if(is_object($row))
									@if(Auth::user()->check() && ($row->user_id == Auth::user()->get()->id))
										<tr style="background-color: yellow;">
										<?php $userInfoIsOnPageAlready = true?>
									@else
										<tr>
									@endif
										<td>
										{{$row->currentRank}}
										</td>
										<td>
										{{$row->name}}
										</td>
										<td>
										{{$row->level}}
										</td>
										<td>
										{{$row->score}}
										</td>
									</tr>
								@else
									@if(Auth::user()->check() && ($row['user_id'] == Auth::user()->get()->id))
										<tr style="background-color: yellow;">
										<?php $userInfoIsOnPageAlready = true?>
									@else
										<tr>
									@endif
									<td>
										{{$row['currentRank']}}
										</td>
										<td>
										{{$row['name']}}
										</td>
										<td>
										{{$row['level']}}
										</td>
										<td>
										{{$row['score']}}
										</td>
									</tr>
								@endif
							@endforeach
							@if(Auth::user()->check() && !$userInfoIsOnPageAlready && $userRank != '')
								<tr style="background-color: yellow;">
								<td>
									{{$userRank}}
									</td>
									<td>
									{{Auth::user()->get()->name}}
									</td>
									<td>
									{{Auth::user()->get()->level}}
									</td>
									<td>
									{{Auth::user()->get()->score}}
									</td>
								</tr>
							@endif
						@endif
						<!-- If the user is logged in, show the user's rank here if it's not in the top 20 already, If the user is in the top 20, highlight the user's row-->
						
					</table>
				</div>
			</div>
		</div>
		@if (Auth::user()->check())
		<!-- Begin sidebar -->
		@include('sidebar')
		<!-- End sidebar -->
		@endif
	</div>
@stop